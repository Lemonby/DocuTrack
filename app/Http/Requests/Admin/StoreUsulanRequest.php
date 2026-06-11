<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsulanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Decode rab_data if sent as JSON string
        if (is_string($this->rab_data)) {
            $this->merge([
                'rab_data' => json_decode($this->rab_data, true),
            ]);
        }

        // Map flat array indicator inputs to nested indicator array
        if (! empty($this->indikator_nama) && is_array($this->indikator_nama)) {
            $indikator = [];
            foreach ($this->indikator_nama as $idx => $nama) {
                if (! empty($nama)) {
                    $indikator[] = [
                        'nama' => $nama,
                        'bulan' => isset($this->indikator_bulan[$idx]) ? (int) $this->indikator_bulan[$idx] : null,
                        'target' => isset($this->indikator_target[$idx]) ? (int) $this->indikator_target[$idx] : 0,
                    ];
                }
            }
            $this->merge([
                'indikator' => $indikator,
            ]);
        }
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
            'rab_data' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (! is_array($value)) {
                        return;
                    }
                    $totalItems = 0;
                    foreach ($value as $category => $items) {
                        if (! is_array($items)) {
                            continue;
                        }
                        foreach ($items as $item) {
                            $uraian = trim($item['uraian'] ?? '');
                            $harga = (float) ($item['harga'] ?? 0);

                            if (empty($uraian)) {
                                $fail("Setiap item belanja pada kategori '{$category}' harus memiliki uraian yang jelas.");

                                return;
                            }
                            if ($harga <= 0) {
                                $fail("Harga untuk item '{$uraian}' pada kategori '{$category}' harus lebih besar dari 0.");

                                return;
                            }
                            $totalItems++;
                        }
                    }

                    if ($totalItems === 0) {
                        $fail('Rincian Anggaran Biaya (RAB) minimal harus memiliki 1 item belanja yang valid.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'nama_kegiatan.max' => 'Nama kegiatan maksimal 255 karakter.',
            'nama_pengusul.required' => 'Nama pengusul wajib diisi.',
            'nama_pengusul.max' => 'Nama pengusul maksimal 150 karakter.',
            'nim_nip.required' => 'NIM/NIP wajib diisi.',
            'nim_nip.max' => 'NIM/NIP maksimal 20 karakter.',
            'jurusan.required' => 'Jurusan wajib diisi.',
            'jurusan.exists' => 'Jurusan yang dipilih tidak valid.',
            'prodi.required' => 'Prodi wajib diisi.',
            'prodi.exists' => 'Prodi yang dipilih tidak valid.',
            'wadir_tujuan.required' => 'Wadir tujuan wajib diisi.',
            'wadir_tujuan.exists' => 'Wadir tujuan yang dipilih tidak valid.',
            'rab_data.required' => 'RAB minimal harus memiliki 1 item.',
            'rab_data.min' => 'RAB minimal harus memiliki 1 item.',
            'indikator.*.nama.required_with' => 'Nama indikator wajib diisi jika indikator ditambahkan.',
            'indikator.*.bulan.between' => 'Bulan indikator harus di antara 1 sampai 12.',
            'indikator.*.target.between' => 'Target indikator harus di antara 0% sampai 100%.',
        ];
    }
}
