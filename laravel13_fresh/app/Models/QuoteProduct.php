<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the product that owns the QuoteProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
