<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TelaahController extends Controller
{
    public function index()
    {
        $kegiatanList = \App\Models\Kegiatan::with(['statusUtama', 'user'])
            ->atPosition(\App\Services\WorkflowService::POSITION_VERIFIKATOR)
            ->latest()
            ->get();

        $list_usulan = $kegiatanList->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->created_at ? $kegiatan->created_at->format('Y-m-d') : null,
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu'
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
        return view('verifikator.telaah.index', compact('list_usulan', 'jurusan_list'));
    }

    public function show($id)
    {
        $kegiatan = (new \App\Services\KegiatanService())->getDetailLengkap($id);
        $status = $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu';

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

        $kegiatan_data = [
            'nama_pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
            'nim_nip' => $kegiatan->nim_pelaksana,
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_pj' => $kegiatan->nip ?? '-',
            'wadir_tujuan' => $kegiatan->wadir->nama_wadir ?? 'Wadir',
            'penerima_manfaat' => $kegiatan->kak->penerima_manfaat ?? '-',
            'gambaran_umum' => $kegiatan->kak->gambaran_umum ?? '-',
            'metode_pelaksanaan' => $kegiatan->kak->metode_pelaksanaan ?? '-',
            'kode_mak' => $kegiatan->bukti_mak ?? null
        ];

        $catatan_revisi = null;

        return view('verifikator.telaah.show', compact('id', 'status', 'iku_data', 'rab_data', 'kegiatan_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'catatan_revisi', 'kegiatan'));
    }

    public function store(Request $request, $id)
    {
        $workflowService = new \App\Services\WorkflowService();
        $action = $request->input('action');
        
        if ($action == 'approve') {
            $workflowService->moveToNextPosition($id, \App\Services\WorkflowService::POSITION_VERIFIKATOR, \App\Services\WorkflowService::STATUS_TELAH_DIVERIFIKASI, [
                'kode_mak' => $request->input('kode_mak'),
                'dana_disetujui' => $request->input('dana_disetujui'),
                'umpan_balik' => $request->input('umpan_balik_verifikator')
            ]);
        } elseif ($action == 'reject') {
            $workflowService->reject($id, \App\Services\WorkflowService::POSITION_VERIFIKATOR, $request->input('alasan_penolakan') ?? 'Ditolak Verifikator');
        } elseif ($action == 'revise') {
            $fieldComments = [];
            if ($request->has('field_comments')) {
                foreach ($request->input('field_comments') as $table => $columns) {
                    foreach ($columns as $column => $commentText) {
                        if (!empty($commentText)) {
                            $fieldComments[] = [
                                'komentar' => $commentText,
                                'target_tabel' => $table,
                                'target_kolom' => $column,
                            ];
                        }
                    }
                }
            }
            $workflowService->requestRevision(
                $id,
                \App\Services\WorkflowService::POSITION_VERIFIKATOR,
                $request->input('catatan_revisi') ?? 'Perlu Revisi',
                $fieldComments
            );
        }

        return redirect()->route('verifikator.telaah.index')->with('success', 'Telaah berhasil disimpan.');
    }
}
