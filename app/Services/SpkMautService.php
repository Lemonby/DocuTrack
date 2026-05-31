<?php

namespace App\Services;

use App\Models\Kegiatan;
use Illuminate\Support\Collection;

/**
 * Class SpkMautService
 * 
 * Layanan khusus untuk menghitung Kinerja & Integritas Jurusan menggunakan Metode MAUT (Multi-Attribute Utility Theory).
 * Layanan ini menghitung 4 kriteria utama (C1, C2, C3, C4) dengan bobot masing-masing 25% (0.25).
 */
class SpkMautService
{
    /**
     * Menghitung nilai detail kriteria (C1, C2, C3, C4) serta skor akhir MAUT untuk satu Kegiatan.
     * 
     * @param Kegiatan $kegiatan Objek kegiatan yang akan dihitung nilainya.
     * @return array Rincian skor C1-C4 beserta skor akhir MAUT.
     */
    public function calculateScores(Kegiatan $kegiatan): array
    {
        // Pastikan relasi LPJ dan KAK terisi. Jika tidak ada LPJ, kembalikan nilai 0.
        if (!$kegiatan->lpj) {
            return [
                'c1' => 0.0,
                'c2' => 0.0,
                'c3' => 0.0,
                'c4' => 0.0,
                'final_score' => 0.0,
            ];
        }

        // ==========================================
        // KRITERIA C1: Ketepatan Waktu Pelaksanaan
        // ==========================================
        // Selisih absolut antara durasi target (perencanaan) dengan durasi riil (realisasi).
        $c1 = 0.0;
        $tanggalMulai = $kegiatan->tanggal_mulai;
        $tanggalSelesai = $kegiatan->tanggal_selesai;
        $realisasiMulai = $kegiatan->lpj->realisasi_tanggal_mulai;
        $realisasiSelesai = $kegiatan->lpj->realisasi_tanggal_selesai;

        if ($tanggalMulai && $tanggalSelesai && $realisasiMulai && $realisasiSelesai) {
            // Hitung durasi rencana dalam hari
            $plannedDuration = (int) $tanggalMulai->diffInDays($tanggalSelesai);
            // Hitung durasi realisasi dalam hari
            $realDuration = (int) $realisasiMulai->diffInDays($realisasiSelesai);
            // Selisih absolut durasi
            $diffDays = (int) abs($plannedDuration - $realDuration);

            // Klasifikasi skor C1 berdasarkan selisih hari
            if ($diffDays == 0) {
                $c1 = 100.0; // Tepat waktu sempurna
            } elseif ($diffDays >= 1 && $diffDays <= 2) {
                $c1 = 85.0;  // Selisih 1-2 hari
            } elseif ($diffDays >= 3 && $diffDays <= 5) {
                $c1 = 60.0;  // Selisih 3-5 hari
            } else {
                $c1 = 20.0;  // Lebih dari 5 hari
            }
        }

        // ==========================================
        // KRITERIA C2: Ketepatan Penggunaan Anggaran
        // ==========================================
        // Rasio penyerapan anggaran: (realisasi / pencairan) * 100.
        $c2 = 10.0; // Default minimal jika tidak ada data valid
        $realisasiDana = (float) $kegiatan->lpj->grand_total_realisasi;
        $danaDicairkan = (float) $kegiatan->jumlah_dicairkan;

        if ($danaDicairkan > 0) {
            // Rasio dalam persen
            $absorptionRate = ($realisasiDana / $danaDicairkan) * 100.0;

            // Klasifikasi skor C2 berdasarkan persentase penyerapan
            if ($absorptionRate >= 95.0 && $absorptionRate <= 100.0) {
                $c2 = 100.0; // Sangat efisien
            } elseif ($absorptionRate >= 80.0 && $absorptionRate < 95.0) {
                $c2 = 85.0;  // Cukup efisien
            } elseif ($absorptionRate >= 50.0 && $absorptionRate < 80.0) {
                $c2 = 50.0;  // Kurang efisien
            } else {
                $c2 = 10.0;  // Tidak efisien (< 50% atau jika over-budget > 100%)
            }
        }

        // ==========================================
        // KRITERIA C3: Mendukung IKU (Indikator Kinerja Utama)
        // ==========================================
        // Jumlah IKU yang dikaitkan pada KAK kegiatan ini.
        $c3 = 0.0;
        $ikuCount = $kegiatan->kak ? $kegiatan->kak->ikus->count() : 0;

        // Klasifikasi skor C3 berdasarkan jumlah IKU yang didukung
        if ($ikuCount === 0) {
            $c3 = 0.0;
        } elseif ($ikuCount === 1) {
            $c3 = 20.0;
        } elseif ($ikuCount >= 2 && $ikuCount <= 3) {
            $c3 = 60.0;
        } elseif ($ikuCount >= 4) {
            $c3 = 100.0;
        }

        // ==========================================
        // KRITERIA C4: Ketepatan Waktu Pengajuan LPJ
        // ==========================================
        // Perbandingan tanggal submit LPJ terhadap tenggat waktu LPJ.
        $c4 = 0.0;
        $submittedAt = $kegiatan->lpj->submitted_at;
        $tenggatLpj = $kegiatan->lpj->tenggat_lpj;

        if ($submittedAt && $tenggatLpj) {
            if ($submittedAt <= $tenggatLpj) {
                $c4 = 100.0; // Tepat waktu atau lebih cepat
            } else {
                // Jika terlambat, gunakan regresi linier pengurang 5 poin per hari keterlambatan
                $daysLate = $tenggatLpj->diffInDays($submittedAt);
                $c4 = (double) max(0, 100 - ($daysLate * 5));
            }
        }

        // ==========================================
        // PERHITUNGAN AKHIR MAUT
        // ==========================================
        // Masing-masing kriteria memiliki bobot 25% (0.25)
        $finalScore = (0.25 * $c1) + (0.25 * $c2) + (0.25 * $c3) + (0.25 * $c4);

        return [
            'c1' => $c1,
            'c2' => $c2,
            'c3' => $c3,
            'c4' => $c4,
            'final_score' => round($finalScore, 2),
        ];
    }

    /**
     * Mendapatkan daftar peringkat Integritas Jurusan berdasarkan rata-rata skor MAUT dari seluruh kegiatan terpilih.
     * Hanya menghitung kegiatan yang SUDAH mengajukan LPJ (lpjs.submitted_at IS NOT NULL).
     * 
     * @return Collection Kumpulan data peringkat jurusan terurut secara descending.
     */
    public function getJurusanRankings(): Collection
    {
        // Ambil semua kegiatan yang LPJ-nya sudah disubmit
        $kegiatans = Kegiatan::with(['kak.ikus', 'lpj'])
            ->whereHas('lpj', function ($query) {
                $query->whereNotNull('submitted_at');
            })
            ->get();

        // Hitung skor MAUT untuk tiap kegiatan dan kumpulkan rinciannya
        $scoredKegiatans = $kegiatans->map(function ($kegiatan) {
            $scores = $this->calculateScores($kegiatan);
            $kegiatan->spk_scores = $scores;
            $kegiatan->final_score = $scores['final_score'];
            return $kegiatan;
        });

        // Kelompokkan kegiatan berdasarkan jurusan penyelenggara
        $grouped = $scoredKegiatans->groupBy('jurusan_penyelenggara');

        // Bentuk ranking per jurusan
        $rankings = $grouped->map(function ($items, $jurusan) {
            $totalScore = $items->sum('final_score');
            $count = $items->count();
            $averageScore = $count > 0 ? round($totalScore / $count, 2) : 0.0;

            // Hitung juga rata-rata untuk masing-masing kriteria (untuk visualisasi grafik radar/bar)
            $avgC1 = round($items->avg('spk_scores.c1'), 2);
            $avgC2 = round($items->avg('spk_scores.c2'), 2);
            $avgC3 = round($items->avg('spk_scores.c3'), 2);
            $avgC4 = round($items->avg('spk_scores.c4'), 2);

            return [
                'jurusan' => $jurusan,
                'average_score' => $averageScore,
                'kegiatan_count' => $count,
                'avg_c1' => $avgC1,
                'avg_c2' => $avgC2,
                'avg_c3' => $avgC3,
                'avg_c4' => $avgC4,
                'kegiatans' => $items->sortByDesc('final_score')->values(),
            ];
        });

        // Urutkan jurusan berdasarkan rata-rata skor tertinggi (descending)
        return $rankings->sortByDesc('average_score')->values();
    }

    /**
     * Mendapatkan rincian seluruh kegiatan di suatu jurusan beserta hasil perhitungan MAUT-nya.
     * 
     * @param string $jurusan Nama jurusan penyelenggara.
     * @return Collection Daftar kegiatan lengkap dengan skor SPK masing-masing.
     */
    public function getKegiatanScoresByJurusan(string $jurusan): Collection
    {
        $kegiatans = Kegiatan::with(['kak.ikus', 'lpj'])
            ->where('jurusan_penyelenggara', $jurusan)
            ->whereHas('lpj', function ($query) {
                $query->whereNotNull('submitted_at');
            })
            ->get();

        return $kegiatans->map(function ($kegiatan) {
            $scores = $this->calculateScores($kegiatan);
            $kegiatan->spk_scores = $scores;
            $kegiatan->final_score = $scores['final_score'];
            return $kegiatan;
        })->sortByDesc('final_score')->values();
    }
}
