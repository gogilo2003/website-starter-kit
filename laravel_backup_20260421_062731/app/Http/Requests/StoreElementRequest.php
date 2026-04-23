<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreElementRequest extends FormRequest
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
            'title' => 'required|string|unique:elements,title',
            'type' => 'required|string|in:text,multiline,richtext',
            'content' => 'required|string|unique:elements,content',
            'icon' => 'nullable|string',
            'picture' => 'nullable|file|image',
        ];
    }
}
