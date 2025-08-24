<?php

namespace App\Livewire\Cards;

use App\Models\Card;
use App\Models\Deck;
use Livewire\Component;

class Form extends Component
{
    public bool $showModal = false;
    public ?Card $editingCard = null;

    public ?Deck $deck = null;
    public string $question = '';
    public string $answer = '';

    protected $listeners = [
        'openCreateCardModal' => 'openForCreate',
        'openEditCardModal' => 'openForEdit',
    ];

    public function rules(): array
    {
        return [
            'question' => 'required|string|min:5',
            'answer' => 'required|string|min:1',
        ];
    }

    public function openForCreate(int $deckId)
    {
        $this->deck = Deck::findOrFail($deckId);
        $this->editingCard = null;
        $this->resetValidation();
        $this->reset('question', 'answer');
        $this->dispatch('open-modal', 'card-form');
    }

    // Open the modal for EDITING an existing card
    public function openForEdit(int $cardId)
    {
        $card = Card::findOrFail($cardId);
        // Optional: Add authorization
        // $this->authorize('update', $card);

        $this->editingCard = $card;
        $this->deck = null; // Deck context isn't strictly needed for editing a card directly
        $this->question = $card->question;
        $this->answer = $card->answer;

        $this->resetValidation();
        $this->dispatch('open-modal', 'card-form');
    }

    public function close()
    {
        $this->reset('question', 'answer', 'editingCard', 'deck');
        $this->dispatch('close-modal', 'card-form');
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->editingCard) {
            // EDIT mode
            $this->editingCard->update($validated);
            $this->dispatch('cardUpdated');
        } else {
            // CREATE mode
            $card = auth()->user()->cards()->create($validated);
            $this->deck->cards()->attach($card->id);
            $this->dispatch('cardCreated');
        }

        $this->close();
    }

    public function render()
    {
        return view('livewire.cards.form')->layout('layouts.app');
    }
}