<?php

namespace App\Http\Requests\Verifikator;

use Illuminate\Foundation\Http\FormRequest;

class ApproveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode_mak' => ['required', 'string'],
            'dana_disetujui' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}
