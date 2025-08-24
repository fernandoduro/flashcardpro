<?php

namespace App\Livewire\Decks;

use App\Models\Card;
use App\Models\Deck;
use Illuminate\Contracts\View\View; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    use AuthorizesRequests;

    public Deck $deck;

    /**
     * Mount the component and authorize the user.
     */
    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck->load('cards');
    }

    /**
     * Remove a card from the current deck.
     */
    #[On('deleteCard')]
    public function deleteCard(int $cardId): void
    {
        $card = Card::findOrFail($cardId);
        $this->authorize('delete', $card);

        // Note: In the future, we might want to just detach the card from the deck, not delete the card itself.
        // $this->deck->cards()->detach($cardId);

        $card->delete();

        $this->refreshCardList();
    }

    /**
     * Refresh the list of cards after an update.
     */
    #[On('cardCreated')]
    #[On('cardUpdated')]
    public function refreshCardList(): void
    {
        $this->deck = $this->deck->fresh()->load('cards');
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.decks.show')
            ->layout('layouts.app', ['title' => $this->deck->name]);
    }
}