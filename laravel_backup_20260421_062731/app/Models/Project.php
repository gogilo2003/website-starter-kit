<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    /**
     * Get all of the post's pictures.
     */
    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'picturable');
    }
}
