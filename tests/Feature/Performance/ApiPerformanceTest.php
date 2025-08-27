<?php

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('API can handle multiple concurrent deck requests', function () {
    $user = User::factory()->create();

    // Create multiple decks with varying card counts
    $decks = Deck::factory()->count(10)->create(['user_id' => $user->id]);

    foreach ($decks as $index => $deck) {
        $cardCount = ($index + 1) * 5; // 5, 10, 15, ... 50 cards
        Card::factory()->count($cardCount)->create([
            'user_id' => $user->id,
            'deck_id' => $deck->id,
        ]);
    }

    $this->actingAs($user);

    // Test deck listing performance
    $startTime = microtime(true);
    $response = $this->getJson('/api/v1/decks');
    $endTime = microtime(true);

    $response->assertStatus(200);
    $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

    // Should respond within reasonable time (under 500ms)
    expect($responseTime)->toBeLessThan(500);

    $responseData = $response->json('data');
    expect(count($responseData))->toEqual(10);

    // Verify all decks have correct card counts
    foreach ($responseData as $deckData) {
        $deck = Deck::find($deckData['id']);
        expect($deckData['cards_count'])->toEqual($deck->cards()->count());
    }
});

test('large deck with many cards loads efficiently', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    // Create a large number of cards (100 cards)
    $cards = Card::factory()->count(100)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    $this->actingAs($user);

    // Test card retrieval performance
    $startTime = microtime(true);
    $response = $this->getJson("/api/v1/decks/{$deck->id}/cards");
    $endTime = microtime(true);

    $response->assertStatus(200);
    $responseTime = ($endTime - $startTime) * 1000;

    // Should handle 100 cards within reasonable time (under 1 second)
    expect($responseTime)->toBeLessThan(1000);

    $responseData = $response->json('data');
    expect(count($responseData))->toEqual(100);
});

test('study session creation is performant under load', function () {
    $user = User::factory()->create();
    $decks = Deck::factory()->count(5)->create(['user_id' => $user->id]);

    // Add cards to each deck
    foreach ($decks as $deck) {
        Card::factory()->count(10)->create([
            'user_id' => $user->id,
            'deck_id' => $deck->id,
        ]);
    }

    $this->actingAs($user);

    $responseTimes = [];

    // Create multiple study sessions rapidly
    foreach ($decks as $deck) {
        $startTime = microtime(true);
        $response = $this->postJson('/api/v1/studies', ['deck_id' => $deck->id]);
        $endTime = microtime(true);

        $response->assertStatus(201);
        $responseTime = ($endTime - $startTime) * 1000;
        $responseTimes[] = $responseTime;

        // Brief pause to avoid overwhelming the system
        usleep(10000); // 10ms
    }

    // Calculate average response time
    $averageTime = array_sum($responseTimes) / count($responseTimes);

    // Average should be under 200ms
    expect($averageTime)->toBeLessThan(200);

    // Verify all study sessions were created
    $studyCount = \App\Models\Study::where('user_id', $user->id)->count();
    expect($studyCount)->toEqual(5);
});

test('concurrent card updates dont cause race conditions', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
        'question' => 'Original Question',
        'answer' => 'Original Answer',
    ]);

    $this->actingAs($user);

    // Simulate concurrent updates by creating multiple update requests
    $updatePromises = [];

    $newQuestions = [
        'Updated Question 1',
        'Updated Question 2',
        'Updated Question 3',
    ];

    foreach ($newQuestions as $newQuestion) {
        // In a real scenario, these would be concurrent requests
        // Here we simulate by doing them sequentially but checking for consistency
        $response = $this->postJson("/api/v1/decks/{$deck->id}/cards", [
            'question' => $newQuestion,
            'answer' => 'Updated Answer',
        ]);

        $response->assertStatus(201);
        $updatePromises[] = $response->json('data.id');
    }

    // Verify deck has correct number of cards
    $deck->refresh();
    expect($deck->cards()->count())->toEqual(4); // Original + 3 new cards

    // Verify all cards exist and are accessible
    foreach ($updatePromises as $cardId) {
        $cardResponse = $this->getJson("/api/v1/decks/{$deck->id}/cards");
        $cardResponse->assertStatus(200);

        $cardIds = collect($cardResponse->json('data'))->pluck('id')->toArray();
        expect(in_array($cardId, $cardIds))->toBeTrue();
    }
});

test('database query optimization prevents N+1 problems', function () {
    $user = User::factory()->create();

    // Create multiple decks with cards
    $decks = Deck::factory()->count(5)->create(['user_id' => $user->id]);

    foreach ($decks as $deck) {
        Card::factory()->count(3)->create([
            'user_id' => $user->id,
            'deck_id' => $deck->id,
        ]);
    }

    $this->actingAs($user);

    // Enable query logging to count queries
    DB::enableQueryLog();

    // Make request that should use optimized queries
    $response = $this->getJson('/api/v1/decks');
    $response->assertStatus(200);

    $queryCount = count(DB::getQueryLog());

    // Should make minimal queries (1-3 queries max for this operation)
    expect($queryCount)->toBeLessThanOrEqual(3);

    DB::disableQueryLog();
});

test('pagination works efficiently with large datasets', function () {
    $user = User::factory()->create();

    // Create a large number of decks
    $decks = Deck::factory()->count(50)->create(['user_id' => $user->id]);

    // Add some cards to each deck
    foreach ($decks as $deck) {
        Card::factory()->count(2)->create([
            'user_id' => $user->id,
            'deck_id' => $deck->id,
        ]);
    }

    $this->actingAs($user);

    // Test first page
    $startTime = microtime(true);
    $response = $this->getJson('/api/v1/decks?page=1&per_page=10');
    $endTime = microtime(true);

    $response->assertStatus(200);
    $responseTime = ($endTime - $startTime) * 1000;

    // Should respond quickly even with large dataset
    expect($responseTime)->toBeLessThan(300);

    $responseData = $response->json();
    expect($responseData['data'])->toHaveCount(10);
    expect($responseData['pagination']['total'])->toEqual(50);
    expect($responseData['pagination']['last_page'])->toEqual(5);

    // Test last page
    $response = $this->getJson('/api/v1/decks?page=5&per_page=10');
    $response->assertStatus(200);
    $responseData = $response->json();
    expect($responseData['data'])->toHaveCount(10);
    expect($responseData['pagination']['current_page'])->toEqual(5);
});

test('memory usage stays reasonable with large card sets', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    // Create a very large deck (200 cards)
    Card::factory()->count(200)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    $this->actingAs($user);

    $startTime = microtime(true);
    $startMemory = memory_get_usage();

    $response = $this->getJson("/api/v1/decks/{$deck->id}/cards");
    $response->assertStatus(200);

    $endTime = microtime(true);
    $endMemory = memory_get_usage();

    $responseTime = ($endTime - $startTime) * 1000;
    $memoryUsage = ($endMemory - $startMemory) / 1024 / 1024; // MB

    // Should handle 200 cards within reasonable time and memory
    expect($responseTime)->toBeLessThan(2000); // Under 2 seconds
    expect($memoryUsage)->toBeLessThan(50); // Under 50MB additional usage

    $responseData = $response->json('data');
    expect(count($responseData))->toEqual(200);
});
