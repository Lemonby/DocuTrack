<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $list_kegiatan = [
            [
                'id' => 1,
                'nama' => 'Peningkatan Kompetensi AI Mahasiswa TI',
                'nama_mahasiswa' => 'Yovana Ibnu Sina',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(2),
                'status' => 'Review'
            ],
            [
                'id' => 2,
                'nama' => 'Workshop UI/UX Design Modern',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(5),
                'status' => 'Disetujui'
            ],
            [
                'id' => 3,
                'nama' => 'Lomba Karya Tulis Ilmiah 2026',
                'nama_mahasiswa' => 'Siti Aminah',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(1),
                'status' => 'Menunggu'
            ],
            [
                'id' => 4,
                'nama' => 'Pengadaan Server Lab Komputer',
                'nama_mahasiswa' => 'Rizky Pratama',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(7),
                'status' => 'Revisi'
            ],
            [
                'id' => 5,
                'nama' => 'Seminar Nasional Cybersecurity',
                'nama_mahasiswa' => 'Dewi Lestari',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(3),
                'status' => 'Ditolak'
            ],
        ];
        return view('admin.kegiatan.index', compact('list_kegiatan'));
    }

    public function detail($id)
    {
        // Simulate finding the item
        // Sync with DashboardController KAK IDs
        $status_map = [
            1 => 'Review',
            2 => 'Disetujui',
            3 => 'Menunggu',
            4 => 'Revisi',
            5 => 'Ditolak',
            201 => 'Review',
            202 => 'Disetujui',
            203 => 'Menunggu',
            204 => 'Selesai',
        ];

        $status = $status_map[$id] ?? 'Review';

        // Mock data for activity details if already completed
        $detail_data = [
            'penanggung_jawab' => 'Andi Wijaya',
            'nim_nip_pj' => '2407411060',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-06-03',
            'surat_pengantar' => 'surat_pengantar_seminar.pdf'
        ];

        return view('admin.kegiatan.detail', compact('id', 'status', 'detail_data'));
    }

    public function storeRincian(Request $request)
    {
        // Placeholder for store rincian logic
        return redirect()->route('admin.kegiatan.index')->with('success', 'Rincian kegiatan berhasil disimpan.');
    }
}
