<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Study;
use App\Models\StudyResult;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Api\V1\StoreStudyResultRequest;
use App\Http\Resources\ApiResponse;

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

        return ApiResponse::success(
            ['study_id' => $study->id],
            'Study session created successfully',
            201
        );
    }

    public function complete(Request $request, Study $study): JsonResponse
    {
        $this->authorize('update', $study);

        // Validate that the study belongs to the authenticated user
        if ($study->user_id !== $request->user()->id) {
            return ApiResponse::error('Unauthorized access to study session', 403);
        }

        // Check if study is already completed
        if ($study->completed_at !== null) {
            return ApiResponse::error('Study session is already completed', 422);
        }

        // Validate that the study was created recently (within last 24 hours)
        if ($study->created_at->diffInHours(now()) > 24) {
            return ApiResponse::error('Study session has expired', 422);
        }

        $study->update(['completed_at' => now()]);

        return ApiResponse::success(null, 'Study session completed successfully');
    }

    public function recordResult(StoreStudyResultRequest $request): JsonResponse
    {
        StudyResult::create($request->validated());

        return ApiResponse::success(null, 'Study result recorded successfully', 201);
    }
}