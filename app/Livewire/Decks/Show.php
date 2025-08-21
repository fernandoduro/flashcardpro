<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests;

    public Deck $deck;

    public function mount(Deck $deck)
    {
        $this->authorize('view', $deck);
        $this->deck = $deck->load('cards');
    }

    public function render()
    {
        return view('livewire.decks.show')->layout('layouts.app', ['title' => $this->deck->name]);;
    }
}