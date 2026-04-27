<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\ResultStageDto;
use App\Enums\EventStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Валидация запроса для фиксации результатов стадии
 */
class ResultStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $action = $this->input('action');

        $baseRules = [
            'action' => ['required', Rule::in(['next_stage', 'complete', 'fail'])],
            'comment' => 'nullable|string|max:1000',
        ];

        if ($action === 'next_stage') {
            $baseRules['dateEvent'] = 'required|date|after:now';
            $baseRules['eventStage'] = ['required', Rule::in(EventStage::values())];
        }

        return $baseRules;
    }

    public function messages(): array
    {
        return [
            'dateEvent.required' => 'Укажите дату и время следующего этапа',
            'dateEvent.after' => 'Дата и время должны быть в будущем',
            'comment.max' => 'Превышена длина комментария',
            'eventStage.required' => 'Выберите этап собеседования',
            'eventStage.in' => 'Выбран недопустимый этап',
            'action.required' => 'Выберите действие.',
            'action.in' => 'Недопустимое действие.',
        ];
    }

    public function toDto(): ResultStageDto
    {
        return ResultStageDto::fromArray($this->validated());
    }
}
