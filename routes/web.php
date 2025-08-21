<?php

use App\Livewire\Decks;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/decks', Decks\Index::class)->name('decks.index');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/decks', Decks\Index::class)->name('decks.index');
    Route::get('/decks/{deck}', Decks\Show::class)->name('decks.show');
    Route::view('profile', 'profile')->name('profile');
});

Route::get('/study/{deck}', function (\App\Models\Deck $deck) {
    // Authorize that the current user can view this deck
    if (auth()->user()->cannot('view', $deck)) {
        abort(403);
    }
    return view('study.show', ['deck' => $deck]);
})->middleware(['auth', 'verified'])->name('study.show');


require __DIR__.'/auth.php';
