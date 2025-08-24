<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var \App\Models\User $user */
        $user = $event->user;

        // Revoke any old tokens to ensure a clean session
        $user->tokens()->delete();

        // Create a new token
        $token = $user->createToken('spa-token')->plainTextToken;

        // Flash the token to the session. It will only be available for the next request.
        session(['api_token' => $token]);
    }
}