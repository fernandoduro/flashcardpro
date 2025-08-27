<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCardRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\CardResource;
use App\Models\Deck;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get a paginated list of the authenticated user's decks.
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'per_page' => 'integer|min:1|max:100',
        ]);

        $perPage = $validated['per_page'] ?? 10;

        $decks = $request->user()
            ->decks()
            ->withCount('cards')
            ->latest()
            ->paginate($perPage);

        return ApiResponse::paginated($decks, 'Decks retrieved successfully');
    }

    /**
     * Get all cards from a specific deck in random order.
     */
    public function cards(Request $request, Deck $deck): \Illuminate\Http\JsonResponse
    {
        $this->authorize('view', $deck);
        $cards = $deck->cards()
            ->select(['id', 'question', 'answer', 'user_id', 'deck_id', 'created_at', 'updated_at'])
            ->inRandomOrder()
            ->get();

        return ApiResponse::success(
            CardResource::collection($cards),
            'Cards retrieved successfully'
        );
    }

    /**
     * Create a new card in the specified deck.
     */
    public function storeCard(StoreCardRequest $request, Deck $deck): \Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $deck);

        $card = $deck->cards()->create($request->validated() + [
            'user_id' => $request->user()->id,
        ]);

        return ApiResponse::success(
            new CardResource($card),
            'Card created successfully',
            201
        );
    }
}
