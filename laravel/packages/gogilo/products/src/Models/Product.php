<?php

namespace Gogilo\Products\Models;

use App\Models\Picture;
use App\Models\Quote;
use App\Support\Util;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $casts = [
        'published' => 'boolean',
        'front' => 'boolean',
        'price' => 'decimal:2',
    ];

    protected $appends = [
        'picture',
        'features',
    ];

    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'picturable');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function getPictureAttribute(): ?string
    {
        $picture = $this->pictures->where('is_primary', 1)->first() ?? $this->pictures->first() ?? null;

        return Util::pictureUrl($picture?->name) ?? url('/images/placeholder-product.png');
    }

    public function getFeaturesAttribute($value)
    {
        $features = $value ? json_decode($value, true) : [];

        return collect($features)
            ->mapWithKeys(function ($value, $key) {
                return [
                    Str::headline($key) => $value,
                ];
            })
            ->toArray();
    }

    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = $value ? json_encode($value) : null;
    }

    public function quotes(): BelongsToMany
    {
        return $this->belongsToMany(Quote::class, 'quote_product')
            ->withPivot('quantity', 'price', 'notes')
            ->withTimestamps();
    }
}
