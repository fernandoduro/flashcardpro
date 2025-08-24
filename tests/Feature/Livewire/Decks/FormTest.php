<?php

use App\Livewire\Decks\Form as DeckForm;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('can create a new deck with a cover image', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->image('cover.jpg');

    Livewire::test(DeckForm::class)
        ->call('openForCreate')
        ->set('name', 'New Test Deck')
        ->set('isPublic', true)
        ->set('coverImage', $file)
        ->call('save')
        ->assertDispatched('deckCreated');

    assertDatabaseHas('decks', [
        'user_id' => $this->user->id,
        'name' => 'New Test Deck',
        'public' => true,
    ]);

    // Assert that the file was stored
    $deck = Deck::first();
    Storage::disk('public')->assertExists($deck->cover_image_path);
});

test('can edit a deck and replace its cover image', function () {
    Storage::fake('public');
    $oldFile = UploadedFile::fake()->image('old_cover.jpg');
    $oldPath = $oldFile->store('deck-covers', 'public');

    $deck = Deck::factory()->for($this->user)->create([
        'name' => 'Original Name',
        'cover_image_path' => $oldPath,
    ]);

    $newFile = UploadedFile::fake()->image('new_cover.jpg');

    Livewire::test(DeckForm::class)
        ->call('openForEdit', $deck->id)
        ->set('name', 'Updated Name')
        ->set('coverImage', $newFile)
        ->call('save')
        ->assertDispatched('deckUpdated');

    assertDatabaseHas('decks', [
        'id' => $deck->id,
        'name' => 'Updated Name',
    ]);

    // Assert the old file was deleted and the new one exists
    Storage::disk('public')->assertMissing($oldPath);
    $deck->refresh();
    Storage::disk('public')->assertExists($deck->cover_image_path);
});

test('validation fails if deck name is not unique for the user', function () {
    Deck::factory()->for($this->user)->create(['name' => 'Existing Deck']);

    Livewire::test(DeckForm::class)
        ->call('openForCreate')
        ->set('name', 'Existing Deck')
        ->call('save')
        ->assertHasErrors(['name' => 'unique']);
});