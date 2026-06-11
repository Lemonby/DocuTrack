<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubmitRincianRequest;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use Illuminate\Support\Facades\Session;

class KegiatanController extends Controller
{
    public function index()
    {
        $userId = Session::get('user_id') ?? 1;
        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $list_kegiatan = $kegiatanList->map(function ($kegiatan) {
            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'nama_mahasiswa' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->created_at,
                'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu',
                'posisi' => $kegiatan->posisi_id,
                'statusUtamaId' => $kegiatan->status_utama_id,
            ];
        })->toArray();

        return view('admin.kegiatan.index', compact('list_kegiatan'));
    }

    public function detail($id)
    {
        $kegiatan = Kegiatan::with(['statusUtama'])->findOrFail($id);
        $status = $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu';

        $detail_data = [
            'penanggung_jawab' => $kegiatan->nama_pj ?? '',
            'nim_nip_pj' => $kegiatan->nip ?? '',
            'tanggal_mulai' => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : null,
            'tanggal_selesai' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : null,
            'surat_pengantar' => $kegiatan->surat_pengantar ?? null,
        ];

        return view('admin.kegiatan.detail', compact('id', 'status', 'detail_data'));
    }

    public function storeRincian(SubmitRincianRequest $request)
    {
        if ($request->has('kegiatan_id')) {
            $file = $request->file('surat_pengantar');
            (new KegiatanService)->submitRincian(
                $request->kegiatan_id,
                $request->all(),
                $file
            );
        }

        return redirect()->route('admin.kegiatan.index')->with('success', 'Rincian kegiatan berhasil disimpan.');
    }
}
