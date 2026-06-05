<?php

namespace App\Http\Requests\Workflow;

use Illuminate\Foundation\Http\FormRequest;

class ReviseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'komentar' => ['required', 'string', 'min:5'],
            'field_comments' => ['nullable', 'array'],
            'field_comments.*.target_kolom' => ['required_with:field_comments', 'string'],
            'field_comments.*.komentar' => ['required_with:field_comments', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'komentar.required' => 'Komentar revisi wajib diisi.',
            'komentar.min' => 'Komentar revisi minimal 5 karakter.',
            'field_comments.*.target_kolom.required_with' => 'Target kolom revisi wajib diisi.',
            'field_comments.*.komentar.required_with' => 'Komentar pada field revisi wajib diisi.',
        ];
    }
}
