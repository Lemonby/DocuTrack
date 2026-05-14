<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsulanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'nama_pengusul' => ['required', 'string', 'max:150'],
            'nim_nip' => ['required', 'string', 'max:20'],
            'jurusan' => ['required', 'string', 'exists:jurusans,nama_jurusan'],
            'prodi' => ['required', 'string', 'exists:prodis,nama_prodi'],
            'wadir_tujuan' => ['required', 'integer', 'exists:wadirs,wadir_id'],
            'indikator_kinerja' => ['nullable', 'string'],
            'gambaran_umum' => ['nullable', 'string'],
            'penerima_manfaat' => ['nullable', 'string'],
            'metode_pelaksanaan' => ['nullable', 'string'],
            'tahapan' => ['nullable', 'array'],
            'tahapan.*' => ['string'],
            'indikator' => ['nullable', 'array'],
            'indikator.*.nama' => ['required_with:indikator', 'string'],
            'indikator.*.bulan' => ['nullable', 'integer', 'between:1,12'],
            'indikator.*.target' => ['nullable', 'integer', 'between:0,100'],
            'rab_data' => ['required', 'array', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'rab_data.required' => 'RAB minimal harus memiliki 1 item.',
            'rab_data.min' => 'RAB minimal harus memiliki 1 item.',
        ];
    }
}
