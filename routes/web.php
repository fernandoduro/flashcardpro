<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Decks;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('register', Register::class)->name('register');
    Route::get('login', Login::class)->name('login');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/decks', Decks\Index::class)->name('decks.index');
    // Add other protected routes here later

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
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

