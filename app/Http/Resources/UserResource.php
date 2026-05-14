<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user_id,
            'nama' => $this->nama,
            'email' => $this->email,
            'nama_jurusan' => $this->nama_jurusan,
            'status' => $this->status,
            'role' => $this->roles->first()?->name,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
