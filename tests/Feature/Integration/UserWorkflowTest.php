<?php

use App\Models\User;
use App\Models\Deck;
use App\Models\Card;
use App\Models\Study;
use App\Models\StudyResult;
use Laravel\Sanctum\Sanctum;

test('complete user workflow from registration to study completion', function () {
    // 1. User registration and login
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com'
    ]);

    $this->actingAs($user);

    // 2. Create a deck
    $deck = Deck::factory()->create([
        'user_id' => $user->id,
        'name' => 'Integration Test Deck',
        'public' => false
    ]);

    expect($deck->fresh())->not->toBeNull();
    expect($deck->name)->toEqual('Integration Test Deck');

    // 3. Add cards to the deck
    $cards = Card::factory()->count(3)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id
    ]);

    expect($cards)->toHaveCount(3);
    expect($deck->fresh()->cards)->toHaveCount(3);

    // 4. Start a study session
    $study = Study::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
        'completed_at' => null
    ]);

    expect($study->fresh())->not->toBeNull();
    expect($study->completed_at)->toBeNull();

    // 5. Record study results for each card
    $correctAnswers = 0;
    foreach ($cards as $index => $card) {
        $isCorrect = $index % 2 === 0; // Alternate correct/incorrect for testing
        if ($isCorrect) {
            $correctAnswers++;
        }

        StudyResult::factory()->create([
            'study_id' => $study->id,
            'card_id' => $card->id,
            'is_correct' => $isCorrect
        ]);
    }

    // Verify study results were recorded
    expect($study->fresh()->results)->toHaveCount(3);

    // 6. Complete the study session
    $study->update(['completed_at' => now()]);

    expect($study->fresh()->completed_at)->not->toBeNull();

    // 7. Verify statistics are calculated correctly
    $totalResults = StudyResult::whereHas('study', fn($q) => $q->where('user_id', $user->id))->count();
    $correctResults = StudyResult::whereHas('study', fn($q) => $q->where('user_id', $user->id))
                                 ->where('is_correct', true)
                                 ->count();

    expect($totalResults)->toBeGreaterThanOrEqual(3);
    expect($correctResults)->toEqual($correctAnswers);

    // 8. Test deck ownership and authorization
    expect($deck->fresh()->user_id)->toEqual($user->id);

    // 9. Verify cascade deletion would work (soft test)
    $cardCountBefore = $deck->fresh()->cards()->count();
    expect($cardCountBefore)->toEqual(3);

    // 10. Test that user can access their own data via API endpoints
    $response = $this->actingAs($user)
                     ->getJson("/api/v1/decks/{$deck->id}/cards");

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'message',
                 'data',
                 'api_version',
                 'timestamp'
             ])
             ->assertJsonCount(3, 'data');
});

test('user cannot access other users data', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $deck = Deck::factory()->create(['user_id' => $user1->id]);
    Card::factory()->create(['user_id' => $user1->id, 'deck_id' => $deck->id]);

    Sanctum::actingAs($user2, ['*']);

    // Try to access another user's deck
    $response = $this->getJson("/api/v1/decks/{$deck->id}/cards");

    $response->assertStatus(403);
});

test('study session expiration works correctly', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    // Create an old study session (more than 24 hours ago)
    $oldStudy = Study::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
        'created_at' => now()->subDays(2),
        'completed_at' => null
    ]);

    // Attempt to complete the expired study session
    $response = $this->patchJson("/api/v1/studies/{$oldStudy->id}/complete");

    $response->assertStatus(422)
             ->assertJson([
                 'success' => false,
                 'message' => 'Study session has expired'
             ]);
});

test('concurrent study sessions are handled properly', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    Card::factory()->count(2)->create(['user_id' => $user->id, 'deck_id' => $deck->id]);

    $this->actingAs($user);

    // Create two concurrent study sessions
    $study1 = Study::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
        'completed_at' => null
    ]);

    $study2 = Study::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
        'completed_at' => null
    ]);

    // Both should exist independently
    expect(Study::where('user_id', $user->id)->whereNull('completed_at')->count())->toEqual(2);

    // Complete first study
    $study1->update(['completed_at' => now()]);

    // Second study should still be active
    expect($study2->fresh()->completed_at)->toBeNull();

    // Complete second study
    $study2->update(['completed_at' => now()]);

    // Both should now be completed
    expect(Study::where('user_id', $user->id)->whereNotNull('completed_at')->count())->toEqual(2);
});

test('deck statistics are calculated correctly', function () {
    $user = User::factory()->create();

    // Create multiple decks
    $deck1 = Deck::factory()->create(['user_id' => $user->id, 'name' => 'Deck 1']);
    $deck2 = Deck::factory()->create(['user_id' => $user->id, 'name' => 'Deck 2']);

    // Add different numbers of cards to each deck
    Card::factory()->count(5)->create(['user_id' => $user->id, 'deck_id' => $deck1->id]);
    Card::factory()->count(3)->create(['user_id' => $user->id, 'deck_id' => $deck2->id]);

    $this->actingAs($user);

    // Test API response includes correct card counts
    $response = $this->getJson('/api/v1/decks');

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'success',
                 'data' => [
                     '*' => [
                         'id',
                         'name',
                         'cards_count'
                     ]
                 ]
             ]);

    $responseData = $response->json('data');

    // Find the decks in response and verify card counts
    $deck1Data = collect($responseData)->firstWhere('id', $deck1->id);
    $deck2Data = collect($responseData)->firstWhere('id', $deck2->id);

    expect($deck1Data['cards_count'])->toEqual(5);
    expect($deck2Data['cards_count'])->toEqual(3);
});
