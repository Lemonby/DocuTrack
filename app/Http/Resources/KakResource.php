<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KakResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->kak_id,
            'iku' => $this->iku,
            'penerima_manfaat' => $this->penerima_manfaat,
            'gambaran_umum' => $this->gambaran_umum,
            'metode_pelaksanaan' => $this->metode_pelaksanaan,
            'tgl_pembuatan' => $this->tgl_pembuatan?->toDateString(),
            'total_rab' => $this->total_rab,
            'indikator' => IndikatorKakResource::collection($this->whenLoaded('indikators')),
            'tahapan' => TahapanPelaksanaanResource::collection($this->whenLoaded('tahapans')),
            'rab' => RabResource::collection($this->whenLoaded('rabs')),
            'ikus' => $this->whenLoaded('ikus'),
        ];
    }
}
