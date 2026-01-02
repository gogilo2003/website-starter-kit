<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    use HasFactory;
    protected $fillable = [
        'url',
        'route_name',
        'visitor_id',
        'ip_address',
        'user_agent',
        'referrer',
        'user_id'
    ];
}
