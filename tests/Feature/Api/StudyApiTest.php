<?php

namespace Tests\Feature\Api;

use App\Models\Card;
use App\Models\Deck;
use App\Models\Study;
use App\Models\StudyResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\patch;
use function Pest\Laravel\getJson;

/**
 * StudyApiTest - Tests for study session API endpoints
 *
 * This test suite covers:
 * - Study session creation
 * - Study session completion
 * - Study result recording
 * - Card retrieval for study sessions
 * - Response format validation
 * - Authorization checks
 * - Error handling
 */
test('a user can start a study session for their own deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    actingAs($user)
        ->post('/api/v1/studies', ['deck_id' => $deck->id])
        ->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['study_id'],
            'api_version',
            'timestamp',
        ]);
});

test('a user cannot start a study session for another users deck', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck = Deck::factory()->for($user1)->create();

    actingAs($user2)
        ->post('/api/v1/studies', ['deck_id' => $deck->id])
        ->assertStatus(302); // Laravel validation redirect for invalid data
});

test('study session creation requires deck_id', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/api/v1/studies', [])
        ->assertStatus(302) // Validation redirect
        ->assertSessionHasErrors(['deck_id']);
});

test('study session creation requires existing deck', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/api/v1/studies', ['deck_id' => 999])
        ->assertStatus(302) // Validation redirect
        ->assertSessionHasErrors(['deck_id']);
});

test('a user can complete their own study session', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create([
        'completed_at' => null,
        'created_at' => now()->subMinutes(30),
    ]);

    actingAs($user)
        ->patch("/api/v1/studies/{$study->id}/complete")
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Study session completed successfully',
        ]);

    $study->refresh();
    expect($study->completed_at)->not->toBeNull();
});

test('a user cannot complete another users study session', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck = Deck::factory()->for($user1)->create();
    $study = Study::factory()->for($user1)->for($deck)->create();

    actingAs($user2)
        ->patch("/api/v1/studies/{$study->id}/complete")
        ->assertStatus(403);
});

test('a user cannot complete an already completed study session', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create([
        'completed_at' => now(),
    ]);

    actingAs($user)
        ->patch("/api/v1/studies/{$study->id}/complete")
        ->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Study session is already completed',
        ]);
});

test('a user cannot complete an expired study session', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create([
        'created_at' => now()->subHours(25), // More than 24 hours ago
    ]);

    actingAs($user)
        ->patch("/api/v1/studies/{$study->id}/complete")
        ->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Study session has expired',
        ]);
});

test('a user can record a study result', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    actingAs($user)
        ->post('/api/v1/study-results', [
            'study_id' => $study->id,
            'card_id' => $card->id,
            'is_correct' => true,
        ])
        ->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'Study result recorded successfully',
        ]);

    $this->assertDatabaseHas('study_results', [
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => true,
    ]);
});

test('a user cannot record duplicate study results', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    // First result
    StudyResult::create([
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => true,
    ]);

    // Try to record the same result again
    actingAs($user)
        ->post('/api/v1/study-results', [
            'study_id' => $study->id,
            'card_id' => $card->id,
            'is_correct' => false,
        ])
        ->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Study result already exists for this card',
        ]);
});

test('a user cannot record study results for another users study', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck = Deck::factory()->for($user1)->create();
    $study = Study::factory()->for($user1)->for($deck)->create();
    $card = Card::factory()->for($user1)->for($deck)->create();

    actingAs($user2)
        ->post('/api/v1/study-results', [
            'study_id' => $study->id,
            'card_id' => $card->id,
            'is_correct' => true,
        ])
        ->assertStatus(302) // Validation redirect for invalid ownership
        ->assertSessionHasErrors(['study_id', 'card_id']);
});

test('a user cannot record study results for another users card', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck1 = Deck::factory()->for($user1)->create();
    $deck2 = Deck::factory()->for($user2)->create();
    $study = Study::factory()->for($user1)->for($deck1)->create();
    $card = Card::factory()->for($user2)->for($deck2)->create();

    actingAs($user1)
        ->post('/api/v1/study-results', [
            'study_id' => $study->id,
            'card_id' => $card->id,
            'is_correct' => true,
        ])
        ->assertStatus(302) // Validation redirect for invalid card ownership
        ->assertSessionHasErrors(['card_id']);
});

test('study result recording requires all fields', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/api/v1/study-results', [])
        ->assertStatus(302) // Validation redirect
        ->assertSessionHasErrors(['study_id', 'card_id', 'is_correct']);
});

test('study result recording requires valid study_id', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    actingAs($user)
        ->post('/api/v1/study-results', [
            'study_id' => 999,
            'card_id' => $card->id,
            'is_correct' => true,
        ])
        ->assertStatus(302) // Validation redirect for non-existent study
        ->assertSessionHasErrors(['study_id']);
});

test('study result recording requires valid card_id', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();

    actingAs($user)
        ->post('/api/v1/study-results', [
            'study_id' => $study->id,
            'card_id' => 999,
            'is_correct' => true,
        ])
        ->assertStatus(302) // Validation redirect for non-existent card
        ->assertSessionHasErrors(['card_id']);
});

test('study result recording requires boolean is_correct', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    actingAs($user)
        ->post('/api/v1/study-results', [
            'study_id' => $study->id,
            'card_id' => $card->id,
            'is_correct' => 'not_boolean',
        ])
        ->assertStatus(302) // Validation redirect for invalid boolean
        ->assertSessionHasErrors(['is_correct']);
});

test('study result recording handles database errors gracefully', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    // Create a duplicate result first to trigger the database error scenario
    StudyResult::create([
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => false,
    ]);

    // Now try to create another result for the same study/card
    actingAs($user)
        ->post('/api/v1/study-results', [
            'study_id' => $study->id,
            'card_id' => $card->id,
            'is_correct' => true,
        ])
        ->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Study result already exists for this card',
        ]);
});

test('a user can fetch shuffled cards for their own deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    // Create 5 cards that belong to this specific user and deck
    Card::factory(5)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    actingAs($user)
        ->getJson("/api/v1/decks/{$deck->id}/cards")
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'api_version',
            'timestamp',
        ])
        ->assertJsonCount(5, 'data');
});

test('a user cannot fetch cards from another users deck', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck = Deck::factory()->for($user1)->create();

    actingAs($user2)
        ->getJson("/api/v1/decks/{$deck->id}/cards")
        ->assertStatus(403);
});

test('study endpoints require authentication', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $study = Study::factory()->for($user)->for($deck)->create();
    $card = Card::factory()->for($user)->for($deck)->create();

    // Test with Accept: application/json header to ensure JSON responses
    $headers = ['Accept' => 'application/json'];

    // Test study creation without auth
    post('/api/v1/studies', ['deck_id' => $deck->id], $headers)
        ->assertStatus(401);

    // Test study completion without auth
    patch("/api/v1/studies/{$study->id}/complete", [], $headers)
        ->assertStatus(401);

    // Test study results recording without auth
    post('/api/v1/study-results', [
        'study_id' => $study->id,
        'card_id' => $card->id,
        'is_correct' => true,
    ], $headers)->assertStatus(401);

    // Test deck cards fetching without auth
    getJson("/api/v1/decks/{$deck->id}/cards")
        ->assertStatus(401);
});
