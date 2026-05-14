<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RevisiCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->revisi_comment_id,
            'komentar' => $this->komentar_revisi,
            'target_kolom' => $this->target_kolom,
        ];
    }
}
