<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndikatorKakResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->indikator_id,
            'bulan' => $this->bulan,
            'indikator_keberhasilan' => $this->indikator_keberhasilan,
            'target_persen' => $this->target_persen,
        ];
    }
}
