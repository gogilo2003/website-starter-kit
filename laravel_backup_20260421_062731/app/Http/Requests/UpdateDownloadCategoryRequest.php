<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDownloadCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:download_categories,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:download_categories,slug,' . $this->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
    /**
     * prepare for validation
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'slug' => strtolower(Str::slug($this->name)),
        ]);
    }
}
