<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use AuthorizesRequests;

    /**
     * Delete a deck after confirmation.
     */
    #[On('deleteDeck')]
    public function deleteDeck(int $deckId): void
    {
        $deck = Deck::findOrFail($deckId);
        $this->authorize('delete', $deck);

        $deck->delete();
    }

    /**
     * Pin or unpin a deck.
     */
    public function togglePin(int $deckId): void
    {
        $deck = Deck::findOrFail($deckId);
        $this->authorize('update', $deck);

        $deck->update(['is_pinned' => !$deck->is_pinned]);
    }

    /**
     * Get the decks for the currently authenticated user.
     * This uses a computed property for cleaner access and caching.
     */
    public function getDecksProperty(): Collection
    {
        return auth()->user()
            ->decks()
            ->withCount('cards')
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();
    }

    /**
     * Render the component.
     *
     * We no longer need to pass the decks manually,
     * as the computed property `$this->decks` is now automatically available.
     */
    #[On('deckCreated')]
    #[On('deckUpdated')]
    public function render(): View
    {
        return view('livewire.decks.index')
            ->layout('layouts.app');
    }
}