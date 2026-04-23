<?php

namespace App\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class StoreDownloadCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:download_categories,slug',
            'description' => 'nullable|string',
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
