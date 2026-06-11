<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotifikasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipe' => $this->tipe_log,
            'status' => $this->status,
            'konten' => $this->konten_json,
            'id_referensi' => $this->id_referensi,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
