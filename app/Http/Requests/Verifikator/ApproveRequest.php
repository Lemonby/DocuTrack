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

    public function messages(): array
    {
        return [
            'kode_mak.required' => 'Kode MAK wajib diisi.',
            'dana_disetujui.required' => 'Nominal dana yang disetujui wajib diisi.',
            'dana_disetujui.numeric' => 'Nominal dana yang disetujui harus berupa angka.',
            'dana_disetujui.min' => 'Nominal dana yang disetujui minimal 0.',
        ];
    }
}
