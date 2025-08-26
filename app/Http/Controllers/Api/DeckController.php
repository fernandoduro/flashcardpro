<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Http\Resources\DeckResource;
use App\Models\Deck;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeckController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $decks = $decks = $request->user()
            ->decks()
            ->withCount('cards')
            ->latest()
            ->paginate(15);

        return DeckResource::collection($decks);
    }

    public function cards(Request $request, Deck $deck): AnonymousResourceCollection
    {
        $this->authorize('view', $deck);

        return CardResource::collection($deck->cards()->inRandomOrder()->get());
    }

    public function storeCard(Request $request, Deck $deck): CardResource
    {
        // Authorize that the user can add cards to this deck
        $this->authorize('update', $deck);

        $validated = $request->validate([
            'question' => ['required', 'string', 'min:5'],
            'answer' => ['required', 'string', 'min:1'],
        ]);

        $card = $deck->cards()->create([
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'user_id' => $request->user()->id,
        ]);

        return new CardResource($card);
    }
}