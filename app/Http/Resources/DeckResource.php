<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeckResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_public' => $this->public,
            'cards_count' => $this->whenCounted('cards'),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}