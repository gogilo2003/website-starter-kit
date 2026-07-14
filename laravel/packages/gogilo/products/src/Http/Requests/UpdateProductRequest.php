<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateProductRequest extends FormRequest
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
        return [
            'title' => 'required|string|unique:projects,content,'.$this->id.',id',
            'summary' => 'required|string',
            'description' => 'required|string|unique:projects,content,'.$this->id.',id',
            'picture' => 'nullable|file|image',
        ];
    }

    protected function prepareForValidation()
    {
        // Check if summary is empty (null or empty string)
        if (empty($this->summary) && $this->has('description')) {
            $content = $this->input('description');

            // Strip HTML tags and limit to 25 words OR 160 characters
            $teaser = Str::words(strip_tags($content), 25, '');

            // If teaser exceeds 160 characters, truncate to 157 characters and add ellipsis
            // if (strlen($teaser) > 160) {
            //     $teaser = Str::limit($teaser, 157, '...');
            // }

            $this->merge(['summary' => $teaser]);
        }

        return parent::prepareForValidation();
    }
}
