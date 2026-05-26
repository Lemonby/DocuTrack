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
                'iku' => $data['indikator_kinerja'] ?? 'Belum pilih',
                'gambaran_umum' => $data['gambaran_umum'] ?? '',
                'penerima_manfaat' => $data['penerima_manfaat'] ?? '',
                'metode_pelaksanaan' => $data['metode_pelaksanaan'] ?? '',
                'tgl_pembuatan' => now()->toDateString(),
            ]);

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
     * Dashboard statistics.
     */
    public function getDashboardStats(?string $jurusan = null): array
    {
        $query = Kegiatan::query();

        if ($jurusan) {
            $query->byJurusan($jurusan);
        }

        return [
            'total' => (clone $query)->count(),
            'disetujui' => (clone $query)->withStatus(5)->count(),
            'ditolak' => (clone $query)->withStatus(4)->count(),
            'menunggu' => (clone $query)->whereNotIn('status_utama_id', [3, 4, 5])->count(),
        ];
    }
}
