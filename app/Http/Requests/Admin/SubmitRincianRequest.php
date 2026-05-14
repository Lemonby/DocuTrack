<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRincianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kegiatan_id' => ['required', 'integer', 'exists:kegiatans,kegiatan_id'],
            'penanggung_jawab' => ['required', 'string', 'max:100'],
            'nim_nip_pj' => ['required', 'string', 'max:30'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'surat_pengantar' => ['nullable', 'file', 'mimes:doc,docx,pdf', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ];
    }
}
