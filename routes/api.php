<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudyController;
use App\Http\Controllers\Api\DeckController;
use App\Http\Controllers\Api\AuthController; // We will create this next

// Public API route for getting a token
Route::post('/login', [AuthController::class, 'login']);

// Protected API routes
Route::middleware(['auth:sanctum', 'log.api'])->group(function () {
    // Your existing routes are now protected by token auth
    Route::get('/decks', [DeckController::class, 'index']);
    Route::get('/decks/{deck}/cards', [DeckController::class, 'cards']);

    Route::post('/studies', [StudyController::class, 'store']);
    Route::patch('/studies/{study}/complete', [StudyController::class, 'complete']);
    Route::post('/study-results', [StudyController::class, 'recordResult']);

    // Route for revoking a token
    Route::post('/logout', [AuthController::class, 'logout']);
});