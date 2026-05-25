<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;

use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Models\TahapanPencairan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PencairanDanaController extends Controller
{
    public function index()
    {
        $list_kak = Kegiatan::where('posisi_id', '>=', 5)
            ->with('statusUtama')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($k) {
                $status = ($k->status_utama_id == 5) ? 'Sudah Dicairkan' : 'Belum Dicairkan';
                return [
                    'id' => $k->kegiatan_id,
                    'nama' => $k->nama_kegiatan,
                    'pengusul' => $k->pemilik_kegiatan ?? $k->nama_pj ?? '-',
                    'nim' => $k->nim_pelaksana ?? $k->nip ?? '-',
                    'prodi' => $k->prodi_penyelenggara ?? '-',
                    'jurusan' => $k->jurusan_penyelenggara ?? '-',
                    'tanggal_pengajuan' => $k->created_at ? $k->created_at->format('Y-m-d') : '-',
                    'status' => $status
                ];
            })->toArray();

        return view('bendahara.pencairan-dana.index', compact('list_kak'));
    }

    public function detail($id)
    {
        $kegiatan = Kegiatan::with([
            'kak.rabs.kategori', 
            'kak.indikators', 
            'kak.tahapans', 
            'wadir', 
            'statusUtama',
            'lpj.status',
            'tahapanPencairans'
        ])->findOrFail($id);

        $status = 'Menunggu';
        if ($kegiatan->status_utama_id == 5) {
            $status = 'Dana Diberikan';
        } elseif ($kegiatan->status_utama_id == 3 && $kegiatan->jumlah_dicairkan > 0) {
            $status = 'Dana Belum Diberikan Semua';
        }

        $jumlah_dicairkan = (float)($kegiatan->jumlah_dicairkan ?? 0);
        
        $riwayat_pencairan = $kegiatan->tahapanPencairans->map(function ($t) {
            return [
                'tanggal_pencairan' => $t->tgl_pencairan ? $t->tgl_pencairan->format('Y-m-d') : now()->format('Y-m-d'),
                'termin' => $t->termin,
                'nominal' => (float)$t->nominal,
                'catatan' => $t->catatan
            ];
        })->toArray();

        $rab_data = [];
        $anggaran_disetujui = 0;
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $catName = $rab->kategori ? $rab->kategori->nama_kategori : 'Belanja Barang';
                $total_harga = $rab->vol1 * ($rab->vol2 ?? 1) * $rab->harga;
                $anggaran_disetujui += $total_harga;
                $rab_data[$catName][] = [
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'vol1' => (float)$rab->vol1,
                    'sat1' => $rab->sat1,
                    'vol2' => (float)$rab->vol2,
                    'sat2' => $rab->sat2,
                    'harga' => (float)$rab->harga,
                    'total_harga' => (float)$total_harga,
                ];
            }
        }

        $sisa_dana = $anggaran_disetujui - $jumlah_dicairkan;
        $boleh_cairkan = ($kegiatan->status_utama_id != 5);

        $iku_data = array_filter(explode(',', $kegiatan->kak->iku ?? ''));

        $tahapan_pelaksanaan = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->tahapans as $index => $t) {
                $tahapan_pelaksanaan[$index + 1] = $t->nama_tahapan;
            }
        }

        $indikator_keberhasilan = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->indikators as $index => $ind) {
                $indikator_keberhasilan[$index + 1] = [
                    'target_persen' => $ind->target_persen,
                    'deskripsi' => $ind->indikator_keberhasilan
                ];
            }
        }

        $kegiatan_data = [
            'id'                    => $kegiatan->kegiatan_id,
            'nama_pengusul'         => $kegiatan->pemilik_kegiatan,
            'nim_nip'               => $kegiatan->nim_pelaksana ?? $kegiatan->nip,
            'nim_pengusul'          => $kegiatan->nim_pelaksana,
            'nama_pelaksana'        => $kegiatan->pemilik_kegiatan,
            'nama_penanggung_jawab' => $kegiatan->nama_pj ?? $kegiatan->pemilik_kegiatan,
            'nip_penanggung_jawab'  => $kegiatan->nip ?? $kegiatan->nim_pelaksana,
            'penanggung_jawab'      => $kegiatan->nama_pj ?? $kegiatan->pemilik_kegiatan,
            'nip_pj'                => $kegiatan->nip ?? $kegiatan->nim_pelaksana,
            'jurusan'               => $kegiatan->jurusan_penyelenggara,
            'prodi'                 => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan'         => $kegiatan->nama_kegiatan,
            'wadir_tujuan'          => $kegiatan->wadir ? ('Wakil Direktur Bidang ' . $kegiatan->wadir->nama_wadir) : 'Wakil Direktur',
            'gambaran_umum'         => $kegiatan->kak ? $kegiatan->kak->gambaran_umum : '',
            'metode_pelaksanaan'    => $kegiatan->kak ? $kegiatan->kak->metode_pelaksanaan : '',
            'tanggal_mulai'         => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : '',
            'tanggal_selesai'       => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : '',
        ];

        $lpj_status = 'Belum Ada';
        if ($kegiatan->lpj) {
            $lpj_status = 'Menunggu Verifikasi';
            if ($kegiatan->lpj->status_id == 2) $lpj_status = 'Revisi';
            elseif ($kegiatan->lpj->status_id == 3) $lpj_status = 'Disetujui';
        }

        $kode_mak = $kegiatan->bukti_mak ?? 'MAK-PENDING-REV';
        $catatan_bendahara = $kegiatan->catatan_bendahara;

        return view('bendahara.pencairan-dana.detail', compact(
            'id', 'status', 'lpj_status', 'iku_data', 'rab_data', 'kegiatan_data',
            'tahapan_pelaksanaan', 'indikator_keberhasilan', 'anggaran_disetujui', 'jumlah_dicairkan',
            'sisa_dana', 'boleh_cairkan', 'riwayat_pencairan',
            'kode_mak', 'catatan_bendahara'
        ));
    }

    public function proses(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        $anggaran_disetujui = 0;
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $anggaran_disetujui += $rab->vol1 * ($rab->vol2 ?? 1) * $rab->harga;
            }
        }

        $request->validate([
            'nominalTahapan' => 'required|array',
            'nominalTahapan.*' => 'required',
            'tanggalTahapan' => 'required|array',
            'terminTahapan' => 'required|array',
        ]);

        $total_pencairan_baru = 0;
        foreach ($request->nominalTahapan as $index => $nominalRaw) {
            $nominal = (float)str_replace('.', '', $nominalRaw);
            $total_pencairan_baru += $nominal;

            TahapanPencairan::create([
                'kegiatan_id' => $kegiatan->kegiatan_id,
                'tgl_pencairan' => $request->tanggalTahapan[$index],
                'termin' => $request->terminTahapan[$index],
                'nominal' => $nominal,
                'catatan' => $request->catatan,
                'created_by' => Session::get('user_id'),
            ]);
        }

        $jumlah_dicairkan_total = ($kegiatan->jumlah_dicairkan ?? 0) + $total_pencairan_baru;

        $status_utama_id = 3;
        if ($jumlah_dicairkan_total >= $anggaran_disetujui) {
            $status_utama_id = 5;
        }

        $kegiatan->update([
            'jumlah_dicairkan' => $jumlah_dicairkan_total,
            'status_utama_id' => $status_utama_id,
            'catatan_bendahara' => $request->catatan,
            'tanggal_pencairan' => now(),
        ]);

        return redirect()->route('bendahara.dashboard')->with('success', 'Dana berhasil dicairkan.');
    }
}
