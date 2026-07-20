<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'published_date' => $this->published_date,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'reviews_avg_rating' => $this->whenLoaded('reviews_avg_rating', fn () => round($this->reviews_avg_rating, 1)),
            'reviews_count' => $this->whenCounted('reviews'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
