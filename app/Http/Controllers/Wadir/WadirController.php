<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WadirController extends Controller
{
    public function dashboard()
    {
        $list_usulan = [
            ['id' => 701, 'nama' => 'Sertifikasi IT Internasional', 'pengusul' => 'Ahmad Fauzi', 'nim' => '2407411001', 'prodi' => 'TI', 'jurusan' => 'Teknik Informatika dan Komputer', 'tanggal_pengajuan' => '2026-05-10', 'status' => 'Menunggu'],
            ['id' => 702, 'nama' => 'Lab AI Terpadu', 'pengusul' => 'Siti Aminah', 'nim' => '2407411002', 'prodi' => 'TI', 'jurusan' => 'Teknik Informatika dan Komputer', 'tanggal_pengajuan' => '2026-05-12', 'status' => 'Disetujui'],
            ['id' => 703, 'nama' => 'Seminar Digital Transformation', 'pengusul' => 'Budi Santoso', 'nim' => '2407411003', 'prodi' => 'Elektro', 'jurusan' => 'Teknik Elektro', 'tanggal_pengajuan' => '2026-05-14', 'status' => 'Menunggu'],
            ['id' => 704, 'nama' => 'Studio Desain Grafis', 'pengusul' => 'Rizky Pratama', 'nim' => '2407411004', 'prodi' => 'DG', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'tanggal_pengajuan' => '2026-05-15', 'status' => 'Menunggu'],
            ['id' => 705, 'nama' => 'Workshop Konstruksi Modern', 'pengusul' => 'Lestari Wahyuni', 'nim' => '2407411005', 'prodi' => 'Sipil', 'jurusan' => 'Teknik Sipil', 'tanggal_pengajuan' => '2026-05-16', 'status' => 'Menunggu'],
            ['id' => 706, 'nama' => 'Pengadaan Drone Pemetaan', 'pengusul' => 'Andi Wijaya', 'nim' => '2407411006', 'prodi' => 'Geodesi', 'jurusan' => 'Teknik Sipil', 'tanggal_pengajuan' => '2026-05-17', 'status' => 'Menunggu'],
            ['id' => 707, 'nama' => 'Lomba Robotik Nasional', 'pengusul' => 'Heri Susanto', 'nim' => '2407411007', 'prodi' => 'Elektro', 'jurusan' => 'Teknik Elektro', 'tanggal_pengajuan' => '2026-05-18', 'status' => 'Disetujui'],
            ['id' => 708, 'nama' => 'Webinar Cyber Security', 'pengusul' => 'Diana Putri', 'nim' => '2407411008', 'prodi' => 'TI', 'jurusan' => 'Teknik Informatika dan Komputer', 'tanggal_pengajuan' => '2026-05-19', 'status' => 'Menunggu'],
            ['id' => 709, 'nama' => 'Pameran Karya Mahasiswa', 'pengusul' => 'Fajar Sidik', 'nim' => '2407411009', 'prodi' => 'TGP', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'tanggal_pengajuan' => '2026-05-20', 'status' => 'Menunggu'],
            ['id' => 710, 'nama' => 'Baksos Teknik Mesin', 'pengusul' => 'Gita Permata', 'nim' => '2407411010', 'prodi' => 'Mesin', 'jurusan' => 'Teknik Mesin', 'tanggal_pengajuan' => '2026-05-21', 'status' => 'Menunggu'],
            ['id' => 711, 'nama' => 'Pelatihan AutoCAD 3D', 'pengusul' => 'Irfan Hakim', 'nim' => '2407411011', 'prodi' => 'Sipil', 'jurusan' => 'Teknik Sipil', 'tanggal_pengajuan' => '2026-05-22', 'status' => 'Menunggu'],
            ['id' => 712, 'nama' => 'Kunjungan Industri Jakarta', 'pengusul' => 'Joko Anwar', 'nim' => '2407411012', 'prodi' => 'Mesin', 'jurusan' => 'Teknik Mesin', 'tanggal_pengajuan' => '2026-05-23', 'status' => 'Menunggu'],
        ];

        $stats = [
            'total' => count($list_usulan),
            'disetujui' => 2,
            'menunggu' => 10,
        ];

        $jurusan_list = [
            'Teknik Informatika dan Komputer',
            'Teknik Grafika dan Penerbitan',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Administrasi Niaga',
            'Akuntansi',
        ];
        
        return view('wadir.dashboard', compact('stats', 'list_usulan', 'jurusan_list'));
    }
}
