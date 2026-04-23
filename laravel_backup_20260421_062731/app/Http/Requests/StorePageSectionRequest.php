<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePageSectionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust this based on your authorization logic
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
            'title' => 'required|string|max:255|unique:page_sections,name',
            'name' => 'required|string|max:255|unique:page_sections,name',
            'description' => 'nullable|string',
        ];
    }
}
