<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ConfirmationModal extends Component
{
    public string $title = '';

    public string $message = '';

    public string $confirmAction = '';

    public $itemId;

    protected $listeners = ['openConfirmationModal' => 'open'];

    /**
     * Open the confirmation modal with specified content.
     */
    public function open(string $title, string $message, string $confirmAction, int $itemId): void
    {
        $this->title = $title;
        $this->message = $message;
        $this->confirmAction = $confirmAction;
        $this->itemId = $itemId;

        // Dispatch an event for Alpine to open the modal
        $this->dispatch('open-modal', 'confirmation-modal');
    }

    /**
     * Execute the confirmation action and close the modal.
     */
    public function confirm(): void
    {
        $this->dispatch($this->confirmAction, $this->itemId);
        $this->dispatch('close-modal', 'confirmation-modal');
    }

    /**
     * Render the confirmation modal view.
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.components.confirmation-modal');
    }
}
