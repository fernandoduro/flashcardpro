<?php

use App\Models\Card;
use App\Models\Deck;
use App\Models\Study;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user has many decks', function () {
    $user = User::factory()->create();
    $decks = Deck::factory()->for($user)->count(3)->create();

    expect($user->decks)->toHaveCount(3);
    expect($user->decks->first())->toBeInstanceOf(Deck::class);
});

test('user has many cards', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $cards = Card::factory()->for($user)->for($deck)->count(5)->create();

    expect($user->cards)->toHaveCount(5);
    expect($user->cards->first())->toBeInstanceOf(Card::class);
});

test('user has many studies', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->for($user)->create();
    $studies = Study::factory()->for($user)->for($deck)->count(2)->create();

    expect($user->studies)->toHaveCount(2);
    expect($user->studies->first())->toBeInstanceOf(Study::class);
});

test('user has fillable attributes', function () {
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    $user = User::create($userData);

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
});

test('user name is required', function () {
    try {
        $user = new User([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $user->save();
        expect(false)->toBeTrue('Should have thrown an exception for missing name');
    } catch (\Exception $e) {
        expect($e)->toBeInstanceOf(\Exception::class);
    }
});

test('user email is required', function () {
    try {
        $user = new User([
            'name' => 'Test User',
            'password' => 'password123',
        ]);
        $user->save();
        expect(false)->toBeTrue('Should have thrown an exception for missing email');
    } catch (\Exception $e) {
        expect($e)->toBeInstanceOf(\Exception::class);
    }
});

test('user email must be unique', function () {
    User::create([
        'name' => 'User 1',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    try {
        $user2 = new User([
            'name' => 'User 2',
            'email' => 'test@example.com',
            'password' => 'password456',
        ]);
        $user2->save();
        expect(false)->toBeTrue('Should have thrown an exception for duplicate email');
    } catch (\Exception $e) {
        expect($e)->toBeInstanceOf(\Exception::class);
    }
});

test('user password is hashed', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    expect($user->password)->not->toBe('password123');
    expect(password_verify('password123', $user->password))->toBeTrue();
});

test('user can be deleted', function () {
    $user = User::factory()->create();

    $user->delete();

    expect(User::find($user->id))->toBeNull();
});

test('user has timestamps', function () {
    $user = User::factory()->create();

    expect($user->created_at)->not->toBeNull();
    expect($user->updated_at)->not->toBeNull();
    expect($user->created_at)->toBeInstanceOf(\Carbon\Carbon::class);
    expect($user->updated_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('user can be updated', function () {
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    $user->update([
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    expect($user->fresh()->name)->toBe('Updated Name');
    expect($user->fresh()->email)->toBe('updated@example.com');
});

test('user email can contain subdomains', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test.user@subdomain.example.com',
        'password' => 'password123',
    ]);

    expect($user->email)->toBe('test.user@subdomain.example.com');
});

test('user name can contain special characters', function () {
    $user = User::create([
        'name' => 'José María García',
        'email' => 'jose@example.com',
        'password' => 'password123',
    ]);

    expect($user->name)->toBe('José María García');
});

test('user can have very long name', function () {
    $longName = str_repeat('A', 200);

    $user = User::create([
        'name' => $longName,
        'email' => 'longname@example.com',
        'password' => 'password123',
    ]);

    expect($user->name)->toBe($longName);
});

test('user can check password with check method', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    expect(\Hash::check('password123', $user->password))->toBeTrue();
    expect(\Hash::check('wrongpassword', $user->password))->toBeFalse();
});

test('user has api tokens relationship', function () {
    $user = User::factory()->create();

    // Create a token
    $token = $user->createToken('test-token');

    expect($user->tokens)->toHaveCount(1);
    expect($user->tokens->first()->name)->toBe('test-token');
});

test('user can have email verified at timestamp', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->email_verified_at)->not->toBeNull();
    expect($user->email_verified_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('user can mark email as verified', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $user->markEmailAsVerified();

    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

test('user can check if email is verified', function () {
    $user1 = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $user2 = User::factory()->create([
        'email_verified_at' => null,
    ]);

    expect($user1->hasVerifiedEmail())->toBeTrue();
    expect($user2->hasVerifiedEmail())->toBeFalse();
});
