<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->progress_history_id,
            'status' => $this->whenLoaded('status', fn () => $this->status->nama_status_usulan),
            'changed_by' => $this->whenLoaded('changedBy', fn () => $this->changedBy->nama),
            'komentar' => RevisiCommentResource::collection($this->whenLoaded('revisiComments')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
