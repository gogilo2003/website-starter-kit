<?php

namespace Gogilo\Quotes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuoteProduct extends FormRequest
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
            "id" => ['required', "integer", "exists:quote_product,id"],
            "quantity" => ["nullable", "integer"],
            "price" => ["nullable", "numeric"],
        ];
    }
}
