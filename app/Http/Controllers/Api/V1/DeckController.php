<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Http\Resources\DeckResource;
use App\Models\Deck;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Requests\Api\V1\StoreCardRequest;

class DeckController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $decks = $request->user()
            ->decks()
            ->withCount('cards')
            ->latest()
            ->paginate(15);

        return DeckResource::collection($decks);
    }

    public function cards(Request $request, Deck $deck): AnonymousResourceCollection
    {
        $this->authorize('view', $deck);

        // Use eager loading and optimize query
        $cards = $deck->cards()
            ->select(['id', 'question', 'answer', 'user_id', 'deck_id', 'created_at', 'updated_at'])
            ->inRandomOrder()
            ->get();

        return CardResource::collection($cards);
    }

    public function storeCard(StoreCardRequest $request, Deck $deck): CardResource
    {
        // Authorize that the user can add cards to this deck
        $this->authorize('update', $deck);

        $card = $deck->cards()->create($request->validated() + [
            'user_id' => $request->user()->id,
        ]);

        return new CardResource($card);
    }
}