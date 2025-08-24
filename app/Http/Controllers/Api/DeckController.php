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
}