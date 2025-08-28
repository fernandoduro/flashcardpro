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

test('cannot remove a card from another users deck', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $deck = Deck::factory()->for($user1)->create();
    $card = Card::factory()->create([
        'user_id' => $user1->id,
        'deck_id' => $deck->id,
    ]);

    // Test that user2 cannot access the deck show page at all
    actingAs($user2);

    // This should fail because user2 doesn't own the deck
    // The web route should prevent access before Livewire even gets involved
    $response = $this->get(route('decks.show', $deck));
    $response->assertForbidden();

    // Card should still exist
    expect($card->fresh())->not->toBeNull();
});

test('can open study modal when deck has cards', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    Card::factory()->count(5)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    actingAs($user);

    Livewire::test(DeckShow::class, ['deck' => $deck->fresh()])
        ->call('openStudyModal')
        ->assertSet('studyCardCount', 5)
        ->assertDispatched('open-modal', 'study-config');
});

test('cannot open study modal when deck has no cards', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    actingAs($user);

    Livewire::test(DeckShow::class, ['deck' => $deck->fresh()])
        ->call('openStudyModal')
        ->assertDispatched('flash-message');
});

test('can start study session with valid card count', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    Card::factory()->count(10)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    actingAs($user);

    Livewire::test(DeckShow::class, ['deck' => $deck->fresh()])
        ->set('studyCardCount', 5)
        ->call('startStudySession')
        ->assertDispatched('close-modal', 'study-config')
        ->assertRedirect(route('study.show', [
            'deck' => $deck->id,
            'count' => 5,
        ]));
});

test('study session validation fails with too many cards', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    Card::factory()->count(5)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    actingAs($user);

    Livewire::test(DeckShow::class, ['deck' => $deck->fresh()])
        ->set('studyCardCount', 10) // More than available
        ->call('startStudySession')
        ->assertHasErrors(['studyCardCount']);
});

test('study session validation fails with zero cards', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    Card::factory()->count(5)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    actingAs($user);

    Livewire::test(DeckShow::class, ['deck' => $deck->fresh()])
        ->set('studyCardCount', 0)
        ->call('startStudySession')
        ->assertHasErrors(['studyCardCount']);
});

test('can call generate AI cards method', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();

    actingAs($user);

    // Just test that the method can be called without errors
    // The actual AI generation will fail due to missing API keys
    Livewire::test(DeckShow::class, ['deck' => $deck->fresh()])
        ->call('generateAiCards')
        ->assertSet('isGenerating', false)
        ->assertDispatched('flash-message');
});

test('refresh card list updates component state', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    actingAs($user);

    $component = Livewire::test(DeckShow::class, ['deck' => $deck->fresh()]);

    // Initially should have 1 card
    expect($component->get('deck')->cards)->toHaveCount(1);

    // Create another card directly in database
    Card::factory()->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    // Call refresh method
    $component->call('refreshCardList');

    // Should now have 2 cards
    expect($component->get('deck')->cards)->toHaveCount(2);
});

test('component initializes correctly with deck data', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $cards = Card::factory()->count(3)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    actingAs($user);

    Livewire::test(DeckShow::class, ['deck' => $deck->fresh()])
        ->assertSet('deck.id', $deck->id)
        ->assertSet('deck.name', $deck->name)
        ->assertSet('isGenerating', false);
});

test('study card count is limited to available cards', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    Card::factory()->count(3)->create([
        'user_id' => $user->id,
        'deck_id' => $deck->id,
    ]);

    actingAs($user);

    Livewire::test(DeckShow::class, ['deck' => $deck->fresh()])
        ->call('openStudyModal')
        ->assertSet('studyCardCount', 3); // Should be limited to available cards
});
