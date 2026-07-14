<?php

namespace Gogilo\Downloads\Models;

use Gogilo\Downloads\Database\Factories\DownloadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return DownloadFactory::new();
    }

    protected $fillable = [
        'title',
        'slug',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'download_category_id',
        'download_count',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DownloadCategory::class, 'download_category_id', 'id');
    }
}
