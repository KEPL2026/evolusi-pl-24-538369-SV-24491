<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePomodoroSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'in:focus'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:180'],
        ];
    }
}
