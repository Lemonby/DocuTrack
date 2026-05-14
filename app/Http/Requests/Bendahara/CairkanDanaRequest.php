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
}
