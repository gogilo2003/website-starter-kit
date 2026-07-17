<?php

namespace Gogilo\PageSections\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdatePageSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => Str::slug($this->title),
        ]);
    }

    public function rules()
    {
        $pageSectionId = $this->route('page_section')?->id ?? $this->route('page_section') ?? $this->id;

        return [
            'id' => 'required|integer|exists:page_sections,id',
            'title' => 'required|string|max:255',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('page_sections', 'name')->ignore($pageSectionId),
            ],
            'description' => 'nullable|string',
        ];
    }
}
