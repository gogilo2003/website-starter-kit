<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSlideRequest extends FormRequest
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
            "id" => "required|integer|exists:slides,id",
            "title" => "nullable|string|unique:slides,title," . $this->id . ',id',
            "caption" => "nullable|string",
            "picture" => "nullable|file|mimetypes:video/mp4,image/jpeg,image/png,image/gif",
        ];
    }
}
