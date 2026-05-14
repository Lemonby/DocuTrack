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
}
