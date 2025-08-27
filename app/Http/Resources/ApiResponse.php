<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Return a successful API response.
     */
    public static function success($data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'api_version' => 'v1',
            'timestamp' => now()->toISOString(),
        ], $status);
    }

    /**
     * Return an error API response.
     */
    public static function error(string $message = 'Error', int $status = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'api_version' => 'v1',
            'timestamp' => now()->toISOString(),
        ], $status);
    }

    /**
     * Return a paginated API response.
     */
    public static function paginated($paginator, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ],
            'api_version' => 'v1',
            'timestamp' => now()->toISOString(),
        ], 200);
    }

    /**
     * Return a no content response.
     */
    public static function noContent(string $message = 'Operation completed')
    {
        return response()->noContent();
    }
}
