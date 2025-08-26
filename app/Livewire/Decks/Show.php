<?php

namespace App\Livewire\Decks;

use App\Models\Card;
use App\Models\Deck;
use App\CardGenerator\AiCardGenerator;
use Illuminate\Contracts\View\View; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Show extends Component
{
    use AuthorizesRequests;

    public Deck $deck;
    public bool $isGenerating = false;

    /**
     * Mount the component and authorize the user.
     */
    public function mount(Deck $deck): void
    {
        $this->authorize('view', $deck);
        $this->deck = $deck->load('cards');
    }

    /**
     * Remove a card from the current deck.
     */
    #[On('deleteCard')]
    public function deleteCard(int $cardId): void
    {
        $card = Card::findOrFail($cardId);
        $this->authorize('delete', $card);

        $card->delete();

        $this->refreshCardList();
    }

    /**
     * Refresh the list of cards after an update.
     */
    #[On('cardCreated')]
    #[On('cardUpdated')]
    public function refreshCardList(): void
    {
        $this->deck = $this->deck->fresh()->load('cards');
    }


    public function generateAiCards(): void
    {
        $this->authorize('update', $this->deck);
        $this->isGenerating = true;

        $cardGenerator = new AiCardGenerator();
        $cards = $cardGenerator->generate(theme: $this->deck->name, count: 5);

        if (empty($cards)) {
            // Handle the failure
            $this->dispatch('flash-message', [
                'type' => 'error',
                'message' => 'Sorry, the AI card generator failed. Please try again.'
            ]);
            $this->isGenerating = false;
            return;
        }

        $userId = auth()->id();

        try {
            foreach ($cards as $cardData) {
                $this->deck->cards()->create([
                    'question' => $cardData['question'],
                    'answer' => $cardData['answer'],
                    'user_id' => $userId,
                ]);
            }
        } catch (\Exception $e) {
            // Since the response from AI is unpredictable, we might run into issues saving the cards.
            // That why we put this in a try-catch block.
            $this->dispatch('flash-message', [
                'type' => 'error',
                'message' => 'An error occurred while saving generated cards. Please try again.'
            ]);
            $this->isGenerating = false;
            return;
        }

        $this->isGenerating = false;
        $this->refreshCardList();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('livewire.decks.show')
            ->layout('layouts.app', ['title' => $this->deck->name]);
    }
}