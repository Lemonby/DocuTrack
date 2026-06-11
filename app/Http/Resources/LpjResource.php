<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LpjResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->lpj_id,
            'kegiatan_id' => $this->kegiatan_id,
            'grand_total_realisasi' => $this->grand_total_realisasi,
            'submitted_at' => $this->submitted_at?->toISOString(),
            'approved_at' => $this->approved_at?->toISOString(),
            'tenggat_lpj' => $this->tenggat_lpj?->toDateString(),
            'status' => $this->whenLoaded('status', fn () => $this->status->nama_status_usulan),
            'deadline_status' => $this->deadline_status,
            'komentar_penolakan' => $this->komentar_penolakan,
            'komentar_revisi' => $this->komentar_revisi,
            'items' => LpjItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
