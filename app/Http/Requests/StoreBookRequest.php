<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'regex:/^[0-9]{13}$/', 'unique:books,isbn'],
            'published_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'genres' => ['required', 'array', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です',
            'title.max' => 'タイトルは255文字以内で入力してください',
            'author.required' => '著者は必須です',
            'author.max' => '著者は255文字以内で入力してください',
            'isbn.regex' => 'ISBN-13は13桁の数字で入力してください',
            'isbn.unique' => 'このISBN-13は既に登録されています',
            'published_date.date' => '有効な日付形式で入力してください',
            'image_url.url' => '有効なURL形式で入力してください',
            'image_url.max' => '画像URLは255文字以内で入力してください',
            'genres.required' => 'ジャンルを少なくとも1つ選択してください',
        ];
    }
}
