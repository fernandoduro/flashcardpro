<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Study extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'deck_id', 'completed_at'];

    /**
     * Get the user that owns the study.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the deck that owns the study.
     */
    public function deck()
    {
        return $this->belongsTo(Deck::class);
    }

    /**
     * Get the study results for the study.
     */
    public function results()
    {
        return $this->hasMany(StudyResult::class);
    }
}
