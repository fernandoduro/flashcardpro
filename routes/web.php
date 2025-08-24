<?php

use App\Livewire\Decks;
use App\Livewire\Cards;
use App\Livewire\Statistics;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');


Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/decks', Decks\Index::class)->name('decks.index');
    Route::get('/decks/create', Decks\Form::class)->name('decks.form');
    Route::get('/decks/{deck}', Decks\Show::class)->name('decks.show');
    Route::get('/decks/{deck}/cards/create', Cards\Form::class)->name('decks.cards.form');
    Route::view('profile', 'profile')->name('profile');
    Route::get('/statistics', Statistics\Index::class)->name('statistics.index');
});

Route::get('/study/{deck}', function (\App\Models\Deck $deck) {
    // Authorize that the current user can view this deck
    if (auth()->user()->cannot('view', $deck)) {
        abort(403);
    }
    return view('study.show', ['deck' => $deck]);
})->middleware(['auth', 'verified'])->name('study.show');


require __DIR__.'/auth.php';
