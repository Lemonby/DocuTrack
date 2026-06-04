<?php

namespace App\Services;

use App\Models\Kegiatan;
use App\Models\LogStatus;
use App\Models\Kak;
use App\Models\IndikatorKak;
use App\Models\KategoriRab;
use App\Models\Rab;
use App\Models\TahapanPelaksanaan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KegiatanService
{
    /**
     * Create kegiatan with all related data (KAK, indikator, tahapan, RAB) in one transaction.
     */
    public function createKegiatan(array $data, int $userId): Kegiatan
    {
        // Decode rab_data if sent as JSON string
        if (isset($data['rab_data']) && is_string($data['rab_data'])) {
            $data['rab_data'] = json_decode($data['rab_data'], true);
        }

        // Map flat array indicator inputs to nested indicator array
        if (!empty($data['indikator_nama']) && is_array($data['indikator_nama'])) {
            $data['indikator'] = [];
            foreach ($data['indikator_nama'] as $idx => $nama) {
                if (!empty($nama)) {
                    $data['indikator'][] = [
                        'nama' => $nama,
                        'bulan' => $data['indikator_bulan'][$idx] ?? null,
                        'target' => $data['indikator_target'][$idx] ?? 0,
                    ];
                }
            }
        }

        return DB::transaction(function () use ($data, $userId) {
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => $data['nama_kegiatan'],
                'prodi_penyelenggara' => $data['prodi'],
                'pemilik_kegiatan' => $data['nama_pengusul'],
                'nim_pelaksana' => $data['nim_nip'],
                'user_id' => $userId,
                'jurusan_penyelenggara' => $data['jurusan'],
                'status_utama_id' => WorkflowService::STATUS_MENUNGGU,
                'wadir_tujuan' => $data['wadir_tujuan'],
                'posisi_id' => WorkflowService::POSITION_VERIFIKATOR,
            ]);

            $kak = Kak::create([
                'kegiatan_id' => $kegiatan->kegiatan_id,
                'gambaran_umum' => $data['gambaran_umum'] ?? '',
                'penerima_manfaat' => $data['penerima_manfaat'] ?? '',
                'metode_pelaksanaan' => $data['metode_pelaksanaan'] ?? '',
                'tgl_pembuatan' => now()->toDateString(),
            ]);

            // Sync dynamic IKUs
            $ikuNames = array_filter(array_map('trim', explode(',', $data['indikator_kinerja'] ?? '')));
            $ikuIds = [];
            foreach ($ikuNames as $idx => $name) {
                $iku = \App\Models\Iku::firstOrCreate(
                    ['indikator_kinerja' => $name],
                    [
                        'kode_iku' => 'IKU_' . strtoupper(uniqid()) . '_' . ($idx + 1),
                        'deskripsi' => $name,
                        'tahun' => 2020
                    ]
                );
                $ikuIds[] = $iku->id;
            }
            $kak->ikus()->sync($ikuIds);

            // Tahapan pelaksanaan
            foreach (($data['tahapan'] ?? []) as $tahap) {
                if (! empty($tahap)) {
                    TahapanPelaksanaan::create(['kak_id' => $kak->kak_id, 'nama_tahapan' => $tahap]);
                }
            }

            // Indikator keberhasilan
            foreach (($data['indikator'] ?? []) as $ind) {
                if (! empty($ind['nama'])) {
                    IndikatorKak::create([
                        'kak_id' => $kak->kak_id,
                        'bulan' => $ind['bulan'] ?? null,
                        'indikator_keberhasilan' => $ind['nama'],
                        'target_persen' => (int) ($ind['target'] ?? 0),
                    ]);
                }
            }

            // RAB items grouped by category
            foreach (($data['rab_data'] ?? []) as $namaKategori => $items) {
                $kategori = KategoriRab::firstOrCreate(['nama_kategori' => $namaKategori]);

                foreach ($items as $item) {
                    Rab::create([
                        'kak_id' => $kak->kak_id,
                        'kategori_id' => $kategori->kategori_rab_id,
                        'uraian' => $item['uraian'] ?? '',
                        'rincian' => $item['rincian'] ?? '',
                        'sat1' => $item['sat1'] ?? '',
                        'sat2' => $item['sat2'] ?? '',
                        'vol1' => (float) ($item['vol1'] ?? 0),
                        'vol2' => (float) ($item['vol2'] ?? 1),
                        'harga' => (float) ($item['harga'] ?? 0),
                    ]);
                }
            }

            // Create submission notification log in log_statuses
            LogStatus::create([
                'user_id' => $userId,
                'tipe_log' => 'SUBMISSION',
                'id_referensi' => $kegiatan->kegiatan_id,
                'status' => 'BELUM_DIBACA',
                'konten_json' => [
                    'judul' => 'Usulan Baru Diajukan',
                    'pesan' => "Usulan baru \"{$kegiatan->nama_kegiatan}\" berhasil diajukan dan sedang menunggu verifikasi.",
                    'link' => "/admin/pengajuan-kegiatan"
                ]
            ]);

            return $kegiatan->load(['kak.rabs', 'kak.indikators', 'kak.tahapans']);
        });
    }

    /**
     * Get full detail of a kegiatan with all nested relations.
     */
    public function getDetailLengkap(int $kegiatanId): Kegiatan
    {
        return Kegiatan::with([
            'user', 'statusUtama', 'wadir',
            'kak.indikators', 'kak.tahapans', 'kak.rabs.kategori',
            'progressHistories.changedBy', 'progressHistories.revisiComments',
            'lpj.items.kategori', 'tahapanPencairans',
        ])->findOrFail($kegiatanId);
    }

    /**
     * Submit activity details (PJ, dates, cover letter) — advances to PPK position.
     */
    public function submitRincian(int $kegiatanId, array $data, $suratPengantarFile = null): Kegiatan
    {
        return DB::transaction(function () use ($kegiatanId, $data, $suratPengantarFile) {
            $kegiatan = Kegiatan::findOrFail($kegiatanId);

            $updateData = [
                'nama_pj' => $data['penanggung_jawab'],
                'nip' => $data['nim_nip_pj'],
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'posisi_id' => WorkflowService::POSITION_PPK,
                'status_utama_id' => WorkflowService::STATUS_MENUNGGU,
            ];

            if ($suratPengantarFile) {
                $path = $suratPengantarFile->store('surat-pengantar', 'public');
                $updateData['surat_pengantar'] = $path;
            }

            $kegiatan->update($updateData);

            return $kegiatan->fresh();
        });
    }

    /**
     * Update kegiatan with all related data (KAK, indikator, tahapan, RAB) in one transaction.
     */
    public function updateKegiatan(int $kegiatanId, array $data, int $userId): Kegiatan
    {
        // Decode rab_data if sent as JSON string
        if (isset($data['rab_data']) && is_string($data['rab_data'])) {
            $data['rab_data'] = json_decode($data['rab_data'], true);
        }

        // Map flat array indicator inputs to nested indicator array
        if (!empty($data['indikator_nama']) && is_array($data['indikator_nama'])) {
            $data['indikator'] = [];
            foreach ($data['indikator_nama'] as $idx => $nama) {
                if (!empty($nama)) {
                    $data['indikator'][] = [
                        'nama' => $nama,
                        'bulan' => $data['indikator_bulan'][$idx] ?? null,
                        'target' => $data['indikator_target'][$idx] ?? 0,
                    ];
                }
            }
        }

        return DB::transaction(function () use ($kegiatanId, $data, $userId) {
            $kegiatan = Kegiatan::findOrFail($kegiatanId);
            
            $statusUtamaId = $kegiatan->status_utama_id;
            $posisiId = $kegiatan->posisi_id;
            
            // If the proposal is in revision status, reset it to pending for verification
            if ((int)$statusUtamaId === WorkflowService::STATUS_REVISI) {
                $statusUtamaId = WorkflowService::STATUS_MENUNGGU;
                $posisiId = WorkflowService::POSITION_VERIFIKATOR;
            }

            $kegiatan->update([
                'nama_kegiatan' => $data['nama_kegiatan'] ?? $kegiatan->nama_kegiatan,
                'prodi_penyelenggara' => $data['prodi'] ?? $kegiatan->prodi_penyelenggara,
                'pemilik_kegiatan' => $data['nama_pengusul'] ?? $kegiatan->pemilik_kegiatan,
                'nim_pelaksana' => $data['nim_nip'] ?? $kegiatan->nim_pelaksana,
                'jurusan_penyelenggara' => $data['jurusan'] ?? $kegiatan->jurusan_penyelenggara,
                'wadir_tujuan' => $data['wadir_tujuan'] ?? $kegiatan->wadir_tujuan,
                'status_utama_id' => $statusUtamaId,
                'posisi_id' => $posisiId,
            ]);

            $kak = Kak::updateOrCreate(
                ['kegiatan_id' => $kegiatan->kegiatan_id],
                [
                    'gambaran_umum' => $data['gambaran_umum'] ?? '',
                    'penerima_manfaat' => $data['penerima_manfaat'] ?? '',
                    'metode_pelaksanaan' => $data['metode_pelaksanaan'] ?? '',
                    'tgl_pembuatan' => now()->toDateString(),
                ]
            );

            // Sync dynamic IKUs
            $ikuNames = array_filter(array_map('trim', explode(',', $data['indikator_kinerja'] ?? '')));
            $ikuIds = [];
            foreach ($ikuNames as $idx => $name) {
                $iku = \App\Models\Iku::firstOrCreate(
                    ['indikator_kinerja' => $name],
                    [
                        'kode_iku' => 'IKU_' . strtoupper(uniqid()) . '_' . ($idx + 1),
                        'deskripsi' => $name,
                        'tahun' => 2020
                    ]
                );
                $ikuIds[] = $iku->id;
            }
            $kak->ikus()->sync($ikuIds);

            // Update Tahapan pelaksanaan: clear existing and re-insert
            TahapanPelaksanaan::where('kak_id', $kak->kak_id)->delete();
            foreach (($data['tahapan'] ?? []) as $tahap) {
                if (! empty($tahap)) {
                    TahapanPelaksanaan::create(['kak_id' => $kak->kak_id, 'nama_tahapan' => $tahap]);
                }
            }

            // Update Indikator keberhasilan: clear existing and re-insert
            IndikatorKak::where('kak_id', $kak->kak_id)->delete();
            foreach (($data['indikator'] ?? []) as $ind) {
                if (! empty($ind['nama'])) {
                    IndikatorKak::create([
                        'kak_id' => $kak->kak_id,
                        'bulan' => $ind['bulan'] ?? null,
                        'indikator_keberhasilan' => $ind['nama'],
                        'target_persen' => (int) ($ind['target'] ?? 0),
                    ]);
                }
            }

            // Update RAB items: clear existing and re-insert
            Rab::where('kak_id', $kak->kak_id)->delete();
            foreach (($data['rab_data'] ?? []) as $namaKategori => $items) {
                $kategori = KategoriRab::firstOrCreate(['nama_kategori' => $namaKategori]);

                foreach ($items as $item) {
                    Rab::create([
                        'kak_id' => $kak->kak_id,
                        'kategori_id' => $kategori->kategori_rab_id,
                        'uraian' => $item['uraian'] ?? '',
                        'rincian' => $item['rincian'] ?? '',
                        'sat1' => $item['sat1'] ?? '',
                        'sat2' => $item['sat2'] ?? '',
                        'vol1' => (float) ($item['vol1'] ?? 0),
                        'vol2' => (float) ($item['vol2'] ?? 1),
                        'harga' => (float) ($item['harga'] ?? 0),
                    ]);
                }
            }

            // Create notification log in log_statuses
            LogStatus::create([
                'user_id' => $userId,
                'tipe_log' => 'SUBMISSION',
                'id_referensi' => $kegiatan->kegiatan_id,
                'status' => 'BELUM_DIBACA',
                'konten_json' => [
                    'judul' => 'Revisi Usulan Diajukan Ulang',
                    'pesan' => "Revisi usulan \"{$kegiatan->nama_kegiatan}\" berhasil diajukan dan sedang menunggu verifikasi.",
                    'link' => "/admin/pengajuan-kegiatan"
                ]
            ]);

            // Add progress history entry for STATUS_MENUNGGU
            \App\Models\ProgressHistory::create([
                'kegiatan_id' => $kegiatan->kegiatan_id,
                'status_id' => WorkflowService::STATUS_MENUNGGU,
                'changed_by_user_id' => $userId,
                'created_at' => now(),
            ]);

            return $kegiatan->load(['kak.rabs', 'kak.indikators', 'kak.tahapans']);
        });
    }

    /**
     * Dashboard statistics router based on role.
     */
    public function getDashboardStats(?string $jurusan = null, ?string $role = null): array
    {
        if (!$role) {
            if (auth()->check()) {
                $role = auth()->user()->getRoleNames()->first();
            } else {
                $role = \Illuminate\Support\Facades\Session::get('role');
            }
        }
        
        $role = $role ?? 'admin';

        if (!$jurusan) {
            if (auth()->check()) {
                $jurusan = auth()->user()->nama_jurusan;
            } else {
                $jurusan = \Illuminate\Support\Facades\Session::get('jurusan');
            }
        }

        return match (strtolower($role)) {
            'admin' => $this->getAdminDashboardStats($jurusan),
            'verifikator' => $this->getVerifikatorDashboardStats(),
            'ppk' => $this->getPpkDashboardStats(),
            'wadir' => $this->getWadirDashboardStats(),
            'bendahara' => $this->getBendaharaDashboardStats(),
            default => $this->getAdminDashboardStats($jurusan),
        };
    }

    /**
     * Admin Dashboard Statistics.
     */
    public function getAdminDashboardStats(?string $jurusan = null): array
    {
        $query = Kegiatan::query();

        if (!empty($jurusan)) {
            $query->where('jurusan_penyelenggara', $jurusan);
        }

        return [
            'total' => (clone $query)->count(),
            'disetujui' => (clone $query)->where('posisi_id', 1)->where('status_utama_id', 8)->count(),
            'ditolak' => (clone $query)->where('status_utama_id', 4)->count(),
            'menunggu' => (clone $query)->where('status_utama_id', '!=', 4)
                ->where(function ($q) {
                    $q->where('posisi_id', '!=', 1)->orWhere('status_utama_id', '!=', 8);
                })->count(),
        ];
    }

    /**
     * Verifikator Dashboard Statistics.
     */
    public function getVerifikatorDashboardStats(): array
    {
        $query = Kegiatan::query();

        return [
            'total' => (clone $query)->count(),
            'disetujui' => (clone $query)->whereIn('status_utama_id', [3, 7, 8])
                ->where('status_utama_id', '!=', 4)
                ->whereNotNull('bukti_mak')
                ->count(),
            'ditolak' => (clone $query)->where('status_utama_id', 4)->count(),
            'menunggu' => (clone $query)->where('posisi_id', 2)->count(),
        ];
    }

    /**
     * PPK Dashboard Statistics.
     */
    public function getPpkDashboardStats(): array
    {
        $query = Kegiatan::query();

        $menungguCount = (clone $query)->where('posisi_id', 3)->count();
        $disetujuiCount = (clone $query)->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('posisi_id', '>', 3)
                    ->whereNotNull('bukti_mak');
            })->orWhereIn('status_utama_id', [5, 6, 8]);
        })->count();

        return [
            'total' => $menungguCount + $disetujuiCount,
            'disetujui' => $disetujuiCount,
            'menunggu' => $menungguCount,
        ];
    }

    /**
     * Wadir Dashboard Statistics.
     */
    public function getWadirDashboardStats(): array
    {
        $query = Kegiatan::query();

        $menungguCount = (clone $query)->where('posisi_id', 4)->count();
        $disetujuiCount = (clone $query)->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('posisi_id', '>', 4)
                    ->whereNotNull('bukti_mak');
            })->orWhereIn('status_utama_id', [5, 6, 8]);
        })->count();

        return [
            'total' => $menungguCount + $disetujuiCount,
            'disetujui' => $disetujuiCount,
            'menunggu' => $menungguCount,
        ];
    }

    /**
     * Bendahara Dashboard Statistics.
     */
    public function getBendaharaDashboardStats(): array
    {
        $query = Kegiatan::query();

        return [
            'total' => (clone $query)->count(),
            'danaDiberikan' => (clone $query)->whereIn('status_utama_id', [5, 6, 8])->count(),
            'ditolak' => (clone $query)->where('status_utama_id', 4)->count(),
            'menunggu' => (clone $query)->where('posisi_id', 5)
                ->where('status_utama_id', 1)
                ->count(),
        ];
    }
}
