<?php

namespace App\Livewire\Decks;

use App\Models\Deck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public ?Deck $editingDeck = null;

    public string $name = '';
    public bool $isPublic = false;
    public $coverImage = null;

    protected $listeners = [
        'openCreateModal' => 'openForCreate',
        'openEditModal' => 'openForEdit',
    ];

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                $this->editingDeck
                    ? Rule::unique('decks')->where('user_id', auth()->id())->ignore($this->editingDeck->id)
                    : Rule::unique('decks')->where('user_id', auth()->id())
            ],
            'isPublic' => ['required', 'boolean'],
            'coverImage' => [
                'nullable',
                'image',
                'max:2048', // Max 2MB
                'mimes:jpeg,png,jpg,gif,webp',
            ],
        ];
    }

    /**
     * Prepare the modal for creating a new deck.
     */
    public function openForCreate(): void
    {
        $this->authorize('create', Deck::class);
        $this->resetState();
        $this->dispatch('open-modal', 'deck-form');
    }

    /**
     * Prepare the modal for editing an existing deck.
     */
    public function openForEdit(int $deckId): void
    {
        $deck = Deck::findOrFail($deckId);
        $this->authorize('update', $deck);

        $this->editingDeck = $deck;
        $this->name = $deck->name;
        $this->isPublic = $deck->public;

        $this->resetValidation();
        $this->dispatch('open-modal', 'deck-form');
    }

    /**
     * Save the new or edited deck to the database.
     */
    public function save(): void
    {
        $validated = $this->validate();
        $dataToSave = [
            'name' => $validated['name'],
            'public' => $validated['isPublic'],
        ];

        if ($this->editingDeck) {
            $this->authorize('update', $this->editingDeck);
        } else {
            $this->authorize('create', Deck::class);
        }

        if ($this->coverImage) {
            // If a new image is uploaded, store it and get the path.
            $dataToSave['cover_image_path'] = $this->coverImage->store('deck-covers', 'public');

            // If we are editing and an old image existed, delete it.
            if ($this->editingDeck?->cover_image_path) {
                Storage::disk('public')->delete($this->editingDeck->cover_image_path);
            }
        }

        if ($this->editingDeck) {
            $this->editingDeck->update($dataToSave);
            $this->dispatch('deckUpdated');
        } else {
            /** @var \App\Models\User $user */
            $user = auth()->user();
            $user->decks()->create($dataToSave);
            $this->dispatch('deckCreated');
        }

        $this->dispatch('close-modal', 'deck-form');
        $this->resetState();
    }

    /**
     * Reset the component's state.
     */
    public function resetState(): void
    {
        $this->reset('editingDeck', 'name', 'isPublic', 'coverImage');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.decks.form');
    }
}