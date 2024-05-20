<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookPutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|max:100',
            'price' => 'numeric|min:1|max:999999',
            'author_ids' => 'required|array',
            'author_ids.*' => 'required|exists:authors,id'
        ];
    }
}
