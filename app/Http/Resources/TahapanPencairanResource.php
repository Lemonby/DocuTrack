<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TahapanPencairanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->tahapan_id,
            'tgl_pencairan' => $this->tgl_pencairan?->toDateString(),
            'termin' => $this->termin,
            'nominal' => $this->nominal,
            'catatan' => $this->catatan,
        ];
    }
}
