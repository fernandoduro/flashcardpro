<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Http\Resources\DeckResource;
use App\Models\Deck;
use Illuminate\Http\Request;

class DeckController extends Controller
{
    public function index(Request $request)
    {
        $decks = $request->user()->decks()->withCount('cards')->get();

        return DeckResource::collection($decks);
    }

    public function cards(Request $request, Deck $deck)
    {
        if ($request->user()->cannot('view', $deck)) {
            abort(403);
        }
        
        // Shuffle the cards for the study session
        return CardResource::collection($deck->cards()->inRandomOrder()->get());
    }
}