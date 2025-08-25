<?php

namespace Tests\Feature\Api;

use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use function Pest\Laravel\{actingAs, post, getJson};

test('a user can start a study session for their own deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    actingAs($user)
        ->post('/api/studies', ['deck_id' => $deck->id])
        ->assertStatus(201)
        ->assertJsonStructure(['study_id']);
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
        ->getJson("/api/decks/{$deck->id}/cards")
        ->assertStatus(200)
        ->assertJsonCount(5, 'data');
});