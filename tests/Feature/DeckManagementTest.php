<?php

use App\Models\Deck;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('an authenticated user can view their own decks', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    actingAs($user)
        ->get(route('decks.index'))
        ->assertStatus(200)
        ->assertSee($deck->name);
});

test('a user cannot view another user\'s decks page', function () {
    $user1 = User::factory()->create();
    $deckForUser1 = Deck::factory()->for($user1)->create();

    $user2 = User::factory()->create();

    actingAs($user2)
        ->get(route('decks.index'))
        ->assertStatus(200)
        ->assertDontSee($deckForUser1->name);
});

test('a user cannot view a specific deck of another user', function () {
    $user1 = User::factory()->create();
    $deck = Deck::factory()->for($user1)->create();
    $user2 = User::factory()->create();

    actingAs($user2)
        ->get(route('decks.show', $deck))
        ->assertStatus(403);
});
