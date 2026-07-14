<?php

namespace App\Models;

use Gogilo\Products\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class QuoteProduct extends Pivot
{
    protected $table = 'quote_product';

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Calculate total price automatically
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($pivot) {});
    }

    /**
     * Get the quote that owns the QuoteProduct
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the product that owns the QuoteProduct
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
