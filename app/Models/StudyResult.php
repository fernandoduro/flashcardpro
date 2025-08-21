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
}