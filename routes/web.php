<?php

use App\Livewire\Decks;
use App\Livewire\Cards;
use App\Livewire\Statistics;
use Illuminate\Support\Facades\File;
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

Route::view('/api/documentation', 'api-docs')->name('api.docs');

Route::get('/openapi.yaml', function () {
    $path = public_path('openapi.yaml');

    if (!file_exists($path)) {
        abort(404, 'The OpenAPI specification file was not found.');
    }

    return response()->file($path, [
        'Content-Type' => 'application/vnd.oai.openapi;charset=utf-8', // A more correct MIME type
    ]);
})->name('api.docs.yaml');

require __DIR__.'/auth.php';
