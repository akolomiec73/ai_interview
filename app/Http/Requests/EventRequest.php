<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\DTO\EventDto;
use Illuminate\Foundation\Http\FormRequest;

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
            'dateInterview' => 'required|date',
            'linkVacantion' => 'required|url',
            'comment' => 'nullable|string|max:250',
        ];
    }

    public function messages(): array
    {
        return [
            'dateInterview.required' => 'Поле "Дата интервью" обязательно для заполнения.',
            'dateInterview.date' => 'Некорректный формат даты',
            'linkVacantion.required' => 'Поле "Ссылка на вакансию" обязательно для заполнения.',
            'linkVacantion.url' => 'Некорректная ссылка',
            'comment.max' => 'Комментарий не может быть длиннее 250 символов.',
        ];
    }

    public function toDto(): EventDto
    {
        return EventDto::fromArray($this->validated());
    }
}
