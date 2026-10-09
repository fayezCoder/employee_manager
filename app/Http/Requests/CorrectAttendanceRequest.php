<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'field' => ['required', 'in:check_in,check_out'],
            'value' => ['required', 'date'],
            'reason' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }
}
