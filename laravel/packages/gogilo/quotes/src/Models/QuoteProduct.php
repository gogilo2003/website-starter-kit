<?php

namespace Gogilo\Quotes\Models;

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
