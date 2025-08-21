<?php

namespace App\Policies;

use App\Models\Deck; // <-- Import the Deck model
use App\Models\Study;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudyPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Deck $deck): bool
    {
        // A user can create a study session for a deck if they own that deck.
        return $user->id === $deck->user_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Study $study): bool
    {
        // A user can update a study session if they own it.
        return $user->id === $study->user_id;
    }
}