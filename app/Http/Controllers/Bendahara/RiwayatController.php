<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;

class RiwayatController extends Controller
{
    public function index()
    {
        $list_riwayat = \App\Models\Kegiatan::where('posisi_id', '>=', 5)
            ->with('statusUtama')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($k) {
                $status = ($k->status_utama_id == 5) ? 'Dana Diberikan' : 'Belum Dicairkan';
                return [
                    'id' => $k->kegiatan_id,
                    'nama' => $k->nama_kegiatan,
                    'pengusul' => $k->pemilik_kegiatan ?? $k->nama_pj ?? '-',
                    'nim' => $k->nim_pelaksana ?? $k->nip ?? '-',
                    'prodi' => $k->prodi_penyelenggara ?? '-',
                    'jurusan' => $k->jurusan_penyelenggara ?? '-',
                    'tgl' => $k->updated_at ? $k->updated_at->format('Y-m-d') : '-',
                    'status' => $status
                ];
            })->toArray();

        return view('bendahara.riwayat.index', compact('list_riwayat'));
    }

    public function detail($id)
    {
        $kegiatan = \App\Models\Kegiatan::with([
            'kak.rabs.kategori', 
            'kak.indikators', 
            'kak.tahapans', 
            'wadir', 
            'statusUtama',
            'tahapanPencairans'
        ])->findOrFail($id);

        $status = ($kegiatan->status_utama_id == 5) ? 'Dana Diberikan' : 'Belum Dicairkan';

        $kegiatan_data = [
            'id'                    => $kegiatan->kegiatan_id,
            'nama_pengusul'         => $kegiatan->pemilik_kegiatan,
            'nim_pengusul'          => $kegiatan->nim_pelaksana,
            'nim_nip'               => $kegiatan->nim_pelaksana ?? $kegiatan->nip,
            'nama_pelaksana'        => $kegiatan->pemilik_kegiatan,
            'nama_penanggung_jawab' => $kegiatan->nama_pj ?? $kegiatan->pemilik_kegiatan,
            'nip_penanggung_jawab'  => $kegiatan->nip ?? $kegiatan->nim_pelaksana,
            'jurusan'               => $kegiatan->jurusan_penyelenggara,
            'prodi'                 => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan'         => $kegiatan->nama_kegiatan,
            'wadir_tujuan'          => $kegiatan->wadir ? ('Wakil Direktur Bidang ' . $kegiatan->wadir->nama_wadir) : 'Wakil Direktur',
            'penerima_manfaat'      => $kegiatan->kak ? $kegiatan->kak->penerima_manfaat : '',
            'gambaran_umum'         => $kegiatan->kak ? $kegiatan->kak->gambaran_umum : '',
            'metode_pelaksanaan'    => $kegiatan->kak ? $kegiatan->kak->metode_pelaksanaan : '',
            'tanggal_mulai'         => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : '',
            'tanggal_selesai'       => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : '',
        ];

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

        $jumlah_dicairkan = (float)($kegiatan->jumlah_dicairkan ?? 0);
        $tanggal_pencairan = $kegiatan->tanggal_pencairan ? $kegiatan->tanggal_pencairan->format('Y-m-d') : null;
        $metode_pencairan = $kegiatan->metode_pencairan ?? 'Transfer';
        $kode_mak = $kegiatan->bukti_mak ?? 'MAK-PENDING-REV';

        $riwayat_pencairan = $kegiatan->tahapanPencairans->map(function ($t) {
            return [
                'tanggal_pencairan' => $t->tgl_pencairan ? $t->tgl_pencairan->format('Y-m-d') : now()->format('Y-m-d'),
                'termin' => $t->termin,
                'nominal' => (float)$t->nominal,
                'catatan' => $t->catatan
            ];
        })->toArray();

        $iku_data = array_filter(explode(',', $kegiatan->kak->iku ?? ''));

        $indikator_data = [];
        if ($kegiatan->kak) {
            $nama_bulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            foreach ($kegiatan->kak->indikators as $index => $ind) {
                $indikator_data[] = [
                    'bulan' => $nama_bulan[$ind->bulan] ?? 'Bulan ' . $ind->bulan,
                    'nama' => $ind->indikator_keberhasilan,
                    'target' => $ind->target_persen
                ];
            }
        }

        $catatan_revisi = $kegiatan->umpan_balik_verifikator;

        return view('bendahara.riwayat.detail', compact(
            'id', 'status', 'kegiatan_data', 'rab_data', 'catatan_revisi',
            'anggaran_disetujui', 'jumlah_dicairkan', 'tanggal_pencairan',
            'metode_pencairan', 'kode_mak', 'riwayat_pencairan', 'iku_data', 'indikator_data'
        ));
    }
}
