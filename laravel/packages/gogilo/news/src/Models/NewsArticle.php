<?php

namespace Gogilo\News\Models;

use App\Models\Picture;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'published',
        'front',
        'user_id',
    ];

    /**
     * Get all of the news article's pictures.
     */
    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'picturable');
    }

    /**
     * Get the user that owns the NewsArticle
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
