<?php

namespace Gogilo\News\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsArticleRequest extends FormRequest
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
            "id" => "required|integer|exists:news_articles,id",
            "title" => "required|string|unique:news_articles,title," . $this->id . ",id",
            "content" => "required|string",
            "picture" => "nullable|file|image",
        ];
    }
}
