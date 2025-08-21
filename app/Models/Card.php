<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'question', 'answer', 'difficulty'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function decks()
    {
        return $this->belongsToMany(Deck::class, 'card_deck');
    }
}