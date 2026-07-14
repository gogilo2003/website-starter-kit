<?php

namespace Gogilo\Products\Models;

use App\Support\Util;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $appends = ['logo_url'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getLogoUrlAttribute()
    {
        return Util::pictureUrl($this->logo);
    }
}
