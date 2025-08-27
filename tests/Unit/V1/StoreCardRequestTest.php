<?php

use App\Http\Requests\Api\V1\StoreCardRequest;
use App\Models\Deck;
use App\Models\User;
use Illuminate\Routing\Route;

// This hook runs before every test, ensuring $this->request is always a fresh instance.
beforeEach(function () {
    $this->request = new StoreCardRequest;
});

it('has the correct validation rules', function () {
    $expectedRules = [
        'question' => ['required', 'string', 'min:5'],
        'answer' => ['required', 'string', 'min:1'],
    ];

    expect($this->request->rules())->toEqual($expectedRules);
});

it('passes validation with valid data', function () {
    $data = [
        'question' => 'What is the capital of France?',
        'answer' => 'Paris',
    ];

    // Notice we use $this->request here
    $validator = validator($data, $this->request->rules());

    expect($validator->fails())->toBeFalse();
});

it('fails validation with invalid data', function (array $data, string $expectedErrorKey) {
    // And here
    $validator = validator($data, $this->request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->toArray())->toHaveKey($expectedErrorKey);
})->with([
    'short question' => [['question' => 'Hi', 'answer' => 'Paris'], 'question'],
    'missing question' => [['answer' => 'Paris'], 'question'],
    'empty answer' => [['question' => 'A valid question?', 'answer' => ''], 'answer'],
    'missing answer' => [['question' => 'A valid question?'], 'answer'],
]);

it('authorizes a user to create a card for their own deck', function () {
    $user = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $user->id]);

    // Use $this->request, which was created by the beforeEach hook
    $this->request->setUserResolver(fn () => $user);
    $this->request = mockRequestWithRoute($this->request, $deck);

    expect($this->request->authorize())->toBeTrue();
});

it('prevents a user from creating a card for another user\'s deck', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $deck = Deck::factory()->create(['user_id' => $otherUser->id]);

    // Use $this->request here as well. No more creating a local $request.
    $this->request->setUserResolver(fn () => $user);
    $this->request = mockRequestWithRoute($this->request, $deck);

    expect($this->request->authorize())->toBeFalse();
});

function mockRequestWithRoute($request, $deck)
{
    $mockRoute = mock(Route::class);
    $mockRoute->shouldReceive('parameter')
        ->with('deck', null)
        ->andReturn($deck);

    $request->setRouteResolver(function () use ($mockRoute) {
        return $mockRoute;
    });

    return $request;
}
