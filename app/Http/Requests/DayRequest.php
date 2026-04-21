<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса для отображения событий дня.
 *
 * @bodyParam date string required Дата в формате Y-m-d. Example: 2025-04-20
 */
class DayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'date' => $this->route('date'),
        ]);
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Дата обязательна',
            'date.date_format' => 'Дата должна быть в формате ГГГГ-ММ-ДД',
        ];
    }
}
