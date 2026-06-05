<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class WebLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login_email' => ['required', 'email'],
            'login_password' => ['required', 'string'],
            'captcha_code' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login_email.required' => 'Email wajib diisi.',
            'login_email.email' => 'Format email tidak valid.',
            'login_password.required' => 'Password wajib diisi.',
            'captcha_code.required' => 'Kode CAPTCHA wajib diisi.',
        ];
    }
}
