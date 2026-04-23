<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncElementsRequest extends FormRequest
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
            'page_section' => ['required', 'integer', 'exists:page_sections,id'],
            'elements' => ['required', 'array'],
            'elements.*' => ['integer', 'exists:elements,id']
        ];
    }
}
