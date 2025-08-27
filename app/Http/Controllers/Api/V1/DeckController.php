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
use App\Http\Resources\ApiResponse;

class DeckController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'per_page' => 'integer|min:1|max:100'
        ]);

        $perPage = $validated['per_page'] ?? 10;
    
        $decks = $request->user()
            ->decks()
            ->withCount('cards')
            ->latest()
            ->paginate($perPage);

        return ApiResponse::paginated($decks, 'Decks retrieved successfully');
    }

    public function cards(Request $request, Deck $deck): \Illuminate\Http\JsonResponse
    {
        $this->authorize('view', $deck);

        // Use eager loading and optimize query
        $cards = $deck->cards()
            ->select(['id', 'question', 'answer', 'user_id', 'deck_id', 'created_at', 'updated_at'])
            ->inRandomOrder()
            ->get();

        return ApiResponse::success(
            CardResource::collection($cards),
            'Cards retrieved successfully'
        );
    }

    public function storeCard(StoreCardRequest $request, Deck $deck): \Illuminate\Http\JsonResponse
    {
        // Authorize that the user can add cards to this deck
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