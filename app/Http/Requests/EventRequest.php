<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\CreateEventDto;
use App\Enums\EventStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Валидация запроса для добавления события
 */
class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dateEvent' => 'required|date',
            'linkVacancy' => 'required|url',
            'comment' => 'nullable|string|max:250',
            'eventStage' => ['required', Rule::in(EventStage::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'dateEvent.required' => 'Поле "Дата интервью" обязательно для заполнения.',
            'dateEvent.date' => 'Некорректный формат даты',
            'linkVacancy.required' => 'Поле "Ссылка на вакансию" обязательно для заполнения.',
            'linkVacancy.url' => 'Некорректная ссылка',
            'comment.max' => 'Комментарий не может быть длиннее 250 символов.',
            'eventStage.required' => 'Выберите этап собеседования',
            'eventStage.in' => 'Выбран недопустимый этап',
        ];
    }

    public function toDto(): CreateEventDto
    {
        return CreateEventDto::fromArray($this->validated());
    }
}
