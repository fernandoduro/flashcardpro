<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Form extends Component
{
    public bool $showModal = false;
    public ?Deck $editingDeck = null;
    
    public string $name = '';
    public bool $isPublic = false;
    

    // Listen for the 'openDeckModal' event from the parent component
    protected $listeners = [
        'openCreateModal' => 'openForCreate',
        'openEditModal' => 'openForEdit',
    ];

    public function openForCreate()
    {
        $this->editingDeck = null; // Ensure we are not in edit mode
        $this->resetValidation();
        $this->reset('name', 'isPublic');
        $this->dispatch('open-deck-modal');
    }

    public function openForEdit(int $deckId)
    {
        $deck = Deck::findOrFail($deckId);
        // Optional: Add authorization check if needed
        // $this->authorize('update', $deck);

        $this->editingDeck = $deck;
        $this->name = $deck->name;
        $this->isPublic = $deck->public;

        $this->resetValidation();
        $this->dispatch('open-deck-modal');
    }

    public function close()
    {
        $this->reset('name', 'isPublic', 'editingDeck');
        $this->dispatch('close-deck-modal');
    }

     public function rules(): array
    {
        return [
            // When editing, the name must be unique, but we must ignore the current deck's name
            'name' => [
                'required',
                'string',
                'max:255',
                $this->editingDeck
                    ? Rule::unique('decks')->ignore($this->editingDeck->id)
                    : Rule::unique('decks')
            ],
            'isPublic' => 'required|boolean',
        ];
    }

     public function save()
    {
        $validated = $this->validate();

        if ($this->editingDeck) {
            // We are in EDIT mode
            $this->editingDeck->update([
                'name' => $validated['name'],
                'public' => $validated['isPublic'],
            ]);
            $this->dispatch('deckUpdated');
        } else {
            // We are in CREATE mode
            auth()->user()->decks()->create([
                'name' => $validated['name'],
                'public' => $validated['isPublic'],
            ]);
            $this->dispatch('deckCreated');
        }

        $this->close();
    }

    public function render()
    {
        return view('livewire.decks.form')->layout('layouts.app');
    }
}