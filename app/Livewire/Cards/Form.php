<?php

namespace App\Livewire\Cards;

use App\Models\Card;
use App\Models\Deck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Form extends Component
{
    use AuthorizesRequests;

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
            'question' => ['required', 'string', 'min:5'],
            'answer' => ['required', 'string', 'min:1'],
        ];
    }

    /**
     * Prepare the modal for creating a new card for a given deck.
     */
    public function openForCreate(int $deckId): void
    {
        $this->deck = Deck::findOrFail($deckId);
        $this->authorize('create', Card::class);
        $this->reset('editingCard', 'question', 'answer');
        $this->resetValidation();

        $this->dispatch('open-modal', 'card-form');
    }

    /**
     * Prepare the modal for editing an existing card.
     */
    public function openForEdit(int $cardId): void
    {
        $card = Card::findOrFail($cardId);
        $this->authorize('update', $card);

        $this->editingCard = $card;
        $this->deck = null;
        $this->question = $card->question;
        $this->answer = $card->answer;

        $this->resetValidation();
        $this->dispatch('open-modal', 'card-form');
    }

    /**
     * Save the new or edited card to the database.
     */
    public function save(): void
    {
        $this->validate();

        if ($this->editingCard) {   
            $this->authorize('update', $this->editingCard);
            $this->editingCard->update([
                'question' => $this->question,
                'answer' => $this->answer,
            ]);
            $this->dispatch('cardUpdated');
        } else {
            $this->authorize('create', Card::class);

            $this->deck->cards()->create([
                'question' => $this->question,
                'answer' => $this->answer,
                'user_id' => auth()->id()
            ]);

            $this->dispatch('cardCreated');
        }

        $this->dispatch('close-modal', 'card-form');
        $this->resetState();
    }

    /**
     * Reset the component's state.
     */
    public function resetState(): void
    {
        $this->reset('editingCard', 'deck', 'question', 'answer');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.cards.form');
    }
}