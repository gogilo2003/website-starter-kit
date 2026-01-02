<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Element extends Model
{
    use HasFactory;

    /**
     * The page_sections that belong to the Element
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function page_sections(): BelongsToMany
    {
        return $this->belongsToMany(PageSection::class, 'element_page_section', 'page_section_id', 'element_id');
    }
}
