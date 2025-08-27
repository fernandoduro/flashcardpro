<?php

use App\Livewire\Decks\Show as DeckShow;
use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('the deck show page can be rendered', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    actingAs($user)->get(route('decks.show', $deck))->assertOk();
});

test('a user cannot view a deck they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $deck = Deck::factory()->for($otherUser)->create();

    actingAs($user)->get(route('decks.show', $deck))->assertForbidden();
});

test('can remove a card from a deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    // Create a card that belongs to this deck
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    actingAs($user);

    // Pass a fresh instance of the deck to ensure the relationship is loaded
    Livewire::test(DeckShow::class, ['deck' => $deck->fresh()])
        ->call('deleteCard', $card->id);

    $this->assertDatabaseMissing('cards', ['id' => $card->id]);
});
