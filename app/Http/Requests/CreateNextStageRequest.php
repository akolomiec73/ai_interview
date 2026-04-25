<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса для создания следующей стадии ивента
 */
class CreateNextStageRequest extends FormRequest
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
            'dateInterview' => 'required|date|after:now',
            'comment' => 'nullable|string|max:250',
        ];
    }

    public function messages(): array
    {
        return [
            'dateInterview.required' => 'Укажите дату и время следующего этапа',
            'dateInterview.after' => 'Дата и время должны быть в будущем',
            'comment.max' => 'Превышена длина комментария',
        ];
    }
}
