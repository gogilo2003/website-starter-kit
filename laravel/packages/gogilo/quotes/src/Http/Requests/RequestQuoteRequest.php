<?php

namespace Gogilo\Quotes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'message.max' => 'Message cannot exceed 1000 characters.',
            'products.*.product_id.exists' => 'Selected product does not exist.',
            'products.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
