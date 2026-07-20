<?php

namespace App\Http\Requests\ReadingPlan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReadingPlanRequest extends FormRequest
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
            'target_date' => ['required', 'date'],
        ];
    }

    /**
     * バリデーションエラー時の日本語メッセージ
     */
    public function messages(): array
    {
        return [
            'target_date.required' => '期日を設定してください',
            'target_date.date' => '期日は日付形式で入力してください',
        ];
    }
}
