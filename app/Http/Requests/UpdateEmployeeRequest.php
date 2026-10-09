<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $this->route('employee')->id],
            'department_id' => ['required', 'exists:departments,id'],
            'role' => ['required', 'in:admin,employee'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
