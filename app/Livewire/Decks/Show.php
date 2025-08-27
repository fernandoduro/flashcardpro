<?php

namespace App\Livewire\Decks;

use App\CardGenerator\AiCardGenerator;
use App\Models\Card;
use App\Models\Deck;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Deck: {deck.name}')]
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

        $cardGenerator = new AiCardGenerator;
        $cards = $cardGenerator->generate(theme: $this->deck->name, count: 5);

        if (empty($cards)) {
            // Handle the failure
            $this->dispatch('flash-message', [
                'type' => 'error',
                'message' => 'Sorry, the AI card generator failed. Please try again.',
            ]);
            $this->isGenerating = false;

            return;
        }

        $userId = auth()->id();
        $createdCards = 0;

        try {
            foreach ($cards as $cardData) {
                // Validate card data before saving
                if (empty(trim($cardData['question'])) || empty(trim($cardData['answer']))) {
                    Log::warning('AI Card Generation: Skipping card with empty content', $cardData);

                    continue;
                }

                if (strlen($cardData['question']) > 1000 || strlen($cardData['answer']) > 1000) {
                    Log::warning('AI Card Generation: Skipping card with content too long', [
                        'question_length' => strlen($cardData['question']),
                        'answer_length' => strlen($cardData['answer']),
                    ]);

                    continue;
                }

                $this->deck->cards()->create([
                    'question' => $cardData['question'],
                    'answer' => $cardData['answer'],
                    'user_id' => $userId,
                ]);
                $createdCards++;
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database-specific errors
            Log::error('AI Card Generation: Database error while saving cards', [
                'error' => $e->getMessage(),
                'deck_id' => $this->deck->id,
                'user_id' => $userId,
            ]);

            $this->dispatch('flash-message', [
                'type' => 'error',
                'message' => 'Database error occurred while saving cards. Please check your input and try again.',
            ]);
            $this->isGenerating = false;

            return;
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            Log::error('AI Card Generation: Validation error while saving cards', [
                'errors' => $e->errors(),
                'deck_id' => $this->deck->id,
            ]);

            $this->dispatch('flash-message', [
                'type' => 'error',
                'message' => 'Some generated cards contain invalid data. Please try generating again.',
            ]);
            $this->isGenerating = false;

            return;
        } catch (\Exception $e) {
            // Handle any other unexpected errors
            Log::error('AI Card Generation: Unexpected error while saving cards', [
                'error' => $e->getMessage(),
                'deck_id' => $this->deck->id,
                'user_id' => $userId,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->dispatch('flash-message', [
                'type' => 'error',
                'message' => 'An unexpected error occurred while saving cards. Please try again.',
            ]);
            $this->isGenerating = false;

            return;
        }

        // Provide feedback on successful creation
        if ($createdCards > 0) {
            $this->dispatch('flash-message', [
                'type' => 'success',
                'message' => "Successfully generated and saved {$createdCards} card".($createdCards > 1 ? 's' : '').'!',
            ]);
        }

        $this->isGenerating = false;
        $this->refreshCardList();
    }

    /**
     * Render the deck show component.
     */
    public function render(): View
    {
        return view('livewire.decks.show')
            ->layout('layouts.app', ['title' => $this->deck->name]);
    }
}
