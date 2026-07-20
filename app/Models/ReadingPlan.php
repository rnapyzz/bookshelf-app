<?php

namespace App\Models;

use App\Enums\ReadingPlanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'target_date',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'datetime',
        'status' => ReadingPlanStatus::class,
    ];

    /**
     * 読書計画の作成ユーザーを取得
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 読書計画の紐付き先の書籍情報を取得
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
