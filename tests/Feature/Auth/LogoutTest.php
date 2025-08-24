<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\post;

test('an authenticated user can log out', function () {
    // 1. Create and authenticate a user
    $user = User::factory()->create();
    actingAs($user);

    // 2. Make a POST request to the logout route
    $response = post(route('logout'));

    // 4. Assert that the user is no longer authenticated (is now a guest)
    assertGuest();
});