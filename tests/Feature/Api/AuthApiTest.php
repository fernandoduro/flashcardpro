<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('user can login via api', function () {
    // Arrange: Create a user with specific credentials
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    // Act: Make the API request to login
    $response = $this->postJson('/api/v1/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    // Assert: Check the response
    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
            ],
            'api_version',
            'timestamp',
        ])
        ->assertJson([
            'success' => true,
            'message' => 'Login successful',
        ]);
});

test('user cannot login with invalid credentials', function () {
    // Arrange: Create a user
    $user = User::factory()->create();

    // Act: Attempt to login with the wrong password
    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    // Assert: Check for validation errors
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('user cannot login with a nonexistent email', function () {
    // Act: Attempt to login with an email that doesn't exist
    $response = $this->postJson('/api/v1/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password',
    ]);

    // Assert: Check for validation errors
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('login requires an email and password', function () {
    // Act: Attempt to login with no data
    $response = $this->postJson('/api/v1/login', []);

    // Assert: Check for validation errors
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

test('user can logout via api', function () {
    // Arrange: Create and log in a user to get a token
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    $loginResponse = $this->postJson('/api/v1/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);
    $token = $loginResponse->json('data.token');

    Sanctum::actingAs($user, ['*']);

    // Act: Make the logout request with the authentication token
    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->postJson('/api/v1/logout');

    // Assert: The logout was successful and the token was deleted
    $response->assertStatus(204);
});

test('logout requires authentication', function () {
    // Act: Attempt to logout without a token
    $response = $this->postJson('/api/v1/logout');

    // Assert: The request is unauthorized
    $response->assertStatus(401);
});

test('login response includes api version and timestamp', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->postJson('/api/v1/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    // Assert
    $response->assertJsonStructure([
        'api_version',
        'timestamp',
    ]);
});
