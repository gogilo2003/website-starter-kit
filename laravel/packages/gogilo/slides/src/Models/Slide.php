<?php

namespace Gogilo\Slides\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $fillable = [
        'picture',
        'title',
        'caption',
        'published',
        'media_type',
    ];
}
