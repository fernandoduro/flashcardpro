<?php

namespace App\Policies;

use App\Models\Deck;
use App\Models\Study;
use App\Models\User;

class StudyPolicy
{
    /**
     * Determine whether the user can view the study session.
     *
     * A user can view a study session if they own it.
     */
    public function view(User $user, Study $study): bool
    {
        return $user->id === $study->user_id;
    }

    /**
     * Determine whether the user can create a new study session for a given deck.
     *
     * A user can create a study session for a deck if they own that deck.
     */
    public function create(User $user, Deck $deck): bool
    {
        return $user->id === $deck->user_id;
    }

    /**
     * Determine whether the user can update the study session (e.g., mark as complete).
     *
     * A user can update a study session if they own it.
     */
    public function update(User $user, Study $study): bool
    {
        return $user->id === $study->user_id;
    }

    /**
     * Determine whether the user can delete the study session.
     *
     * A user can delete a study session if they own it.
     */
    public function delete(User $user, Study $study): bool
    {
        return $user->id === $study->user_id;
    }
}
