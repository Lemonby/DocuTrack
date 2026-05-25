<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\Kegiatan;

class KegiatanController extends Controller
{
    public function index()
    {
        $jurusan = Session::get('jurusan');
        $list_kegiatan = Kegiatan::where('jurusan_penyelenggara', $jurusan)
            ->where(function($q) {
                $q->whereIn('status_utama_id', [3, 5])
                  ->orWhereNotNull('bukti_mak');
            })
            ->with('statusUtama')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->kegiatan_id,
                    'nama' => $k->nama_kegiatan,
                    'nama_mahasiswa' => $k->pemilik_kegiatan ?? $k->nama_pj ?? '-',
                    'jurusan' => $k->jurusan_penyelenggara,
                    'tanggal_pengajuan' => $k->created_at ? $k->created_at->format('Y-m-d') : '-',
                    'status' => $k->statusUtama ? $k->statusUtama->nama_status_usulan : 'Disetujui'
                ];
            })->toArray();

        return view('admin.kegiatan.index', compact('list_kegiatan'));
    }

    public function detail($id)
    {
        $kegiatan = Kegiatan::with('statusUtama')->findOrFail($id);
        $status = $kegiatan->statusUtama ? $kegiatan->statusUtama->nama_status_usulan : 'Disetujui';

        $detail_data = [
            'penanggung_jawab' => $kegiatan->nama_pj ?? '',
            'nim_nip_pj' => $kegiatan->nip ?? '',
            'tanggal_mulai' => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : '',
            'tanggal_selesai' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : '',
            'surat_pengantar' => $kegiatan->surat_pengantar ?? ''
        ];

        return view('admin.kegiatan.detail', compact('id', 'status', 'detail_data'));
    }

    public function storeRincian(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required',
            'penanggung_jawab' => 'required',
            'nim_nip_pj' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $kegiatan = Kegiatan::findOrFail($request->kegiatan_id);

        $path = $kegiatan->surat_pengantar;
        if ($request->hasFile('surat_pengantar')) {
            // Delete old file if exists
            if ($kegiatan->surat_pengantar) {
                Storage::disk('public')->delete($kegiatan->surat_pengantar);
            }
            $file = $request->file('surat_pengantar');
            $path = $file->store('surat_pengantars', 'public');
        }

        $kegiatan->update([
            'nama_pj' => $request->penanggung_jawab,
            'nip' => $request->nim_nip_pj,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'surat_pengantar' => $path,
            'posisi_id' => $kegiatan->posisi_id < 3 ? 3 : $kegiatan->posisi_id,
        ]);

        return redirect()->route('admin.kegiatan.index')->with('success_message', 'Rincian kegiatan berhasil disimpan.');
    }
}

