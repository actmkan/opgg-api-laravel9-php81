<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'is_notice',
        'is_all_notice',
        'talk_id',
        'channel_id',
        'user_id',
        'title',
        'content',
        'like_count',
        'unlike_count',
        'view_count'
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->HasMany(Comment::class);
    }

    public function onLike(): HasMany
    {
        return $this->HasMany(ArticleLike::class);
    }

    public function onUnlike(): HasMany
    {
        return $this->HasMany(ArticleLike::class);
    }

    public function wards(): HasMany
    {
        return $this->HasMany(ArticleWard::class);
    }
}
