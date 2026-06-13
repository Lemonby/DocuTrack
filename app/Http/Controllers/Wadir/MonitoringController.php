<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC PPK', 'ACC WD', 'Dana Cair', 'LPJ'];

        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->latest()
            ->get();

        $list_proposal = $kegiatanList->map(function ($kegiatan) {
            // Tentukan tahap berdasarkan pencapaian tertinggi
            if ($kegiatan->status_utama_id == WorkflowService::STATUS_SELESAI || $kegiatan->status_utama_id == WorkflowService::STATUS_LPJ_DISETUJUI) {
                $tahap = 'LPJ';
            } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_BENDAHARA || $kegiatan->status_utama_id == WorkflowService::STATUS_DANA_DIBERIKAN ||
                $kegiatan->status_utama_id == WorkflowService::STATUS_DANA_DIBERIKAN_SEBAGIAN) {
                // Jika sudah di Bendahara, berarti sudah melewati WD, maka masuk tahap Dana Cair
                $tahap = 'Dana Cair';
            } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_WADIR) {
                $tahap = 'ACC WD';
            } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_PPK) {
                $tahap = 'ACC PPK';
            } elseif ($kegiatan->posisi_id == WorkflowService::POSITION_VERIFIKATOR || ($kegiatan->posisi_id > WorkflowService::POSITION_VERIFIKATOR && $kegiatan->bukti_mak ==
                    null)) {
                $tahap = 'Verifikasi';
            } else {
                $tahap = 'Pengajuan';
            }

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tahap_sekarang' => $tahap,
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'In Process',
            ];
        })->toArray();

        $jurusan_list = [
            'Teknik Informatika dan Komputer',
            'Teknik Grafika dan Penerbitan',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Administrasi Niaga',
            'Akuntansi',
        ];

        return view('wadir.monitoring.index', compact('list_proposal', 'tahapan_all', 'jurusan_list'));
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
            WorkflowService::STATUS_REVISI => 'In Process',
            default => 'In Process',
        };
    }
}
