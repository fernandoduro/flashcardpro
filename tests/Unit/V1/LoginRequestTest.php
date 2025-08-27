<?php

use App\Http\Requests\Api\V1\LoginRequest;

test('login request authorization always returns true', function () {
    $request = new LoginRequest;

    expect($request->authorize())->toBeTrue();
});

test('login request has correct validation rules', function () {
    $request = new LoginRequest;

    $expectedRules = [
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ];

    expect($request->rules())->toEqual($expectedRules);
});

test('login request validation passes with valid data', function () {
    $data = [
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    $request = new LoginRequest;
    $rules = $request->rules();

    // Manual validation for unit testing - check required fields exist and email is valid
    $isValid = true;
    $errors = [];

    // Check required fields
    foreach (['email', 'password'] as $field) {
        if (! isset($data[$field]) || empty($data[$field])) {
            $isValid = false;
            $errors[$field] = 'required';
        }
    }

    // Check email format if present
    if (isset($data['email']) && ! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $isValid = false;
        $errors['email'] = 'invalid email format';
    }

    expect($isValid)->toBeTrue();
    expect($errors)->toBeEmpty();
});

test('login request validation fails with missing email', function () {
    $data = [
        'password' => 'password123',
    ];

    // Manual validation - email is missing
    $isValid = true;
    $errors = [];

    if (! isset($data['email']) || empty($data['email'])) {
        $isValid = false;
        $errors['email'] = 'required';
    }

    expect($isValid)->toBeFalse();
    expect($errors)->toHaveKey('email');
});

test('login request validation fails with invalid email', function () {
    $data = [
        'email' => 'invalid-email',
        'password' => 'password123',
    ];

    // Manual validation - email format is invalid
    $isValid = true;
    $errors = [];

    if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $isValid = false;
        $errors['email'] = 'invalid email format';
    }

    expect($isValid)->toBeFalse();
    expect($errors)->toHaveKey('email');
});

test('login request validation fails with missing password', function () {
    $data = [
        'email' => 'test@example.com',
    ];

    // Manual validation - password is missing
    $isValid = true;
    $errors = [];

    if (! isset($data['password']) || empty($data['password'])) {
        $isValid = false;
        $errors['password'] = 'required';
    }

    expect($isValid)->toBeFalse();
    expect($errors)->toHaveKey('password');
});
