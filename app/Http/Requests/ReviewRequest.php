<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーション実行前の準備
     */
    protected function prepareForValidation(): void
    {
        $book = $this->route('book');
        $bookId = $book instanceof Book ? $book->id : $book;

        $this->merge([
            'book_id' => $bookId,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->book_id;

        return [
            'rating' => ['required', 'integer', 'in:1,2,3,4,5'],
            'comment' => ['required', 'string', 'max:1000'],
            'book_id' => [
                Rule::unique('reviews', 'book_id')->where(function ($query) use ($bookId) {
                    return $query->where('book_id', $bookId)->where('user_id', auth()->id());
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => '評価を選択してください',
            'rating.in' => '評価は1-5の整数で入力してください',
            'comment.required' => 'コメントは必須です',
            'comment.max' => 'コメントは1000文字以内で入力してください',
            'book_id.unique' => '既にレビューを投稿済みです',
        ];
    }
}
