<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Services\SpkMautService;
use Illuminate\Http\Request;

/**
 * Class IntegritasController
 * 
 * Kontroler khusus Direktur untuk menyajikan data halaman Integritas Jurusan
 * menggunakan metode SPK MAUT (Multi-Attribute Utility Theory).
 */
class IntegritasController extends Controller
{
    /**
     * @var SpkMautService Layanan untuk mengelola perhitungan SPK MAUT
     */
    protected SpkMautService $spkService;

    /**
     * Konstruktor untuk menyuntikkan (inject) dependensi SpkMautService.
     * 
     * @param SpkMautService $spkService Layanan penghitung SPK MAUT.
     */
    public function __construct(SpkMautService $spkService)
    {
        $this->spkService = $spkService;
    }

    /**
     * Menampilkan halaman dashboard Integritas Jurusan (Leaderboard SPK MAUT)
     * beserta visualisasi grafik rata-rata kriteria dan rincian nilai per KAK.
     * 
     * @param Request $request Menampung query parameter 'jurusan' untuk filter detail.
     * @return \Illuminate\View\View Halaman view dengan data peringkat, jurusan terpilih, dan rincian kegiatannya.
     */
    public function index(Request $request)
    {
        // 1. Ambil daftar ranking seluruh jurusan beserta data kegiatannya yang ter-LPJ
        $rankings = $this->spkService->getJurusanRankings();

        // 2. Tentukan jurusan terpilih untuk ditampilkan detail kegiatan/KAK-nya.
        // Jika ada parameter 'jurusan', gunakan parameter tersebut.
        // Jika tidak, default-kan ke jurusan di peringkat pertama (skor tertinggi).
        $selectedJurusanName = $request->query('jurusan');
        if (!$selectedJurusanName && $rankings->isNotEmpty()) {
            $selectedJurusanName = $rankings->first()['jurusan'];
        }

        // 3. Ambil data spesifik jurusan terpilih dari koleksi ranking
        $selectedRankData = $rankings->firstWhere('jurusan', $selectedJurusanName);

        // Jika jurusan terpilih ditemukan, ambil daftar kegiatannya. Jika tidak, buat koleksi kosong.
        $selectedKegiatans = $selectedRankData ? $selectedRankData['kegiatans'] : collect();

        // 4. Kirimkan seluruh data hasil perhitungan MAUT ke view
        return view('direktur.integritas.index', [
            'rankings' => $rankings,
            'selectedJurusan' => $selectedJurusanName,
            'selectedRankData' => $selectedRankData,
            'selectedKegiatans' => $selectedKegiatans,
        ]);
    }
}
