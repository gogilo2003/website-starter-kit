<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisitStat extends Model
{
    use HasFactory;
    protected $fillable = [
        'url',
        'route_name',
        'date',
        'unique_visits',
        'total_visits'
    ];

    protected $casts = [
        'date' => 'date'
    ];
}
