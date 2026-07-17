<?php

namespace Gogilo\Partners\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartnerRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:partners,id',
            'title' => 'required|string|unique:partners,title,' . $this->id . ',id',
            'logo' => 'nullable|file|image',
            'website' => 'nullable|string|url',
            'description' => 'nullable|string'
        ];
    }
}
