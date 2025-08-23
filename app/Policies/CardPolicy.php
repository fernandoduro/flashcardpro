<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CardPolicy
{
    
    public function delete(User $user, Card $card): bool
    {
        return $user->id === $card->user_id;
    }
}
