<?php

namespace App\Models;

use App\Support\Util;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'features'
    ];


    /**
     * Get all of the post's pictures.
     */
    public function pictures(): MorphMany
    {
        return $this->morphMany(Picture::class, 'picturable');
    }

    /**
     * Get the category that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /**
     * Get the brand that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Accessor to picture attribute
     * Transform get the primary picture or default to the first picture and return a valid url for the picture
     * @return string | null
     */
    public function getPictureAttribute(): string|null
    {
        $picture = $this->pictures->where('is_primary', 1)->first() ?? $this->pictures->first() ?? null;
        return Util::pictureUrl($picture?->name) ?? url('/images/placeholder-product.png');
    }

    /**
     * Accessor to decode features JSON automatically.
     */
    public function getFeaturesAttribute($value)
    {
        $features = $value ? json_decode($value, true) : [];

        return collect($features)
            ->mapWithKeys(function ($value, $key) {
                return [
                    Str::headline($key) => $value
                ];
            })
            ->toArray();
    }

    /**
     * Mutator to encode features JSON automatically.
     */
    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = $value ? json_encode($value) : null;
    }

    /**
     * The quotes that belong to the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function quotes()
    {
        return $this->belongsToMany(Quote::class, 'quote_product')
            ->withPivot('quantity', 'price', 'notes')
            ->withTimestamps();
    }
}
