<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Session::get('role');

        if (!$role) {
            return redirect('/');
        }

        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'superadmin':
                return redirect()->route('superadmin.dashboard');
            case 'bendahara':
                return redirect()->route('bendahara.dashboard');
            case 'verifikator':
                return redirect()->route('verifikator.dashboard');
            case 'ppk':
                return redirect()->route('ppk.dashboard');
            case 'wadir':
                return redirect()->route('wadir.dashboard');
            case 'direktur':
                return redirect()->route('direktur.dashboard');
            default:
                return redirect('/');
        }
    }

    public function adminDashboard()
    {
        // Mock data for Phase 1 verification
        $stats = [
            'total' => 142,
            'disetujui' => 95,
            'ditolak' => 12,
            'menunggu' => 35
        ];

        $notifications = [
            [
                'id' => 1,
                'title' => 'LPJ Disetujui',
                'message' => 'Laporan Akhir Workshop AI telah disetujui oleh Bendahara.',
                'time' => '5 menit yang lalu',
                'type' => 'success',
                'icon' => 'fa-check-circle'
            ],
            [
                'id' => 2,
                'title' => 'Revisi Diperlukan',
                'message' => 'KAK Pengadaan Server Lab Komputer memerlukan revisi pada bagian RAB.',
                'time' => '1 jam yang lalu',
                'type' => 'warning',
                'icon' => 'fa-exclamation-triangle'
            ],
            [
                'id' => 3,
                'title' => 'Tenggat LPJ Mendekati',
                'message' => 'LPJ Pelatihan Cloud Computing akan berakhir dalam 2 hari.',
                'time' => '3 jam yang lalu',
                'type' => 'danger',
                'icon' => 'fa-clock'
            ],
            [
                'id' => 4,
                'title' => 'Dana Cair',
                'message' => 'Dana untuk Workshop UI/UX Design telah dicairkan oleh Bendahara.',
                'time' => '5 jam yang lalu',
                'type' => 'info',
                'icon' => 'fa-wallet'
            ],
        ];

        $tahapan_kak = ['Draft', 'Review', 'Revisi', 'Disetujui'];
        $tahap_sekarang_kak = 'Review';
        $icons_kak = [
            'Draft' => 'fa-edit',
            'Review' => 'fa-search',
            'Revisi' => 'fa-undo',
            'Disetujui' => 'fa-check'
        ];

        $tahapan_lpj = ['Draft', 'Review', 'Revisi', 'Disetujui'];
        $tahap_sekarang_lpj = 'Draft';
        $icons_lpj = [
            'Draft' => 'fa-edit',
            'Review' => 'fa-search',
            'Revisi' => 'fa-undo',
            'Disetujui' => 'fa-check'
        ];

        $list_kak = [
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

        $list_lpj = [
            [
                'id' => 1,
                'nama' => 'Laporan Akhir Workshop AI',
                'nama_mahasiswa' => 'Rizky Pratama',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(10),
                'tenggatLpj' => now()->addDays(5),
                'status' => 'Menunggu_Upload'
            ],
            [
                'id' => 2,
                'nama' => 'LPJ Pelatihan Cloud Computing Dasar',
                'nama_mahasiswa' => 'Dewi Lestari',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(15),
                'tenggatLpj' => now()->subDays(1),
                'status' => 'Menunggu'
            ],
            [
                'id' => 3,
                'nama' => 'Laporan Kegiatan Lomba Coding',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(20),
                'tenggatLpj' => now()->addDays(10),
                'status' => 'Disetujui'
            ],
            [
                'id' => 4,
                'nama' => 'LPJ Kunjungan Industri 2026',
                'nama_mahasiswa' => 'Siti Aminah',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(25),
                'tenggatLpj' => now()->addDays(2),
                'status' => 'Revisi'
            ],
            [
                'id' => 5,
                'nama' => 'LPJ Pameran Teknologi Tepat Guna',
                'nama_mahasiswa' => 'Budi Santoso',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => now()->subDays(5),
                'tenggatLpj' => now()->addDays(7),
                'status' => 'Telah Direvisi'
            ],
            [
                'id' => 6,
                'nama' => 'LPJ Seminar Nasional Cybersecurity',
                'nama_mahasiswa' => 'Andi Wijaya',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(3),
                'tenggatLpj' => now()->addDays(12),
                'status' => 'Siap_Submit'
            ],
        ];

        return view('admin.dashboard', compact(
            'stats',
            'tahapan_kak',
            'tahap_sekarang_kak',
            'icons_kak',
            'tahapan_lpj',
            'tahap_sekarang_lpj',
            'icons_lpj',
            'list_kak',
            'list_lpj',
            'notifications'
        ));
    }
}
