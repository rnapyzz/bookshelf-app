<?php

namespace App\Http\Requests\Api\V1;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'integer', 'exists:genres,id'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * バリデーションエラー時の日本語メッセージ
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'keyword.max' => 'キーワードは255文字以内で指定してください',
            'keyword.string' => 'キーワードは文字列で指定してください',
            'genre.integer' => 'ジャンルIDは整数で指定してください',
            'genre.exists' => '指定されたジャンルは存在しません',
            'page.integer' => 'ページ番号は整数で指定してください',
            'page.min' => 'ページ番号は1以上を指定してください',
            'per_page.integer' => '取得件数は整数で指定してください',
            'per_page.min' => '取得件数は1以上を指定してください',
            'per_page.max' => '取得件数は100以下を指定してください',
        ];
    }
}
