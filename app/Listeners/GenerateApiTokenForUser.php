<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class GenerateApiTokenForUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the login event by generating an API token.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        $user->tokens()->delete();
        $token = $user->createToken('spa-token')->plainTextToken;

        session(['api_token' => $token]);
    }
}
