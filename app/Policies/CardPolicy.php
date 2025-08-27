<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;

class CardPolicy
{
    /**
     * Determine whether the user can view the card.
     *
     * A user can view a card if they own it.
     */
    public function view(User $user, Card $card): bool
    {
        return $user->id === $card->user_id;
    }

    /**
     * Determine whether the user can create cards.
     *
     * Any authenticated user can create a card.
     * The ownership will be assigned in the controller.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the card.
     *
     * A user can update a card if they own it.
     */
    public function update(User $user, Card $card): bool
    {
        return $user->id === $card->user_id;
    }

    /**
     * Determine whether the user can delete the card.
     *
     * A user can delete a card if they own it.
     */
    public function delete(User $user, Card $card): bool
    {
        return $user->id === $card->user_id;
    }
}
