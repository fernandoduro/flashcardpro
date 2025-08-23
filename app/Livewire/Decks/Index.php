<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Livewire\Component;

class Index extends Component
{
    protected $listeners = ['deckCreated' => '$refresh', 'deckUpdated' => '$refresh', 'deleteDeck' => 'deleteDeck'];    
    
    public function deleteDeck(int $deckId)
    {
        $deck = Deck::findOrFail($deckId);
        $this->authorize('delete', $deck);
        $deck->delete();
        // The list will refresh automatically
    }

    public function render()
    {
        $decks = auth()->user()->decks()->withCount('cards')->get();

        return view('livewire.decks.index', [
            'decks' => $decks,
        ])->layout('layouts.app');
    }
}