<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Livewire\Component;
use Livewire\WithFileUploads; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Form extends Component
{
    use WithFileUploads;
    
    public bool $showModal = false;
    public ?Deck $editingDeck = null;
    
    public string $name = '';
    public bool $isPublic = false;
    public $coverImage = null;

    // Listen for the 'openDeckModal' event from the parent component
    protected $listeners = [
        'openCreateModal' => 'openForCreate',
        'openEditModal' => 'openForEdit',
    ];

    public function openForCreate()
    {
        $this->editingDeck = null; // Ensure we are not in edit mode
        $this->resetValidation();
        $this->reset('name', 'isPublic', 'coverImage');
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
        $this->reset('coverImage'); 

        $this->resetValidation();
        $this->dispatch('open-deck-modal');
    }

    public function close()
    {
        $this->reset('name', 'isPublic', 'editingDeck', 'coverImage');
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
            'coverImage' => 'nullable|image|max:2048', 
        ];
    }

     public function save()
    {
        $validated = $this->validate();

        if ($this->coverImage) {
            // Delete the old image if we are editing and an old one exists
            if ($this->editingDeck && $this->editingDeck->cover_image_path) {
                Storage::disk('public')->delete($this->editingDeck->cover_image_path);
            }
            // Store the new image in 'storage/app/public/deck-covers'
            $path = $this->coverImage->store('deck-covers', 'public');
        }

        $dataToSave = [
            'name' => $validated['name'],
            'public' => $validated['isPublic'],
            'cover_image_path' => $path,
        ];

        if ($this->editingDeck) {
            // We are in EDIT mode
            $this->editingDeck->update($dataToSave);
            $this->dispatch('deckUpdated');
        } else {
            // We are in CREATE mode
            auth()->user()->decks()->create($dataToSave);
            $this->dispatch('deckCreated');
        }

        $this->close();
    }

    public function render()
    {
        return view('livewire.decks.form')->layout('layouts.app');
    }
}