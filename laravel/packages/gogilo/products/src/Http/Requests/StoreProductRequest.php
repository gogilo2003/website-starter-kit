<?php

namespace Gogilo\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'required|integer|exists:product_categories,id',
            'title' => 'required|string|unique:projects,title',
            'summary' => 'nullable|string',
            'description' => 'required|string|unique:projects,content',
            'picture' => 'required|file|image',
        ];
    }

    protected function prepareForValidation()
    {
        if (empty($this->summary) && $this->has('description')) {
            $content = $this->input('description');

            $teaser = Str::words(strip_tags($content), 25, '');

            $this->merge(['summary' => $teaser]);
        }

        return parent::prepareForValidation();
    }
}
