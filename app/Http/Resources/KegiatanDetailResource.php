<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KegiatanDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->kegiatan_id,
            'nama_kegiatan' => $this->nama_kegiatan,
            'prodi_penyelenggara' => $this->prodi_penyelenggara,
            'pemilik_kegiatan' => $this->pemilik_kegiatan,
            'nim_pelaksana' => $this->nim_pelaksana,
            'nip' => $this->nip,
            'nama_pj' => $this->nama_pj,
            'bukti_mak' => $this->bukti_mak,
            'jurusan_penyelenggara' => $this->jurusan_penyelenggara,
            'surat_pengantar' => $this->surat_pengantar,
            'tanggal_mulai' => $this->tanggal_mulai?->toDateString(),
            'tanggal_selesai' => $this->tanggal_selesai?->toDateString(),
            'status' => [
                'id' => $this->status_utama_id,
                'nama' => $this->whenLoaded('statusUtama', fn () => $this->statusUtama->nama_status_usulan),
            ],
            'posisi_id' => $this->posisi_id,
            'workflow_progress' => $this->workflow_progress,
            'wadir' => $this->whenLoaded('wadir', fn () => $this->wadir->nama_wadir),
            'admin' => new UserResource($this->whenLoaded('user')),
            'dana_di_setujui' => $this->dana_di_setujui,
            'jumlah_dicairkan' => $this->jumlah_dicairkan,
            'metode_pencairan' => $this->metode_pencairan,
            'tanggal_pencairan' => $this->tanggal_pencairan?->toISOString(),
            'catatan_bendahara' => $this->catatan_bendahara,
            'umpan_balik_verifikator' => $this->umpan_balik_verifikator,
            'kak' => new KakResource($this->whenLoaded('kak')),
            'lpj' => new LpjResource($this->whenLoaded('lpj')),
            'progress_history' => ProgressHistoryResource::collection($this->whenLoaded('progressHistories')),
            'tahapan_pencairan' => TahapanPencairanResource::collection($this->whenLoaded('tahapanPencairans')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
