<?php

namespace Gogilo\Downloads\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateDownloadCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:download_categories,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:download_categories,slug,'.$this->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => strtolower(Str::slug($this->name)),
        ]);
    }
}
