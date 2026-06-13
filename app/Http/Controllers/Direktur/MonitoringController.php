<?php

namespace App\Http\Controllers\Direktur;

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

        return view('direktur.monitoring.index', compact('list_jurusan'));
    }

    public function getData(Request $request)
    {
        $status = strtolower($request->status ?? 'semua');
        $jurusan = $request->jurusan;
        $search = $request->search;
        $perPage = 5;

        $query = Kegiatan::with(['statusUtama', 'user', 'lpj', 'kak.rabs']);

        if ($jurusan && $jurusan !== 'semua') {
            $query->byJurusan($jurusan);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', '%'.$search.'%')
                    ->orWhere('pemilik_kegiatan', 'like', '%'.$search.'%')
                    ->orWhere('nim_pelaksana', 'like', '%'.$search.'%');
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
            } elseif ($status === 'lpj') {
                $query->whereHas('lpj');
            }
        }

        $paginator = $query->latest()->paginate($perPage);

        $proposals = $paginator->getCollection()->map(function ($kegiatan) {
            $dana = (float) ($kegiatan->jumlah_dicairkan ?? 0);
            if ($dana === 0 && $kegiatan->kak) {
                $dana = (float) $kegiatan->kak->rabs->sum('total_harga');
            }

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tahap_sekarang' => $this->mapTahapDirektur($kegiatan),
                'status' => $this->mapStatusLabel($kegiatan),
                'dana' => $dana,
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

    private function mapTahapDirektur(Kegiatan $kegiatan): string
    {
        // Tentukan tahap berdasarkan pencapaian tertinggi
        if ($kegiatan->status_utama_id == WorkflowService::STATUS_SELESAI || $kegiatan->status_utama_id == WorkflowService::STATUS_LPJ_DISETUJUI) {
            return 'LPJ';
        } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_BENDAHARA || $kegiatan->status_utama_id == WorkflowService::STATUS_DANA_DIBERIKAN ||
            $kegiatan->status_utama_id == WorkflowService::STATUS_DANA_DIBERIKAN_SEBAGIAN) {
            return 'Dana Cair';
        } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_WADIR) {
            return 'ACC WD';
        } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_PPK) {
            return 'ACC PPK';
        } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_VERIFIKATOR || ($kegiatan->posisi_id > WorkflowService::POSITION_VERIFIKATOR && $kegiatan->bukti_mak ==
                null)) {
            return 'Verifikasi';
        } else {
            return 'Pengajuan';
        }
    }

    private function mapStatusLabel(Kegiatan $kegiatan): string
    {
        return match ((int) $kegiatan->status_utama_id) {
            WorkflowService::STATUS_MENUNGGU => 'Menunggu',
            WorkflowService::STATUS_DISETUJUI, WorkflowService::STATUS_DANA_DIBERIKAN => 'Approved',
            WorkflowService::STATUS_DITOLAK => 'Ditolak',
            WorkflowService::STATUS_REVISI => 'Revisi',
            default => 'In Process',
        };
    }
}
