<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($this->user()->user_id, 'user_id')],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ];
    }
}
