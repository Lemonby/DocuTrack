<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kegiatan;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $list_jurusan = Jurusan::orderBy('nama_jurusan')->pluck('nama_jurusan')->toArray();
        return view('wadir.monitoring.index', compact('list_jurusan'));
    }

    public function getData(Request $request)
    {
        $status = strtolower($request->status ?? 'semua');
        $jurusan = $request->jurusan;
        $search = $request->search;
        $perPage = 5;

        $query = Kegiatan::with(['statusUtama', 'user', 'lpj']);

        if ($jurusan && $jurusan !== 'semua') {
            $query->byJurusan($jurusan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', '%' . $search . '%')
                    ->orWhere('pemilik_kegiatan', 'like', '%' . $search . '%')
                    ->orWhere('nim_pelaksana', 'like', '%' . $search . '%');
            });
        }

        if ($status && $status !== 'semua') {
            if ($status === 'menunggu') {
                $query->withStatus(WorkflowService::STATUS_MENUNGGU);
            } elseif ($status === 'approved') {
                $query->whereIn('status_utama_id', [
                    WorkflowService::STATUS_DISETUJUI,
                    WorkflowService::STATUS_DANA_DIBERIKAN,
                ]);
            } elseif ($status === 'ditolak') {
                $query->withStatus(WorkflowService::STATUS_DITOLAK);
            } elseif ($status === 'in process') {
                $query->withStatus(WorkflowService::STATUS_REVISI);
            }
        }

        $paginator = $query->latest()->paginate($perPage);

        $proposals = $paginator->getCollection()->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'nama_lengkap' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tahap_sekarang' => $this->mapTahapWadir($kegiatan),
                'status' => $this->mapStatusLabel($kegiatan),
            ];
        })->toArray();

        return response()->json([
            'proposals' => $proposals,
            'pagination' => [
                'totalItems' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
                'currentPage' => $paginator->currentPage(),
                'showingFrom' => $paginator->firstItem() ?? 0,
                'showingTo' => $paginator->lastItem() ?? 0,
            ],
        ]);
    }

    private function mapTahapWadir(Kegiatan $kegiatan): string
    {
        if ($kegiatan->lpj && $kegiatan->lpj->submitted_at) {
            return 'LPJ';
        }

        return match ((int) $kegiatan->posisi_id) {
            WorkflowService::POSITION_ADMIN => 'Pengajuan',
            WorkflowService::POSITION_VERIFIKATOR => 'Verifikasi',
            WorkflowService::POSITION_PPK => 'ACC PPK',
            WorkflowService::POSITION_WADIR => 'ACC WD',
            WorkflowService::POSITION_BENDAHARA => 'Dana Cair',
            default => 'Pengajuan',
        };
    }

    private function mapStatusLabel(Kegiatan $kegiatan): string
    {
        return match ((int) $kegiatan->status_utama_id) {
            WorkflowService::STATUS_MENUNGGU => 'Menunggu',
            WorkflowService::STATUS_DISETUJUI, WorkflowService::STATUS_DANA_DIBERIKAN => 'Approved',
            WorkflowService::STATUS_DITOLAK => 'Ditolak',
            WorkflowService::STATUS_REVISI => 'In Process',
            default => 'In Process',
        };
    }
}
