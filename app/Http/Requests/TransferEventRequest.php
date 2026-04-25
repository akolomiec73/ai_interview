<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferEventRequest extends FormRequest
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
            'dateTransferEvent' => 'required|date|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'dateTransferEvent.required' => 'Укажите дату и время',
            'dateTransferEvent.after' => 'Дата и время должны быть в будущем',
        ];
    }
}
