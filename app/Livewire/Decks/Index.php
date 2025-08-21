<?php

namespace App\Livewire\Decks;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $decks = auth()->user()->decks()->withCount('cards')->get();
        return view('livewire.decks.index', [
            'decks' => $decks,
        ])->layout('layouts.app');
    }
}