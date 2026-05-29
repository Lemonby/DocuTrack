<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->atPosition(WorkflowService::POSITION_PPK)
            ->latest()
            ->get();

        $list_kegiatan = $kegiatanList->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->created_at ? $kegiatan->created_at->format('Y-m-d') : null,
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu',
            ];
        })->toArray();

        $jurusan_list = [
            'Teknik Informatika dan Komputer',
            'Teknik Elektro',
            'Administrasi Niaga',
            'Akuntansi',
            'Teknik Mesin',
            'Teknik Grafika dan Penerbitan'
        ];

        return view('ppk.kegiatan.index', compact('list_kegiatan', 'jurusan_list'));
    }

    public function show($id)
    {
        $kegiatan = (new KegiatanService())->getDetailLengkap($id);
        
        $status = $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu';
        
        $kegiatan_data = [
            'nama_pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
            'nim_nip' => $kegiatan->nim_pelaksana,
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_penanggung_jawab' => $kegiatan->nip ?? '-',
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'mak_code' => $kegiatan->bukti_mak ?? '-',
            'gambaran_umum' => $kegiatan->kak->gambaran_umum ?? '-',
            'penerima_manfaat' => $kegiatan->kak->penerima_manfaat ?? '-',
            'metode_pelaksanaan' => $kegiatan->kak->metode_pelaksanaan ?? '-',
            'tahapan_kegiatan' => $kegiatan->kak ? $kegiatan->kak->tahapans->pluck('nama_tahapan')->implode("\n") : '',
            'surat_pengantar' => $kegiatan->surat_pengantar,
            'tanggal_mulai' => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : '-',
            'tanggal_selesai' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : '-',
        ];

        $iku_data = $kegiatan->kak ? explode(',', $kegiatan->kak->iku ?? '') : [];
        
        $tahapan_pelaksanaan = [];
        $indikator_keberhasilan = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->indikators as $ind) {
                if ($ind->bulan) {
                    $indikator_keberhasilan[$ind->bulan] = [
                        'deskripsi' => $ind->indikator_keberhasilan,
                        'target_persen' => $ind->target_persen
                    ];
                }
            }
            foreach ($kegiatan->kak->tahapans as $key => $tahap) {
                $tahapan_pelaksanaan[$key] = $tahap->nama_tahapan;
            }
        }

        $rab_data = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $cat = $rab->kategori->nama_kategori ?? 'Lainnya';
                $rab_data[$cat][] = [
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'vol1' => $rab->vol1,
                    'sat1' => $rab->sat1,
                    'vol2' => $rab->vol2,
                    'sat2' => $rab->sat2,
                    'harga' => $rab->harga
                ];
            }
        }

        return view('ppk.kegiatan.show', compact('id', 'status', 'kegiatan_data', 'iku_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'rab_data', 'kegiatan'));
    }

    public function store(Request $request, $id)
    {
        $workflowService = new WorkflowService();
        // Assume basic approval for now. You can expand based on request logic.
        $workflowService->moveToNextPosition($id, WorkflowService::POSITION_PPK);
        
        return redirect()->route('ppk.dashboard')->with('success', 'Usulan #' . $id . ' berhasil diproses.');
    }
}
