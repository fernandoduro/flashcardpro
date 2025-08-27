<?php

namespace Tests\Feature\Api;

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;

use function Pest\Laravel\actingAs;

/**
 * StudyApiTest - Tests for study session API endpoints
 *
 * This test suite covers:
 * - Study session creation
 * - Card retrieval for study sessions
 * - Response format validation
 * - Authorization checks
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
