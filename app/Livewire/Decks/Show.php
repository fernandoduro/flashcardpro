<?php

namespace App\Livewire\Decks;

use App\Models\Card;
use App\Models\Deck;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests;

    public Deck $deck;

    protected $listeners = ['cardCreated' => 'refreshCardList', 'cardUpdated' => 'refreshCardList', 'deleteCard' => 'deleteCard'];

    public function deleteCard(int $cardId)
    {
        $card = Card::findOrFail($cardId);
        $this->authorize('delete', $card);

        // Note: In the future, we might want to just detach the card from the deck, not delete the card itself.
        // $this->deck->cards()->detach($cardId);

        $card->delete();

        $this->refreshCardList();
    }
    
    public function mount(Deck $deck)
    {
        $this->authorize('view', $deck);
        $this->deck = $deck->load('cards');
    }

    public function refreshCardList()
    {
        $this->deck->load('cards');
    }

    public function render()
    {
        return view('livewire.decks.show')->layout('layouts.app', ['title' => $this->deck->name]);;
    }
}