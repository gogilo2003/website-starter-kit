<?php

namespace Gogilo\News\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsArticleRequest extends FormRequest
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
            "title" => "required|string|unique:news_articles,title",
            "content" => "required|string",
            "picture" => "nullable|file|image",
        ];
    }
}
