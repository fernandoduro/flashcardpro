<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeckController;
use App\Http\Controllers\Api\StudyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is all API routes for the application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('api.login');

    // Protected logout route
    Route::post('/logout', 'logout')->name('api.logout')->middleware('auth:sanctum');
});

// Protected Application Routes
Route::middleware(['auth:sanctum', 'log.api'])->name('api.')->group(function () {

    // Deck Routes
    Route::controller(DeckController::class)->prefix('decks')->name('decks.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{deck}/cards', 'cards')->name('cards');
        Route::post('/{deck}/cards', 'storeCard')->name('cards.store');
    });

    // Study Routes
    Route::controller(StudyController::class)->prefix('studies')->name('studies.')->group(function () {
        Route::post('/', 'store')->name('store');
        Route::patch('/{study}/complete', 'complete')->name('complete');
    });

    // Study Result Route
    Route::post('/study-results', [StudyController::class, 'recordResult'])->name('study-results.store');
});