<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'author', 'isbn', 'published_date', 'description', 'image_url',
    ];

    /**
     * 書籍に紐づくジャンルを取得
     *
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)->withTimestamps();
    }

    /**
     * 書籍に紐づくレビューを取得
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * 書籍をお気に入りしたユーザーを取得
     *
     * @return BelongsToMany
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * 検索・絞り込み・並び替えを適用するクエリスコープ
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $query->when($filters['keyword'] ?? null, function (Builder $q, string $keyword) {
            $q->where(function (Builder $sub) use ($keyword) {
                $sub->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        });

        $query->when($filters['genre'] ?? null, function (Builder $q, int|string $genreId) {
            $q->whereHas('genres', function (Builder $sub) use ($genreId) {
                $sub->where('genres.id', $genreId);
            });
        });

        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'oldest';
                $query->orderBy('created_at');
                break;
            case 'rating';
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'title';
                $query->orderBy('title');
                break;
            case 'newest';
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }
}
