<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;

class RiwayatController extends Controller
{
    public function index()
    {
        $list_riwayat = [
            [
                'id' => 402,
                'nama' => 'Lomba Karya Tulis Ilmiah Nasional',
                'pengusul' => 'Dewi Lestari',
                'nim' => '2407411051',
                'prodi' => 'Akuntansi',
                'jurusan' => 'Akuntansi',
                'tgl' => '2026-05-14',
                'status' => 'Dana Diberikan'
            ],
            [
                'id' => 404,
                'nama' => 'Pengadaan Server Cloud Lab Komputer',
                'pengusul' => 'Eko Prasetyo',
                'nim' => '2407411056',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tgl' => '2026-05-10',
                'status' => 'Revisi'
            ],
        ];
        return view('bendahara.riwayat.index', compact('list_riwayat'));
    }

    public function detail($id)
    {
        $id = (int) $id;
        $status_map = [
            402 => 'Dana Diberikan',
            404 => 'Revisi',
        ];
        $status = $status_map[$id] ?? 'Dana Diberikan';

        $kegiatan_data = [
            'id'                    => $id,
            'nama_pengusul'         => 'Dewi Lestari',
            'nim_pengusul'          => '2407411051',
            'nim_nip'               => '2407411051',
            'nama_pelaksana'        => 'UKM Seni & Budaya',
            'nama_penanggung_jawab' => 'Dr. Siti Aminah, M.Pd.',
            'nip_penanggung_jawab'  => '197508122003122001',
            'jurusan'               => 'Akuntansi',
            'prodi'                 => 'D3 Akuntansi',
            'nama_kegiatan'         => 'Lomba Karya Tulis Ilmiah Nasional',
            'wadir_tujuan'          => 'Wakil Direktur Bidang Kemahasiswaan',
            'penerima_manfaat'      => 'Mahasiswa aktif tingkat nasional',
            'gambaran_umum'         => 'Kegiatan lomba tahunan untuk meningkatkan literasi dan inovasi mahasiswa di bidang karya tulis ilmiah.',
            'metode_pelaksanaan'    => 'Seleksi daring dilanjutkan dengan presentasi luring bagi finalis 10 besar.',
            'tahapan_kegiatan'      => "1. Pembukaan pendaftaran\n2. Pengumpulan abstrak\n3. Seleksi naskah penuh\n4. Grand Final",
            'tanggal_mulai'         => '2026-05-14',
            'tanggal_selesai'       => '2026-05-16',
        ];

        $rab_data = [
            'Belanja Barang' => [
                ['uraian' => 'Konsumsi', 'rincian' => 'Snack & Lunch Box', 'vol1' => 50, 'sat1' => 'Paket', 'vol2' => 1, 'sat2' => 'Kali', 'harga' => 35000],
                ['uraian' => 'Tropi',    'rincian' => 'Piala Juara 1-3',       'vol1' => 3,  'sat1' => 'Set',   'vol2' => 1, 'sat2' => 'Kali', 'harga' => 250000],
            ],
            'Belanja Jasa' => [
                ['uraian' => 'Honor Juri', 'rincian' => 'Juri Pakar Akademisi', 'vol1' => 3, 'sat1' => 'Orang', 'vol2' => 1, 'sat2' => 'Kali', 'harga' => 1000000],
            ],
        ];

        $anggaran_disetujui = 0;
        foreach ($rab_data as $items) {
            foreach ($items as $item) {
                $anggaran_disetujui += $item['vol1'] * ($item['vol2'] ?? 1) * $item['harga'];
            }
        }

        $jumlah_dicairkan = ($status === 'Dana Diberikan') ? $anggaran_disetujui : 0;
        $tanggal_pencairan = ($status === 'Dana Diberikan') ? '2026-05-15' : null;
        $metode_pencairan = ($status === 'Dana Diberikan') ? 'Langsung (Lunas)' : '-';
        $kode_mak = 'MAK/2026/0402/ACC';
        
        $riwayat_pencairan = ($status === 'Dana Diberikan') ? [
            ['tanggal_pencairan' => '2026-05-15', 'termin' => 'Lunas', 'nominal' => $anggaran_disetujui, 'catatan' => 'Pembayaran lunas untuk pelaksanaan kegiatan.']
        ] : [];

        $iku_data = [
            'IKU 2 - Mahasiswa berprestasi tingkat nasional',
        ];

        $indikator_data = [
            ['bulan' => 'Mei', 'nama' => 'Pendaftaran mencapai 100 tim', 'target' => 100],
        ];

        $catatan_revisi = ($status === 'Revisi') ? 'Nota pembelian belum dilampirkan dengan lengkap.' : null;

        return view('bendahara.riwayat.detail', compact(
            'id', 'status', 'kegiatan_data', 'rab_data', 'catatan_revisi',
            'anggaran_disetujui', 'jumlah_dicairkan', 'tanggal_pencairan',
            'metode_pencairan', 'kode_mak', 'riwayat_pencairan', 'iku_data', 'indikator_data'
        ));
    }
}
