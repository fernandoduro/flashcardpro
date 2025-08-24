<?php
namespace App\Policies;
use App\Models\Deck;
use App\Models\User;

class DeckPolicy
{
     /**
     * Determine whether the user can view the deck.
     *
     * A user can view a deck if they own it.
     */
    public function view(User $user, Deck $deck): bool
    {
        return $user->id === $deck->user_id;
    }

    /**
     * Determine whether the user can create decks.
     *
     * Any authenticated user can create a deck.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the deck.
     *
     * A user can update a deck if they own it.
     */
    public function update(User $user, Deck $deck): bool
    {
        return $user->id === $deck->user_id;
    }

    /**
     * Determine whether the user can delete the deck.
     *
     * A user can delete a deck if they own it.
     */
    public function delete(User $user, Deck $deck): bool
    {
        return $user->id === $deck->user_id;
    }
}