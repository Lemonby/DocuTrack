<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc,strict', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:128'],
            'captcha_code' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'captcha_code.required' => 'Kode CAPTCHA wajib diisi.',
        ];
    }
}
