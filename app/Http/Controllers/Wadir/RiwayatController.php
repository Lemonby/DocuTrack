<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\ProgressHistory;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Session::get('user_id') ?? auth()->id();

        $historyQuery = ProgressHistory::with(['kegiatan.user', 'status'])
            ->when($userId, fn ($q) => $q->where('changed_by_user_id', $userId))
            ->whereHas('kegiatan', function ($q) {
                $q->where('posisi_id', '>=', WorkflowService::POSITION_WADIR);
            })
            ->latest('created_at')
            ->get()
            ->groupBy('kegiatan_id')
            ->map(function ($items) {
                return $items->first();
            });

        $list_riwayat = $historyQuery->map(function ($history) {
            $kegiatan = $history->kegiatan;
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tgl' => $history->created_at ? $history->created_at->format('Y-m-d') : null,
                'status' => $history->status->nama_status_usulan ?? 'Disetujui',
            ];
        })->values()->toArray();

        $jurusan_list = Jurusan::orderBy('nama_jurusan')->pluck('nama_jurusan')->toArray();

        return view('wadir.riwayat.index', compact('list_riwayat', 'jurusan_list'));
    }
}
