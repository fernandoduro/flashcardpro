<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Index extends Component
{
    use AuthorizesRequests; 

    protected $listeners = ['deckCreated' => '$refresh', 'deckUpdated' => '$refresh', 'deleteDeck' => 'deleteDeck'];    
    
    public function deleteDeck(int $deckId)
    {
        $deck = Deck::findOrFail($deckId);
        $this->authorize('delete', $deck);
        
        $deck->delete();
        // The list will refresh automatically
    }

    
    public function togglePin(int $deckId)
    {
        $deck = Deck::where('user_id', auth()->id())->findOrFail($deckId);
        $deck->update(['is_pinned' => !$deck->is_pinned]);
        // A re-render will be triggered automatically.
    }


    public function render()
    {
        $decks = auth()->user()->decks()->withCount('cards')->orderBy('is_pinned', 'desc')->get();

        return view('livewire.decks.index', [
            'decks' => $decks,
        ])->layout('layouts.app');
    }
}