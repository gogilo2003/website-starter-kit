<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DownloadCategory extends Model
{
    use HasFactory;

    /**
     * Get all of the downloads for the DownloadCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }
}
