<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (empty($this->password)) {
            $this->merge(['password' => null, 'password_confirmation' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password'  => ['nullable', 'confirmed', Password::min(8)],
            'office_id' => ['required', Rule::exists('offices', 'id')->where('is_active', true)],
        ];
    }
}
