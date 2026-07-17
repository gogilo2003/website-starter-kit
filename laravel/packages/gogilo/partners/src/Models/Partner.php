<?php

namespace Gogilo\Partners\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'logo',
        'website',
        'description',
        'published',
        'front',
    ];
}
