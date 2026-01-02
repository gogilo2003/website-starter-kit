<?php

namespace App\Models;

use App\Support\Util;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Picture extends Model
{
    use HasFactory;

    protected $appends = ['url'];

    /**
     * Get the parent picturable model (post or video).
     */
    public function picturable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute()
    {
        return Util::pictureUrl($this->name);
    }
}
