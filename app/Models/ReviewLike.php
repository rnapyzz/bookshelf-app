<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ReviewLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'review_id',
    ];

    //    public function users(): BelongsToMany
    //    {
    //        return $this->belongsToMany(User::class);
    //    }
    //
    //    public function reviews(): BelongsToMany
    //    {
    //        return $this->belongsToMany(Review::class);
    //    }
}
