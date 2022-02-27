<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['article_id', 'parent_id', 'user_id', 'image_id', 'content', 'like_count', 'unlike_count'];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function childes(): HasMany
    {
        return $this->HasMany(self::class, 'parent_id', 'id');
    }

    public function onLike(): HasMany
    {
        return $this->HasMany(CommentLike::class);
    }

    public function onUnlike(): HasMany
    {
        return $this->HasMany(CommentLike::class);
    }
}
