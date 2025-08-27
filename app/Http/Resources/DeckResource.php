<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DeckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_public' => $this->public,
            'is_pinned' => $this->is_pinned,
            'cover_image_url' => $this->whenNotNull(
                $this->cover_image_path,
                Storage::disk('public')->url($this->cover_image_path)
            ),
            'cards_count' => $this->whenCounted('cards'),
            'cards' => CardResource::collection($this->whenLoaded('cards')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
