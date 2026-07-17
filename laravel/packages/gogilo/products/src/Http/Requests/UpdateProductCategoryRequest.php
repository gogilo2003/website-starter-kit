<?php

namespace Gogilo\Products\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateProductCategoryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('category');

        return [
            'slug' => "required|string|unique:product_categories,slug,{$id}",
            'name' => 'required|string',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'picture' => 'nullable|file|image',
        ];
    }

    public function prepareForValidation()
    {
        $slug = $this->slug;
        if (! $slug) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }
}
