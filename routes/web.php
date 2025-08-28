<?php

use App\Livewire\Cards;
use App\Livewire\Decks;
use App\Livewire\Statistics;
use App\Models\Deck;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public / Guest Routes
Route::view('/', 'welcome')->name('welcome');
Route::view('/api/documentation', 'api-docs')->name('api.docs');
Route::get('/openapi.yaml', function () {
    $path = public_path('openapi.yaml');
    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path, ['Content-Type' => 'application/vnd.oai.openapi;charset=utf-8']);
})->name('api.docs.yaml');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/statistics', Statistics\Index::class)->name('statistics.index');
    Route::view('/profile', 'profile')->name('profile');

    // Deck-specific routes
    Route::prefix('decks')->name('decks.')->group(function () {
        Route::get('/', Decks\Index::class)->name('index');
        Route::get('/create', Decks\Form::class)->name('create');
        Route::get('/{deck}', Decks\Show::class)->name('show');
        Route::get('/{deck}/cards/create', Cards\Form::class)->name('cards.create');
    });

    // Study route
    Route::get('/study/{deck}', function (Deck $deck, \Illuminate\Http\Request $request) {
        $deck->loadCount('cards'); // Load the cards count relationship
        $cardCount = (int) $request->query('count', $deck->cards_count);

        return view('study.show', [
            'deck' => $deck,
            'requestedCardCount' => $cardCount,
        ]);
    })->name('study.show');
});

require __DIR__.'/auth.php';
