<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\EventStage;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'dateEvent' => 'required|date|after:now',
            'comment' => 'nullable|string|max:250',
            'eventStage' => ['required', Rule::in(EventStage::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'dateEvent.required' => 'Укажите дату и время следующего этапа',
            'dateEvent.after' => 'Дата и время должны быть в будущем',
            'comment.max' => 'Превышена длина комментария',
            'eventStage.required' => 'Выберите этап собеседования',
            'eventStage.in' => 'Выбран недопустимый этап',
        ];
    }
}
