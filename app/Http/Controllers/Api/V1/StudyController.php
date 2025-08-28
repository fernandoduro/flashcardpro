<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Models\Study;
use App\Models\StudyResult;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StudyController extends Controller
{
    use AuthorizesRequests;

    /**
     * Create a new study session for the authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'deck_id' => ['required', Rule::exists('decks', 'id')->where('user_id', $request->user()->id)],
        ]);

        $user = $request->user();

        $study = $user->studies()->create($data);

        return ApiResponse::success(
            ['study_id' => $study->id],
            'Study session created successfully',
            201
        );
    }

    /**
     * Mark a study session as completed.
     */
    public function complete(Request $request, Study $study): JsonResponse
    {
        $this->authorize('update', $study);

        if ($study->user_id !== $request->user()->id) {
            return ApiResponse::error('Unauthorized access to study session', 403);
        }

        if ($study->completed_at !== null) {
            return ApiResponse::error('Study session is already completed', 422);
        }

        if ($study->created_at->diffInHours(now()) > 24) {
            return ApiResponse::error('Study session has expired', 422);
        }

        $study->update(['completed_at' => now()]);

        return ApiResponse::success(null, 'Study session completed successfully');
    }

    /**
     * Record a study result for a specific card.
     */
    public function recordResult(Request $request): JsonResponse
    {
        $data = $request->validate([
            'study_id' => ['required', 'integer', Rule::exists('studies', 'id')->where('user_id', $request->user()->id)],
            'card_id' => ['required', 'integer', Rule::exists('cards', 'id')->where('user_id', $request->user()->id)],
            'is_correct' => ['required', 'boolean'],
        ]);

        $study = Study::find($data['study_id']);
        if (! $study) {
            return ApiResponse::error('Study session not found', 404);
        }

        if ($study->user_id !== $request->user()->id) {
            return ApiResponse::error('Unauthorized access to study session', 403);
        }

        try {
            DB::beginTransaction();

            $existingResult = StudyResult::where('study_id', $data['study_id'])
                ->where('card_id', $data['card_id'])
                ->lockForUpdate()
                ->first();

            if ($existingResult) {
                DB::rollBack();

                return ApiResponse::error('Study result already exists for this card', 422);
            }

            StudyResult::create($data);

            DB::commit();

            return ApiResponse::success(null, 'Study result recorded successfully', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to record study result', [
                'error' => $e->getMessage(),
                'study_id' => $data['study_id'],
                'card_id' => $data['card_id'],
                'user_id' => $request->user()->id,
            ]);

            return ApiResponse::error('Failed to record study result', 500);
        }
    }
}
