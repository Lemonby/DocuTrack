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
            'surat_pengantar' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'kegiatan_id.required' => 'ID kegiatan wajib diisi.',
            'kegiatan_id.exists' => 'Kegiatan tidak valid.',
            'penanggung_jawab.required' => 'Nama penanggung jawab wajib diisi.',
            'penanggung_jawab.max' => 'Nama penanggung jawab maksimal 100 karakter.',
            'nim_nip_pj.required' => 'NIM/NIP penanggung jawab wajib diisi.',
            'nim_nip_pj.max' => 'NIM/NIP penanggung jawab maksimal 30 karakter.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'surat_pengantar.mimes' => 'Format file surat pengantar harus berupa pdf.',
            'surat_pengantar.max' => 'Ukuran file surat pengantar maksimal 5MB.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Tangani jika PHP gagal mengunggah file karena melebihi batas upload_max_filesize di php.ini
            if (isset($_FILES['surat_pengantar']) && $_FILES['surat_pengantar']['error'] === UPLOAD_ERR_INI_SIZE) {
                // Hapus pesan kesalahan default "surat pengantar harus berupa file" jika ada
                $validator->errors()->forget('surat_pengantar');
                $validator->errors()->add('surat_pengantar', 'Ukuran file surat pengantar maksimal 5MB.');
            }
        });
    }
}
