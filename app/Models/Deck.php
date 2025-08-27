<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'public', 'cover_image_path', 'is_pinned'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function studies()
    {
        return $this->hasMany(Study::class);
    }

    /**
     * Scope a query to only include public decks.
     */
    public function scopePublic($query)
    {
        return $query->where('public', true);
    }

    /**
     * Scope a query to only include decks owned by a specific user.
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include recently created decks.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope a query to order by most studied decks.
     */
    public function scopeMostStudied($query)
    {
        return $query->withCount('studies')
            ->having('studies_count', '>', 0)
            ->orderByDesc('studies_count');
    }

    /**
     * Scope a query to include decks with a minimum number of cards.
     */
    public function scopeWithMinimumCards($query, $minCards = 1)
    {
        return $query->has('cards', '>=', $minCards);
    }
}
