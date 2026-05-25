<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Kegiatan;
use App\Models\Lpj;

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
        $jurusan = Session::get('jurusan');

        // Stats based on real database records for this department
        $stats = [
            'total' => Kegiatan::where('jurusan_penyelenggara', $jurusan)->count(),
            'disetujui' => Kegiatan::where('jurusan_penyelenggara', $jurusan)->whereIn('status_utama_id', [3, 5])->count(),
            'ditolak' => Kegiatan::where('jurusan_penyelenggara', $jurusan)->where('status_utama_id', 4)->count(),
            'menunggu' => Kegiatan::where('jurusan_penyelenggara', $jurusan)->where('status_utama_id', 1)->count()
        ];

        $notifications = [
            [
                'id' => 1,
                'title' => 'LPJ Disetujui',
                'message' => 'Laporan Akhir Workshop AI telah disetujui oleh Bendahara.',
                'time' => '5 menit yang lalu',
                'type' => 'success',
                'icon' => 'fa-check-circle'
            ]
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

        // Fetch real KAK / Kegiatan for the department
        $list_kak = Kegiatan::where('jurusan_penyelenggara', $jurusan)
            ->with(['statusUtama'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->kegiatan_id,
                    'nama' => $k->nama_kegiatan,
                    'nama_mahasiswa' => $k->pemilik_kegiatan ?? $k->nama_pj ?? '-',
                    'jurusan' => $k->jurusan_penyelenggara,
                    'tanggal_pengajuan' => $k->created_at ? $k->created_at->format('Y-m-d') : '-',
                    'status' => $k->statusUtama ? $k->statusUtama->nama_status_usulan : 'Menunggu'
                ];
            })->toArray();

        // Fetch real LPJs for the department
        $list_lpj = Lpj::whereHas('kegiatan', function ($q) use ($jurusan) {
                $q->where('jurusan_penyelenggara', $jurusan);
            })
            ->with(['kegiatan', 'status'])
            ->get()
            ->map(function ($l) {
                $statusMap = [
                    1 => 'menunggu_upload',
                    2 => 'menunggu',
                    3 => 'disetujui',
                    4 => 'revisi',
                    5 => 'telah_direvisi',
                    6 => 'siap_submit'
                ];
                $statusStr = $statusMap[$l->status_id] ?? 'menunggu';
                return [
                    'id' => $l->lpj_id,
                    'nama' => $l->kegiatan ? $l->kegiatan->nama_kegiatan : '-',
                    'nama_mahasiswa' => $l->kegiatan ? ($l->kegiatan->pemilik_kegiatan ?? $l->kegiatan->nama_pj) : '-',
                    'jurusan' => $l->kegiatan ? $l->kegiatan->jurusan_penyelenggara : '-',
                    'tanggal_pengajuan' => $l->submitted_at ? $l->submitted_at->format('Y-m-d') : ($l->created_at ? $l->created_at->format('Y-m-d') : '-'),
                    'tenggatLpj' => $l->tenggat_lpj ? $l->tenggat_lpj->format('Y-m-d') : '-',
                    'status' => $statusStr
                ];
            })->toArray();

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

