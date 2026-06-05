<?php

namespace App\Http\Requests\Bendahara;

use Illuminate\Foundation\Http\FormRequest;

class CairkanDanaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kegiatan_id' => ['required', 'integer', 'exists:kegiatans,kegiatan_id'],
            'metode' => ['required', 'in:penuh,bertahap'],
            'jumlah' => ['required_if:metode,penuh', 'numeric', 'min:0'],
            'tanggal' => ['required_if:metode,penuh', 'date'],
            'tahapan' => ['required_if:metode,bertahap', 'array', 'min:1'],
            'tahapan.*.tanggal' => ['required_with:tahapan', 'date'],
            'tahapan.*.termin' => ['nullable', 'string'],
            'tahapan.*.nominal' => ['required_with:tahapan', 'numeric', 'min:1'],
            'catatan' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'kegiatan_id.required' => 'ID kegiatan wajib diisi.',
            'kegiatan_id.exists' => 'Kegiatan tidak valid.',
            'metode.required' => 'Metode pencairan wajib dipilih.',
            'metode.in' => 'Metode pencairan harus berupa "penuh" atau "bertahap".',
            'jumlah.required_if' => 'Jumlah nominal pencairan wajib diisi jika metode pencairan penuh.',
            'jumlah.numeric' => 'Jumlah nominal harus berupa angka.',
            'jumlah.min' => 'Jumlah nominal minimal 0.',
            'tanggal.required_if' => 'Tanggal pencairan wajib diisi jika metode pencairan penuh.',
            'tanggal.date' => 'Format tanggal pencairan tidak valid.',
            'tahapan.required_if' => 'Data tahapan wajib diisi jika metode pencairan bertahap.',
            'tahapan.min' => 'Data tahapan minimal harus memiliki 1 item.',
            'tahapan.*.tanggal.required_with' => 'Tanggal pada tahapan wajib diisi.',
            'tahapan.*.tanggal.date' => 'Format tanggal pada tahapan tidak valid.',
            'tahapan.*.nominal.required_with' => 'Nominal pada tahapan wajib diisi.',
            'tahapan.*.nominal.numeric' => 'Nominal pada tahapan harus berupa angka.',
            'tahapan.*.nominal.min' => 'Nominal pada tahapan minimal 1.',
        ];
    }
}
