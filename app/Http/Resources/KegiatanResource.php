<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KegiatanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->kegiatan_id,
            'nama_kegiatan' => $this->nama_kegiatan,
            'prodi_penyelenggara' => $this->prodi_penyelenggara,
            'pemilik_kegiatan' => $this->pemilik_kegiatan,
            'nim_pelaksana' => $this->nim_pelaksana,
            'jurusan_penyelenggara' => $this->jurusan_penyelenggara,
            'status' => [
                'id' => $this->status_utama_id,
                'nama' => $this->whenLoaded('statusUtama', fn () => $this->statusUtama->nama_status_usulan),
            ],
            'posisi_id' => $this->posisi_id,
            'workflow_progress' => $this->workflow_progress,
            'wadir' => $this->whenLoaded('wadir', fn () => $this->wadir->nama_wadir),
            'admin' => new UserResource($this->whenLoaded('user')),
            'tanggal_mulai' => $this->tanggal_mulai?->toDateString(),
            'tanggal_selesai' => $this->tanggal_selesai?->toDateString(),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
