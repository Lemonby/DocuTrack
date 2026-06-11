<?php

// File: app/Http/Resources/RabResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RabResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->rab_item_id,
            'kategori' => $this->whenLoaded('kategori', fn () => $this->kategori->nama_kategori),
            'uraian' => $this->uraian,
            'rincian' => $this->rincian,
            'sat1' => $this->sat1,
            'sat2' => $this->sat2,
            'vol1' => $this->vol1,
            'vol2' => $this->vol2,
            'harga' => $this->harga,
            'total_harga' => $this->total_harga,
        ];
    }
}
