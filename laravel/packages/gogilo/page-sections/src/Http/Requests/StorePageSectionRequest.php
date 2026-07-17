<?php

namespace Gogilo\PageSections\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePageSectionRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255|unique:page_sections,title',
            'name' => 'required|string|max:255|unique:page_sections,name',
            'description' => 'nullable|string',
        ];
    }
}
