<?php

namespace App\Http\Requests\Book;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchBookRequest extends FormRequest
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
            'keyword' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'integer', 'exists:genres,id'],
            'sort' => ['nullable', 'string', 'in:newest,oldest,rating,title'],
        ];
    }

    /**
     * バリデーションエラー時の日本語メッセージ
     */
    public function messages(): array
    {
        return [
            'keyword.max' => 'キーワードは255文字以内で入力してください',
            'genre.integer' => 'ジャンルは数値で入力してください',
            'genre.exists' => 'ジャンルが見つかりません',
            'sort.in' => '並び順は規定値から選択してください',
        ];
    }
}
