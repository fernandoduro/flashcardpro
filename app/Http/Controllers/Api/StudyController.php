<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deck;
use App\Models\Study;
use App\Models\StudyResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 
use Illuminate\Validation\Rule;

class StudyController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'deck_id' => ['required', Rule::exists('decks', 'id')->where('user_id', $request->user()->id)],
        ]);

        $user = $request->user();

        $study = $user->studies()->create($data);

        return response()->json(['study_id' => $study->id], 201);
    }

    public function complete(Request $request, Study $study): JsonResponse
    {
        $this->authorize('update', $study);

        $study->update(['completed_at' => now()]);

        return response()->json(['message' => 'Study session completed.']);
    }

    public function recordResult(Request $request): JsonResponse
    {
        $data = $request->validate([
            'study_id' => ['required', Rule::exists('studies', 'id')->where('user_id', $request->user()->id)],
            'card_id' => ['required', Rule::exists('cards', 'id')->where('user_id', $request->user()->id)],
            'is_correct' => ['required', 'boolean'],
        ]);

        StudyResult::create($data);

        return response()->json(['message' => 'Result recorded.'], 201);
    }
}