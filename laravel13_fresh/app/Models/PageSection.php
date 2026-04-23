<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PageSection extends Model
{
    use HasFactory;

    /**
     * The elements that belong to the PageSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function elements(): BelongsToMany
    {
        return $this->belongsToMany(Element::class, 'element_page_section', 'element_id', 'page_section_id');
    }
}
