<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация запроса для отображения событий месяца.
 */
class EventIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|between:1,12',
        ];
    }
}
