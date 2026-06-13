<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLpjRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kegiatan_id' => ['required', 'exists:kegiatans,kegiatan_id'],
            'uraian' => ['nullable', 'array'],
            'uraian.*.*' => ['required', 'string', 'max:255'],
            'rincian' => ['nullable', 'array'],
            'rincian.*.*' => ['required', 'string', 'max:255'],
            'vol1' => ['nullable', 'array'],
            'vol1.*.*' => ['required', 'numeric', 'min:0'],
            'sat1' => ['nullable', 'array'],
            'sat1.*.*' => ['required', 'string', 'max:50'],
            'vol2' => ['nullable', 'array'],
            'vol2.*.*' => ['required', 'numeric', 'min:0'],
            'sat2' => ['nullable', 'array'],
            'harga' => ['nullable', 'array'],
            'harga.*.*' => ['required', 'numeric', 'min:0'],
            'realisasi' => ['nullable', 'array'],
            'realisasi.*.*' => ['required', 'numeric', 'min:0'],
            'bukti' => ['nullable', 'array'],
            'bukti.*.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'lpj_item_id' => ['nullable', 'array'],
            'realisasi_tanggal_mulai' => ['required', 'date'],
            'realisasi_tanggal_selesai' => ['required', 'date', 'after_or_equal:realisasi_tanggal_mulai'],
        ];
    }

    public function messages(): array
    {
        return [
            'kegiatan_id.required' => 'ID kegiatan wajib diisi.',
            'kegiatan_id.exists' => 'Kegiatan tidak valid.',
            'realisasi_tanggal_mulai.required' => 'Tanggal mulai realisasi wajib diisi.',
            'realisasi_tanggal_mulai.date' => 'Format tanggal mulai realisasi tidak valid.',
            'realisasi_tanggal_selesai.required' => 'Tanggal selesai realisasi wajib diisi.',
            'realisasi_tanggal_selesai.date' => 'Format tanggal selesai realisasi tidak valid.',
            'realisasi_tanggal_selesai.after_or_equal' => 'Tanggal selesai realisasi harus sama atau setelah tanggal mulai.',
            'uraian.*.*.required' => 'Semua item uraian belanja harus diisi.',
            'rincian.*.*.required' => 'Semua item rincian belanja harus diisi.',
            'vol1.*.*.required' => 'Volume 1 belanja harus diisi.',
            'vol1.*.*.numeric' => 'Volume 1 belanja harus berupa angka.',
            'vol1.*.*.min' => 'Volume 1 belanja minimal 0.',
            'sat1.*.*.required' => 'Satuan 1 belanja harus diisi.',
            'vol2.*.*.required' => 'Volume 2 belanja harus diisi.',
            'vol2.*.*.numeric' => 'Volume 2 belanja harus berupa angka.',
            'vol2.*.*.min' => 'Volume 2 belanja minimal 0.',
            'harga.*.*.required' => 'Harga belanja harus diisi.',
            'harga.*.*.numeric' => 'Harga belanja harus berupa angka.',
            'harga.*.*.min' => 'Harga belanja minimal 0.',
            'realisasi.*.*.required' => 'Realisasi belanja harus diisi.',
            'realisasi.*.*.numeric' => 'Realisasi belanja harus berupa angka.',
            'realisasi.*.*.min' => 'Realisasi belanja minimal 0.',
            'bukti.*.*.file' => 'Bukti LPJ harus berupa file.',
            'bukti.*.*.mimes' => 'Format file bukti LPJ harus berupa JPG atau PNG.',
            'bukti.*.*.max' => 'Ukuran file bukti LPJ maksimal 2MB.',
        ];
    }
}
