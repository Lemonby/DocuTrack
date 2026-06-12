<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LpjItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->lpj_item_id,
            'kategori' => [
                'nama_kategori' => $this->kategori->nama_kategori ?? $this->jenis_belanja ?? 'Lainnya',
            ],
            'uraian' => $this->uraian,
            'rincian' => $this->rincian,
            'total_harga' => $this->total_harga,
            'realisasi' => $this->realisasi,
            'nominal' => $this->realisasi, // Compatibility with app
            'file_bukti' => $this->file_bukti,
            'lampiran' => $this->file_bukti, // Compatibility with app
            'komentar' => $this->komentar,
            'sat1' => $this->sat1, 'sat2' => $this->sat2,
            'vol1' => $this->vol1, 'vol2' => $this->vol2,
            'harga' => $this->harga,
        ];
    }
}
