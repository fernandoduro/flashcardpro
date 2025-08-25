<?php

use App\Livewire\Cards\Form as CardForm;
use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    // Create a user and act as that user for all tests in this file
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('can create a new card', function () {
    $deck = Deck::factory()->for($this->user)->create();

    Livewire::test(CardForm::class)
        ->call('openForCreate', $deck->id)
        ->set('question', 'What is the capital of France?')
        ->set('answer', 'Paris')
        ->call('save')
        ->assertDispatched('cardCreated');
        
    assertDatabaseHas('cards', [
        'deck_id' => $deck->id,
        'user_id' => $this->user->id,
        'question' => 'What is the capital of France?',
        'answer' => 'Paris',
    ]);
});

test('can edit an existing card', function () {
    $card = Card::factory()->for($this->user)->create([
        'question' => 'Old Question',
    ]);

    Livewire::test(CardForm::class)
        ->call('openForEdit', $card->id)
        ->set('question', 'New Updated Question')
        ->set('answer', $card->answer) // Answer remains the same
        ->call('save')
        ->assertDispatched('cardUpdated')
        ->assertDispatched('close-modal', 'card-form');

    assertDatabaseHas('cards', [
        'id' => $card->id,
        'question' => 'New Updated Question',
    ]);

    assertDatabaseMissing('cards', [
        'id' => $card->id,
        'question' => 'Old Question',
    ]);
});

test('validation fails for empty fields', function () {
    $deck = Deck::factory()->for($this->user)->create();

    Livewire::test(CardForm::class)
        ->call('openForCreate', $deck->id)
        ->set('question', '')
        ->set('answer', '')
        ->call('save')
        ->assertHasErrors(['question' => 'required', 'answer' => 'required']);
});

test('a user cannot edit a card they do not own', function () {
    // Create a card owned by a different user
    $otherUser = User::factory()->create();
    $card = Card::factory()->for($otherUser)->create();

    // Try to open the edit modal for that card as the authenticated user
    Livewire::test(CardForm::class)
        ->call('openForEdit', $card->id)
        ->assertForbidden();
});