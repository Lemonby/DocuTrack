<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Session::get('role');

        if (! $role) {
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
        $role = Session::get('role') ?? 'admin';

        // DB Integration for Admin Dashboard
        $kegiatanService = new KegiatanService;
        $stats = $kegiatanService->getDashboardStats($jurusan, $role);

        // Notifications (Mock for now since notifications service isn't specified in requirements)
        $notifications = [
            [
                'id' => 1,
                'title' => 'Sistem Terhubung',
                'message' => 'Integrasi database berhasil.',
                'time' => 'Baru saja',
                'type' => 'success',
                'icon' => 'fa-check-circle',
            ],
        ];

        $tahapan_kak = ['Draft', 'Review', 'Revisi', 'Disetujui'];
        $tahap_sekarang_kak = 'Review';
        $icons_kak = [
            'Draft' => 'fa-edit',
            'Review' => 'fa-search',
            'Revisi' => 'fa-undo',
            'Disetujui' => 'fa-check',
        ];

        $tahapan_lpj = ['Draft', 'Review', 'Revisi', 'Disetujui'];
        $tahap_sekarang_lpj = 'Draft';
        $icons_lpj = [
            'Draft' => 'fa-edit',
            'Review' => 'fa-search',
            'Revisi' => 'fa-undo',
            'Disetujui' => 'fa-check',
        ];

        // Ambil Kegiatan milik Admin (sesuai jurusan jika ada, fallback ke user_id)
        $userId = Session::get('user_id') ?? 1;

        $listKakQuery = Kegiatan::with(['statusUtama', 'user']);
        $listKakQuery->where('user_id', $userId);
        $list_kak_db = $listKakQuery->latest()->get();

        $list_kak = $list_kak_db->map(function ($k) {
            return [
                'id' => $k->kegiatan_id,
                'nama' => $k->nama_kegiatan,
                'nama_mahasiswa' => $k->user->nama ?? $k->pemilik_kegiatan,
                'jurusan' => $k->jurusan_penyelenggara,
                'tanggal_pengajuan' => $k->created_at,
                'status' => $k->statusUtama->nama_status_usulan ?? 'Menunggu',
            ];
        })->toArray();

        $listLpjQuery = Kegiatan::with(['statusUtama', 'user', 'lpj'])
            ->whereIn('status_utama_id', [
                WorkflowService::STATUS_DANA_DIBERIKAN,
                6,
                8,
            ]);
        $listLpjQuery->where('user_id', $userId);
        $list_lpj_db = $listLpjQuery->latest()->get();

        $list_lpj = $list_lpj_db->map(function ($k) {
            $statusLabel = 'menunggu_upload';
            if ($k->status_utama_id == 8) {
                $statusLabel = 'selesai';
            } elseif ($k->lpj) {
                if ($k->lpj->status_id == 1) {
                    $statusLabel = $k->lpj->submitted_at ? 'menunggu' : 'menunggu_upload';
                } elseif ($k->lpj->status_id == 2) {
                    $statusLabel = 'revisi';
                } elseif ($k->lpj->status_id == 3) {
                    $statusLabel = 'disetujui';
                } elseif ($k->lpj->status_id == 4) {
                    $statusLabel = 'ditolak';
                } elseif ($k->lpj->status_id == 8) {
                    $statusLabel = 'selesai';
                }
            }

            $tenggatLpj = now()->addDays(14)->toDateString();
            if ($k->lpj && $k->lpj->tenggat_lpj) {
                $tenggatLpj = $k->lpj->tenggat_lpj->toDateString();
            } elseif ($k->tanggal_selesai) {
                try {
                    $tenggatLpj = \Carbon\Carbon::parse($k->tanggal_selesai)->addDays(14)->toDateString();
                } catch (\Exception $e) {
                    // Fallback to now
                }
            }

            return [
                'id' => $k->kegiatan_id,
                'nama' => 'LPJ '.$k->nama_kegiatan,
                'nama_mahasiswa' => $k->user->nama ?? $k->pemilik_kegiatan,
                'jurusan' => $k->jurusan_penyelenggara,
                'tanggal_pengajuan' => $k->lpj->submitted_at ?? $k->created_at,
                'tenggatLpj' => $tenggatLpj,
                'status' => $statusLabel,
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
