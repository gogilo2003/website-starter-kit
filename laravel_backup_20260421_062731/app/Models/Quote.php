<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'company',
        'message',
        'status',
        'view_count',
        'last_viewed_at'
    ];

    protected $casts = [
        'last_viewed_at' => 'datetime',
    ];

    protected $appends = ['total_amount'];

    protected static function boot()
    {
        parent::boot();

        // Generate unique code when creating a new quote
        static::creating(function ($quote) {
            if (empty($quote->code)) {
                $quote->code = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique tracking code
     * Format: Q-XXXXXX where X is alphanumeric
     */
    public static function generateUniqueCode(): string
    {
        do {
            // You can customize the format as needed
            $code = 'Q-' . strtoupper(Str::random(6));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Get the public tracking URL for the quote
     */
    public function getTrackingUrlAttribute(): string
    {
        return route('quote-track', ['code' => $this->code]);
    }

    /**
     * Relationship with products
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'quote_product')
            ->using(QuoteProduct::class)
            ->withPivot('quantity', 'price', 'notes')
            ->withTimestamps();
    }

    /**
     * Get all of the items for the Quote
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuoteProduct::class);
    }

    /**
     * Increment view count when quote is viewed
     */
    public function trackView(): void
    {
        $this->increment('view_count');
        $this->last_viewed_at = now();
        $this->save();
    }

    /**
     * Calculate total quote amount
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity *  $item->price;
        });
    }

    /**
     * Scope for pending quotes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for active quotes
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'sent', 'viewed']);
    }

    /**
     * Find quote by tracking code
     */
    public static function findByCode(string $code)
    {
        return static::where('code', $code)->first();
    }
}
