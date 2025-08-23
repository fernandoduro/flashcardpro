<?php

namespace App\Livewire\Components;

use Livewire\Component;

class ConfirmationModal extends Component
{
    public bool $showModal = false;

    public string $title = '';
    public string $message = '';
    public string $confirmAction = '';
    public $itemId;

    protected $listeners = ['openConfirmationModal' => 'open'];

    // CHANGE THE METHOD SIGNATURE from `array $data` to individual parameters
    public function open(string $title, string $message, string $confirmAction, int $itemId)
    {
        $this->title = $title;
        $this->message = $message;
        $this->confirmAction = $confirmAction;
        $this->itemId = $itemId;
        $this->showModal = true;
    }

    public function close()
    {
        $this->showModal = false;
    }

    public function confirm()
    {
        $this->dispatch($this->confirmAction, $this->itemId);
        $this->close();
    }

    public function render()
    {
        return view('livewire.components.confirmation-modal')->layout('layouts.modals');
    }
}