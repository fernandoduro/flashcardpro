<?php

use App\Livewire\Decks\Index as DeckIndex;
use App\Models\Deck;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('the component renders successfully and displays decks', function () {
    $deck = Deck::factory()->for($this->user)->create();

    Livewire::test(DeckIndex::class)
        ->assertOk()
        ->assertSee($deck->name);
});

test('does not display decks owned by other users', function () {
    $otherUserDeck = Deck::factory()->for(User::factory())->create();
    Livewire::test(DeckIndex::class)->assertDontSee($otherUserDeck->name);
});

test('can pin a deck', function () {
    $deck = Deck::factory()->for($this->user)->create(['is_pinned' => false]);

    Livewire::test(DeckIndex::class)
        ->call('togglePin', $deck->id);

    assertDatabaseHas('decks', [
        'id' => $deck->id,
        'is_pinned' => true,
    ]);
});

test('pinned decks appear first', function () {
    $deckA = Deck::factory()->for($this->user)->create(['name' => 'Deck A', 'is_pinned' => false]);
    $deckB = Deck::factory()->for($this->user)->create(['name' => 'Deck B', 'is_pinned' => true]);

    Livewire::test(DeckIndex::class)
        ->assertSeeInOrder(['Deck B', 'Deck A']);
});

test('can delete a deck', function () {
    $deck = Deck::factory()->for($this->user)->create();

    Livewire::test(DeckIndex::class)
        ->call('deleteDeck', $deck->id);

    assertDatabaseMissing('decks', ['id' => $deck->id]);
});

test('a user cannot delete a deck they do not own', function () {
    $otherUserDeck = Deck::factory()->for(User::factory())->create();
    Livewire::test(DeckIndex::class)
        ->call('deleteDeck', $otherUserDeck->id)
        ->assertForbidden();
});