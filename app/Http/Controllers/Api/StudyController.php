<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use App\Models\Study;
use App\Models\StudyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- 1. IMPORT THE TRAIT

class StudyController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request)
    {
        $data = $request->validate([
            'deck_id' => 'required|exists:decks,id'
        ]);

        $deck = Deck::findOrFail($data['deck_id']);

        $this->authorize('create', [Study::class, $deck]);

        $study = Study::create([
            'user_id' => Auth::id(),
            'deck_id' => $deck->id,
        ]);

        return response()->json(['study_id' => $study->id]);
    }

    public function complete(Study $study)
    {
        $this->authorize('update', $study);
        $study->update(['completed_at' => now()]);
        return response()->json(['message' => 'Study session completed.']);
    }

    public function recordResult(Request $request)
    {
        $data = $request->validate([
            'study_id' => 'required|exists:studies,id',
            'card_id' => 'required|exists:cards,id',
            'is_correct' => 'required|boolean',
        ]);

        $study = Study::findOrFail($data['study_id']);
        $this->authorize('update', $study);

        StudyResult::create($data);

        return response()->json(['message' => 'Result recorded.']);
    }
}