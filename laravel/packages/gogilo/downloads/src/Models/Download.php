<?php

namespace Gogilo\Downloads\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Gogilo\Downloads\Database\Factories\DownloadFactory::new();
    }

    protected $fillable = [
        'original_filename',
        'storage_path',
        'file_size',
        'mime_type',
        'disk',
        'metadata',
        'download_count',
    ];

    protected $casts = [
        'metadata' => 'array',
        'download_count' => 'integer',
        'file_size' => 'integer',
    ];

    protected $appends = ['file_url'];

    /**
     * Scope a query to only include downloads within the last 24 hours.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->subDay());
    }

    /**
     * Scope a query to filter by file extension.
     */
    public function scopeByExtension(Builder $query, string $extension): Builder
    {
        return $query->where('original_filename', 'like', '%.' . $extension);
    }

    /**
     * Get the file URL attribute.
     */
    public function getFileUrlAttribute(): string
    {
        return route('downloads.api.download', ['file' => $this->id]);
    }

    /**
     * Get the download count with formatting.
     */
    public function getFormattedDownloadCountAttribute(): string
    {
        $count = $this->download_count;
        if ($count >= 1000000) {
            return number_format($count / 1000000, 1) . 'M';
        }
        if ($count >= 1000) {
            return number_format($count / 1000, 1) . 'K';
        }
        return (string) $count;
    }

    /**
     * Get the file size with formatting.
     */
    public function getFormattedSizeAttribute(): string
    {
        $size = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}