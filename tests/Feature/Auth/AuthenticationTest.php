<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;


test('registration screen can be rendered', function () {
    get(route('register'))->assertOk();
});

test('a new user can register', function () {
    // Manually start a session and bind it.
    $this->startSession();

    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->call('register')
        ->assertRedirect(route('decks.index'));

    assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
});

test('login screen can be rendered', function () {
    get(route('login'))->assertOk();
});

test('a user can log in with correct credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
    ]);

    // Manually start a session and bind it.
    $this->startSession();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('decks.index'));

    assertAuthenticated();
});

test('a user cannot log in with incorrect credentials', function () {
    $user = User::factory()->create();

    // Manually start a session and bind it.
    $this->startSession();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('email');

    assertGuest();
});

// The rest of the tests were correct and do not need to change.
test('login validation works for required fields', function () {
    Livewire::test(Login::class)
        ->call('login')
        ->assertHasErrors(['email' => 'required', 'password' => 'required']);
});

test('decks index page is protected from guests', function () {
    get(route('decks.index'))
        ->assertRedirect(route('login'));
});

test('a logged in user can access the decks index page', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get(route('decks.index'))->assertOk();
});