<?php

namespace Gogilo\PageSections\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'description',
    ];

    /**
     * The elements that belong to the PageSection
     */
    public function elements(): BelongsToMany
    {
        return $this->belongsToMany(Element::class, 'element_page_section', 'element_id', 'page_section_id');
    }
}
