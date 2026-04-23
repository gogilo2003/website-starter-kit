<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsArticle extends Model
{
    use HasFactory;

    /**
     * Get all of the post's pictures.
     */
    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'picturable');
    }

    /**
     * Get the user that owns the NewsArticle
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
