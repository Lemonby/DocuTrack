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
     * Langkah 1: Menghitung nilai kriteria mentah (raw score) untuk satu Kegiatan.
     * Nilai keluaran hasil kriteria ini langsung berskala desimal 0.0 - 1.0.
     * 
     * @param Kegiatan $kegiatan Objek kegiatan yang akan dihitung nilainya.
     * @return array Nilai kriteria mentah C1 s.d C4 skala desimal 0.0 - 1.0.
     */
    public function calculateRawScores(Kegiatan $kegiatan): array
    {
        // Pastikan relasi LPJ dan KAK terisi. Jika tidak ada LPJ, kembalikan nilai 0.
        if (!$kegiatan->lpj) {
            return [
                'c1' => 0.0,
                'c2' => 0.0,
                'c3' => 0.0,
                'c4' => 0.0,
            ];
        }

        // ====================================================================
        // KRITERIA C1: Ketepatan Waktu Pelaksanaan (Skala 0.0 - 1.0)
        // ====================================================================
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
            $diffDays = (int) abs($tanggalMulai->diffInDays($realisasiMulai));

            // Klasifikasi skor C1 berdasarkan selisih hari (skala 0.0 - 1.0)
            if ($diffDays == 0) {
                $c1 = 1.00;  // Tepat waktu sempurna (100%)
            } elseif ($diffDays >= 1 && $diffDays <= 2) {
                $c1 = 0.80;  // Selisih 1-2 hari (80%)
            } elseif ($diffDays >= 3 && $diffDays <= 5) {
                $c1 = 0.60;  // Selisih 3-5 hari (60%)
            } elseif ($diffDays >= 6 && $diffDays <= 8) {
                $c1 = 0.40;  // Selisih 6-8 hari (40%)
            } elseif ($diffDays >= 9 && $diffDays <= 11) {
                $c1 = 0.20;  // Selisih 9-11 hari (20%)
            } elseif ($diffDays >= 12 && $diffDays <= 14) {
                $c1 = 0.10;  // Selisih 12-14 hari (10%)
            } else {
                $c1 = 0.00;  // Lebih dari 14 hari (0%)
            }
        }

        // ====================================================================
        // KRITERIA C2: Ketepatan Penggunaan Anggaran (Skala 0.0 - 1.0)
        // ====================================================================
        // Rasio penyerapan anggaran: (realisasi / pencairan) * 100.
        $c2 = 0.10; // Default minimal jika tidak ada data valid (10%)
        $realisasiDana = (float) $kegiatan->lpj->grand_total_realisasi;
        $danaDicairkan = (float) $kegiatan->jumlah_dicairkan;

        if ($danaDicairkan > 0) {
            // Rasio penyerapan dana dalam persen (0 - 100)
            $absorptionRate = ($realisasiDana / $danaDicairkan) * 100.0;

            // Klasifikasi skor C2 berdasarkan persentase penyerapan (skala 0.0 - 1.0)
            if ($absorptionRate >= 95.0 && $absorptionRate <= 100.0) {
                $c2 = 1.00;  // Sangat efisien (95% - 100%)
            } elseif ($absorptionRate >= 90.0 && $absorptionRate < 95.0) {
                $c2 = 0.90;  // Sangat dekat target (90% - 94.99%)
            } elseif ($absorptionRate >= 80.0 && $absorptionRate < 90.0) {
                $c2 = 0.80;  // Cukup efisien tinggi (80% - 89.99%)
            } elseif ($absorptionRate >= 70.0 && $absorptionRate < 80.0) {
                $c2 = 0.70;  // Cukup efisien rendah (70% - 79.99%)
            } elseif ($absorptionRate >= 60.0 && $absorptionRate < 70.0) {
                $c2 = 0.55;  // Efisien sedang (60% - 69.99%)
            } elseif ($absorptionRate >= 50.0 && $absorptionRate < 60.0) {
                $c2 = 0.40;  // Kurang efisien (50% - 59.99%)
            } elseif ($absorptionRate >= 30.0 && $absorptionRate < 50.0) {
                $c2 = 0.25;  // Sangat kurang efisien (30% - 49.99%)
            } else {
                $c2 = 0.10;  // Tidak efisien / over-budget / di bawah 30%
            }
        }

        // ====================================================================
        // KRITERIA C3: Mendukung IKU (Indikator Kinerja Utama) (Skala 0.0 - 1.0)
        // ====================================================================
        // Jumlah IKU yang dikaitkan pada KAK kegiatan ini.
        $c3 = 0.0;
        $ikuCount = $kegiatan->kak ? $kegiatan->kak->ikus->count() : 0;

        // Klasifikasi skor C3 berdasarkan jumlah IKU yang didukung (skala 0.0 - 1.0)
        // Aturan dosen: jika ada IKU minimal 1, nilainya langsung maksimal yaitu 1.0 (100%), jika tidak ada nilainya 0.0
        if ($ikuCount === 0) {
            $c3 = 0.0;
        } else {
            $c3 = 1.0;
        }

        // ====================================================================
        // KRITERIA C4: Ketepatan Waktu Pengajuan LPJ (Skala 0.0 - 1.0)
        // ====================================================================
        // Perbandingan tanggal submit LPJ terhadap tenggat waktu LPJ.
        $c4 = 0.0;
        $submittedAt = $kegiatan->lpj->submitted_at;
        $tenggatLpj = $kegiatan->lpj->tenggat_lpj;

        if ($submittedAt && $tenggatLpj) {
            if ($submittedAt <= $tenggatLpj) {
                $c4 = 1.00; // Tepat waktu atau lebih cepat (100%)
            } else {
                // Jika terlambat, gunakan regresi linier pengurang 0.05 poin per hari keterlambatan (skala 0-1)
                $daysLate = $tenggatLpj->diffInDays($submittedAt);
                $c4 = (double) max(0.0, 1.0 - ($daysLate * 0.05));
            }
        }

        return [
            'c1' => $c1,
            'c2' => $c2,
            'c3' => $c3,
            'c4' => $c4,
        ];
    }

    /**
     * Menyinkronkan nilai kriteria mentah dari satu kegiatan ke tabel database.
     *
     * @param Kegiatan $kegiatan
     */
    public function syncKegiatanScores(Kegiatan $kegiatan): void
    {
        if (!$kegiatan->lpj || !$kegiatan->lpj->submitted_at) {
            return;
        }

        $raw = $this->calculateRawScores($kegiatan);
        $kriterias = \App\Models\Kriteria::all();

        foreach ($kriterias as $kriteria) {
            $code = strtolower($kriteria->kode_kriteria); // 'c1', 'c2', 'c3', 'c4'
            if (isset($raw[$code])) {
                \App\Models\NilaiKegiatanKriteria::updateOrCreate(
                    [
                        'kegiatan_id' => $kegiatan->kegiatan_id,
                        'kriteria_id' => $kriteria->kriteria_id,
                    ],
                    [
                        'nilai_mentah' => $raw[$code],
                    ]
                );
            }
        }
    }

    /**
     * Menyinkronkan nilai kriteria mentah untuk seluruh kegiatan yang sudah memiliki LPJ ter-submit.
     */
    public function syncAllKegiatanScores(): void
    {
        $kegiatans = Kegiatan::whereHas('lpj', function ($query) {
            $query->whereNotNull('submitted_at');
        })->get();

        foreach ($kegiatans as $kegiatan) {
            $this->syncKegiatanScores($kegiatan);
        }
    }

    /**
     * Langkah 2 & 3: Menghitung nilai detail kriteria (C1-C4) setelah normalisasi, 
     * serta skor akhir MAUT untuk satu Kegiatan di lingkup jurusannya.
     * 
     * @param Kegiatan $kegiatan Objek kegiatan yang akan dihitung nilainya.
     * @param Collection|null $peerKegiatans Kumpulan kegiatan lain dalam satu jurusan untuk acuan min/max.
     * @return array Rincian skor C1-C4 (nilai mentah usulan) beserta skor akhir MAUT hasil normalisasi.
     */
    public function calculateScores(Kegiatan $kegiatan, ?Collection $peerKegiatans = null): array
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

        // Ambil nilai mentah dari database untuk kriteria yang terdaftar
        $kriterias = \App\Models\Kriteria::all();
        
        // Pastikan skor disinkronkan terlebih dahulu ke database jika belum ada
        $scoresMap = \App\Models\NilaiKegiatanKriteria::where('kegiatan_id', $kegiatan->kegiatan_id)
            ->pluck('nilai_mentah', 'kriteria_id');

        if ($scoresMap->isEmpty() && $kegiatan->lpj->submitted_at !== null) {
            $this->syncKegiatanScores($kegiatan);
            $scoresMap = \App\Models\NilaiKegiatanKriteria::where('kegiatan_id', $kegiatan->kegiatan_id)
                ->pluck('nilai_mentah', 'kriteria_id');
        }

        $raw = [];
        $finalScore = 0.0;
        
        foreach ($kriterias as $kriteria) {
            $code = strtolower($kriteria->kode_kriteria);
            $val = $scoresMap->get($kriteria->kriteria_id) ?? 0.0;
            $raw[$code] = $val;
            
            // Hitung kontribusi bobot kriteria dinamis
            $finalScore += $kriteria->bobot * $val;
        }

        return array_merge($raw, [
            'final_score' => round($finalScore, 4),
        ]);
    }

    /**
     * Langkah 5: Mendapatkan daftar peringkat Integritas Jurusan (Perankingan Pusat).
     * Hasil perankingan lokal (jurusan) dirata-ratakan untuk merepresentasikan integritas setiap jurusan.
     * Hanya menghitung kegiatan yang SUDAH mengajukan LPJ (lpjs.submitted_at IS NOT NULL).
     * 
     * @return Collection Kumpulan data peringkat jurusan terurut secara descending berdasarkan rata-rata skor.
     */
    public function getJurusanRankings(): Collection
    {
        // Ambil kriteria terdaftar di database
        $kriterias = \App\Models\Kriteria::all();

        // Ambil semua kegiatan yang LPJ-nya sudah disubmit
        $kegiatans = Kegiatan::with(['kak.ikus', 'lpj'])
            ->whereHas('lpj', function ($query) {
                $query->whereNotNull('submitted_at');
            })
            ->get();

        // Kelompokkan kegiatan berdasarkan jurusan penyelenggara (Langkah Pertama)
        $grouped = $kegiatans->groupBy('jurusan_penyelenggara');

        // Simpan data kriteria mentah rata-rata per jurusan
        $jurusanAverages = [];
        $jurusanData = [];

        foreach ($grouped as $jurusan => $items) {
            $scoredKegiatans = $items->map(function ($kegiatan) {
                $scores = $this->calculateScores($kegiatan);
                $kegiatan->spk_scores = $scores;
                $kegiatan->final_score = $scores['final_score'];
                return $kegiatan;
            });

            $count = $scoredKegiatans->count();

            // Hitung rata-rata kriteria mentah untuk setiap kriteria dinamis
            $averages = [];
            $roundedAverages = [];

            foreach ($kriterias as $kriteria) {
                $code = strtolower($kriteria->kode_kriteria);
                $avgVal = $count > 0 ? $scoredKegiatans->avg("spk_scores.{$code}") : 0.0;
                $averages[$kriteria->kriteria_id] = $avgVal;
                $roundedAverages["avg_{$code}"] = round($avgVal, 4);
            }

            $jurusanAverages[$jurusan] = $averages;

            // Kita juga simpan nilai rata-rata yang dibulatkan ke 4 desimal untuk ditampilkan di visualisasi
            $jurusanData[$jurusan] = array_merge([
                'jurusan' => $jurusan,
                'kegiatan_count' => $count,
                'kegiatans' => $scoredKegiatans->sortByDesc('final_score')->values(),
            ], $roundedAverages);
        }

        // Cari Nilai MAX dan MIN dari setiap kriteria rata-rata di antara semua jurusan
        $maxValues = [];
        $minValues = [];

        foreach ($kriterias as $kriteria) {
            $kid = $kriteria->kriteria_id;
            $values = count($jurusanAverages) > 0 ? array_column($jurusanAverages, $kid) : [];
            $maxValues[$kid] = count($values) > 0 ? max($values) : 1.0;
            $minValues[$kid] = count($values) > 0 ? min($values) : 0.0;
        }

        $rankings = collect();

        foreach ($jurusanData as $jurusan => $data) {
            $avg = $jurusanAverages[$jurusan];

            // Hitung Nilai Evaluasi MAUT (Perkalian Bobot Dinamis dari database)
            $averageScore = 0.0;

            foreach ($kriterias as $kriteria) {
                $kid = $kriteria->kriteria_id;
                $maxVal = $maxValues[$kid];
                $minVal = $minValues[$kid];

                // U_ij = (x_ij - x_j_min) / (x_j_max - x_j_min)
                // Hasil normalisasi utilitas dibulatkan ke 4 desimal
                $uVal = ($maxVal - $minVal == 0) ? 1.0 : round(($avg[$kid] - $minVal) / ($maxVal - $minVal), 4);

                // Bobot dikali utility, bulatkan tiap suku ke 4 desimal
                $term = round($kriteria->bobot * $uVal, 4);
                $averageScore += $term;
            }

            $data['average_score'] = round($averageScore, 4);

            $rankings->push($data);
        }

        // Urutkan jurusan berdasarkan skor akhir tertinggi (descending)
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
        // Ambil kegiatan yang ada di jurusan tertentu dan LPJ-nya sudah diajukan
        $kegiatans = Kegiatan::with(['kak.ikus', 'lpj'])
            ->where('jurusan_penyelenggara', $jurusan)
            ->whereHas('lpj', function ($query) {
                $query->whereNotNull('submitted_at');
            })
            ->get();

        // Lakukan kalkulasi normalisasi lokal dan bobot
        return $kegiatans->map(function ($kegiatan) {
            $scores = $this->calculateScores($kegiatan);
            $kegiatan->spk_scores = $scores;
            $kegiatan->final_score = $scores['final_score'];
            return $kegiatan;
        })->sortByDesc('final_score')->values();
    }
}

