<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $list_kegiatan = [
            [
                'id' => 1,
                'nama' => 'Pengadaan Laptop Laboratorium TI',
                'pengusul' => 'Yovana Ibnu Sina',
                'nim' => '2407411059',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Menunggu',
            ],
            [
                'id' => 2,
                'nama' => 'Workshop UI/UX Design Modern',
                'pengusul' => 'Muhammad Syafri',
                'nim' => '2407411085',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-14',
                'status' => 'Disetujui',
            ],
            [
                'id' => 3,
                'nama' => 'Seminar Nasional Teknologi 2026',
                'pengusul' => 'Budi Santoso',
                'nim' => '2407411001',
                'prodi' => 'Teknik Komputer',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-13',
                'status' => 'Menunggu',
            ],
        ];

        $jurusan_list = [
            'Teknik Informatika dan Komputer',
            'Teknik Elektro',
            'Administrasi Niaga',
            'Akuntansi',
            'Teknik Mesin',
            'Teknik Grafika dan Penerbitan'
        ];

        return view('ppk.kegiatan.index', compact('list_kegiatan', 'jurusan_list'));
    }

    public function show($id)
    {
        // Dummy data detail
        $status = ($id % 2 == 0) ? 'Disetujui' : 'Menunggu';
        
        $kegiatan_data = [
            'nama_pengusul' => 'Yovana Ibnu Sina',
            'nim_nip' => '2407411059',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'Teknik Informatika',
            'nama_penanggung_jawab' => 'Dr. Heri Herwanto',
            'nip_penanggung_jawab' => '198001012005011001',
            'nama_kegiatan' => 'Pengadaan Laptop Laboratorium TI Terpadu',
            'mak_code' => '521211.001.052.A.524111',
            'gambaran_umum' => 'Kegiatan ini bertujuan untuk meremajakan perangkat komputer di Lab TI guna mendukung pembelajaran mata kuliah Pemrograman Web dan AI.',
            'penerima_manfaat' => 'Seluruh mahasiswa tingkat 2 dan 3 prodi Teknik Informatika.',
            'metode_pelaksanaan' => 'Pembelian melalui vendor resmi dengan spesifikasi minimal i7 Gen 13, 16GB RAM.',
            'tahapan_kegiatan' => "1. Survei Harga\n2. Pengajuan Vendor\n3. Verifikasi Barang\n4. Instalasi & Inventarisasi",
            'surat_pengantar' => 'SURAT_PENGANTAR_059.pdf',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-06-30',
        ];

        $iku_data = ['IKU 1: Lulusan Mendapat Pekerjaan Layak', 'IKU 7: Kelas yang Kolaboratif dan Partisipatif'];
        
        $tahapan_pelaksanaan = [
            6 => 'Survei dan Pemilihan Vendor',
            7 => 'Pengiriman dan QC Barang'
        ];

        $indikator_keberhasilan = [
            6 => ['deskripsi' => 'Tersedianya 3 penawaran vendor', 'target_persen' => 100],
            7 => ['deskripsi' => 'Barang sampai dan sesuai spek', 'target_persen' => 100]
        ];

        $rab_data = [
            'Belanja Barang' => [
                ['uraian' => 'Laptop ASUS ROG Strix', 'rincian' => 'Spek i7/16GB/512GB', 'vol1' => 10, 'sat1' => 'Unit', 'harga' => 15000000],
            ],
            'Honorarium' => [
                ['uraian' => 'Honor Tim Teknis', 'rincian' => 'Instalasi Software', 'vol1' => 2, 'sat1' => 'Orang', 'vol2' => 1, 'sat2' => 'Kegiatan', 'harga' => 500000],
            ]
        ];

        return view('ppk.kegiatan.show', compact('id', 'status', 'kegiatan_data', 'iku_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'rab_data'));
    }

    public function store(Request $request, $id)
    {
        // Logika simpan persetujuan
        return redirect()->route('ppk.dashboard')->with('success', 'Usulan #' . $id . ' berhasil diproses.');
    }
}
