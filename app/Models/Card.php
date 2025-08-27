<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'question', 'answer', 'deck_id'];

    /**
     * Get the user that owns the card.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the deck that owns the card.
     */
    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }
}
