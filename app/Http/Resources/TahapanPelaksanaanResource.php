<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TahapanPelaksanaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->tahapan_id, 'nama_tahapan' => $this->nama_tahapan];
    }
}
