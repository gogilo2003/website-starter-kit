<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateElementRequest extends FormRequest
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
            "id" => "required|exists:elements,id",
            'title' => 'required|string|unique:elements,title,' . $this->id . ',id',
            'type' => 'required|string|in:text,multiline,richtext',
            'content' => 'required|string|unique:elements,content,' . $this->id . ',id',
            'icon' => 'nullable|string',
            'picture' => 'nullable|file|image',
        ];
    }
}
