<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ConfirmationModal extends Component
{
    // We no longer need the $showModal property
    // public bool $showModal = false;

    public string $title = '';
    public string $message = '';
    public string $confirmAction = '';
    public $itemId;

    protected $listeners = ['openConfirmationModal' => 'open'];

    public function open(string $title, string $message, string $confirmAction, int $itemId)
    {
        $this->title = $title;
        $this->message = $message;
        $this->confirmAction = $confirmAction;
        $this->itemId = $itemId;

        // Dispatch an event for Alpine to open the modal
        $this->dispatch('open-modal', 'confirmation-modal');
    }

    public function confirm()
    {
        // Dispatch the action to the parent component (e.g., 'deleteDeck')
        $this->dispatch($this->confirmAction, $this->itemId);

        // Dispatch an event for Alpine to close this modal
        $this->dispatch('close-modal', 'confirmation-modal');
    }

    public function render()
    {
        return view('livewire.components.confirmation-modal');
    }
}