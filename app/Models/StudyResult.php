<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyResult extends Model
{
    use HasFactory;

    protected $fillable = ['study_id', 'card_id', 'is_correct'];

    public function study()
    {
        return $this->belongsTo(Study::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Get the user that owns this study result through the study
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, Study::class, 'id', 'id', 'study_id', 'user_id');
    }

    /**
     * Get the deck that this study result belongs to through the study
     */
    public function deck()
    {
        return $this->hasOneThrough(Deck::class, Study::class, 'id', 'id', 'study_id', 'deck_id');
    }
}