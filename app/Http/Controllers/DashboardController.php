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
        // DB Integration for Admin Dashboard
        $kegiatanService = new \App\Services\KegiatanService();
        $stats = $kegiatanService->getDashboardStats();

        // Notifications (Mock for now since notifications service isn't specified in requirements)
        $notifications = [
            [
                'id' => 1,
                'title' => 'Sistem Terhubung',
                'message' => 'Integrasi database berhasil.',
                'time' => 'Baru saja',
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

        // Ambil Kegiatan milik Admin (user_id saat ini, fallback ke 1 jika null)
        $userId = \Illuminate\Support\Facades\Session::get('user_id') ?? 1;
        
        $list_kak_db = \App\Models\Kegiatan::with(['statusUtama', 'user'])
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $list_kak = $list_kak_db->map(function($k) {
            return [
                'id' => $k->kegiatan_id,
                'nama' => $k->nama_kegiatan,
                'nama_mahasiswa' => $k->user->nama ?? $k->pemilik_kegiatan,
                'jurusan' => $k->jurusan_penyelenggara,
                'tanggal_pengajuan' => $k->created_at,
                'status' => $k->statusUtama->nama_status_usulan ?? 'Menunggu'
            ];
        })->toArray();

        $list_lpj_db = \App\Models\Kegiatan::with(['statusUtama', 'user', 'lpj'])
            ->where('user_id', $userId)
            ->where('status_utama_id', \App\Services\WorkflowService::STATUS_DANA_DIBERIKAN)
            ->latest()
            ->take(5)
            ->get();

        $list_lpj = $list_lpj_db->map(function($k) {
            $statusLabel = 'menunggu_upload';
            if ($k->lpj) {
                if ($k->lpj->status_id == 1) {
                    $statusLabel = $k->lpj->submitted_at ? 'menunggu' : 'menunggu_upload';
                } elseif ($k->lpj->status_id == 2) {
                    $statusLabel = 'revisi';
                } elseif ($k->lpj->status_id == 3) {
                    $statusLabel = 'disetujui';
                } elseif ($k->lpj->status_id == 4) {
                    $statusLabel = 'ditolak';
                }
            }

            return [
                'id' => $k->kegiatan_id,
                'nama' => 'LPJ ' . $k->nama_kegiatan,
                'nama_mahasiswa' => $k->user->nama ?? $k->pemilik_kegiatan,
                'jurusan' => $k->jurusan_penyelenggara,
                'tanggal_pengajuan' => $k->lpj->submitted_at ?? $k->created_at,
                'tenggatLpj' => $k->lpj->tenggat_lpj ?? ($k->tanggal_selesai ? $k->tanggal_selesai->copy()->addDays(14) : now()->addDays(14)),
                'status' => $statusLabel
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
