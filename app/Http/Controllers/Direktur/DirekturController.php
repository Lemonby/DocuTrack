<?php

namespace App\Http\Controllers\Direktur;

use App\Http\Controllers\Controller;
use App\Models\Iku;
use App\Models\Jurusan;
use App\Models\Kegiatan;
use App\Services\WorkflowService;
use Illuminate\Support\Carbon;

class DirekturController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total' => Kegiatan::count(),
            'disetujui' => Kegiatan::whereIn('status_utama_id', [
                WorkflowService::STATUS_DISETUJUI,
                WorkflowService::STATUS_DANA_DIBERIKAN,
            ])->count(),
            'revisi' => Kegiatan::withStatus(WorkflowService::STATUS_REVISI)->count(),
            'ditolak' => Kegiatan::withStatus(WorkflowService::STATUS_DITOLAK)->count(),
            'menunggu' => Kegiatan::whereNotIn('status_utama_id', [
                WorkflowService::STATUS_DISETUJUI,
                WorkflowService::STATUS_DANA_DIBERIKAN,
                WorkflowService::STATUS_DITOLAK,
            ])->count(),
        ];

        $totalAllocated = (float) Kegiatan::sum('dana_di_setujui');
        $totalRealized = (float) Kegiatan::sum('jumlah_dicairkan');
        $remaining = max(0, $totalAllocated - $totalRealized);
        $percentage = $totalAllocated > 0 ? round(($totalRealized / $totalAllocated) * 100, 1) : 0;

        $budget = [
            'total_allocated' => $totalAllocated,
            'total_realized' => $totalRealized,
            'remaining' => $remaining,
            'percentage' => $percentage,
        ];

        $iku_achievements = Iku::orderBy('tahun', 'desc')->take(4)->get()->map(function ($iku) {
            $target = (float) ($iku->target ?? 0);
            $capaian = (float) ($iku->realisasi ?? 0);
            $status = $capaian >= $target ? 'Exceeded' : ($target > 0 && $capaian >= $target * 0.9 ? 'On Track' : 'Warning');

            return [
                'nama' => $iku->indikator_kinerja,
                'target' => $target,
                'capaian' => $capaian,
                'status' => $status,
            ];
        })->toArray();

        $approval_queue = Kegiatan::with('user')->latest()->take(5)->get()->map(function ($kegiatan) {
            $dana = (float) ($kegiatan->dana_di_setujui ?? 0);
            $prioritas = $dana >= 50000000 ? 'High' : ($dana >= 20000000 ? 'Medium' : 'Low');

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->prodi_penyelenggara,
                'dana' => $dana,
                'prioritas' => $prioritas,
            ];
        })->toArray();

        $list_jurusan = Jurusan::orderBy('nama_jurusan')->pluck('nama_jurusan')->toArray();

        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M');
            $data[] = Kegiatan::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        $monthly_trend = [
            'labels' => $labels,
            'data' => $data,
        ];

        return view('direktur.dashboard', compact(
            'stats',
            'budget',
            'iku_achievements',
            'approval_queue',
            'list_jurusan',
            'monthly_trend'
        ));
    }

    public function getDanaPerJurusan()
    {
        $labels = Jurusan::orderBy('nama_jurusan')->pluck('nama_jurusan')->toArray();
        $totals = Kegiatan::selectRaw('jurusan_penyelenggara, SUM(jumlah_dicairkan) as total_dicairkan')
            ->whereNotNull('jumlah_dicairkan')
            ->groupBy('jurusan_penyelenggara')
            ->pluck('total_dicairkan', 'jurusan_penyelenggara');

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'data' => array_map(function ($jurusan) use ($totals) {
                    return (float) ($totals[$jurusan] ?? 0);
                }, $labels),
            ],
        ]);
    }
}
