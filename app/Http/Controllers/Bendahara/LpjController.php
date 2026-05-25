<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Services\KegiatanService;
use Illuminate\Http\Request;

class LpjController extends Controller
{
    public function index()
    {
        $lpjList = Lpj::with(['kegiatan.user'])
            ->latest('submitted_at')
            ->get();

        $list_lpj = $lpjList->map(function ($lpj) {
            return [
                'id' => $lpj->kegiatan_id,
                'nama' => 'LPJ - ' . ($lpj->kegiatan->nama_kegiatan ?? 'Kegiatan'),
                'pengusul' => $lpj->kegiatan->user->nama ?? $lpj->kegiatan->pemilik_kegiatan,
                'nim' => $lpj->kegiatan->nim_pelaksana,
                'prodi' => $lpj->kegiatan->prodi_penyelenggara,
                'jurusan' => $lpj->kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $lpj->submitted_at ? $lpj->submitted_at->format('Y-m-d') : null,
                'deadline' => $lpj->tenggat_lpj ? $lpj->tenggat_lpj->format('Y-m-d') : null,
                'status' => $this->mapLpjStatusLabel($lpj),
            ];
        })->toArray();
        return view('bendahara.lpj.index', compact('list_lpj'));
    }

    public function detail(Request $request, $id)
    {
        $id = (int) $id;
        $from = $request->query('from', 'index');
        $kegiatan = (new KegiatanService())->getDetailLengkap($id);
        $status = $kegiatan->lpj ? $this->mapLpjStatusLabel($kegiatan->lpj) : 'Menunggu Verifikasi';

        $kegiatan_data = [
            'id' => $id,
            'nama_pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
            'nim_pengusul' => $kegiatan->nim_pelaksana,
            'nama_pelaksana' => $kegiatan->pemilik_kegiatan ?? '-',
            'nama_penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_penanggung_jawab' => $kegiatan->nip ?? '-',
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'wadir_tujuan' => $kegiatan->wadir->nama_wadir ?? $kegiatan->wadir_tujuan,
            'penerima_manfaat' => $kegiatan->kak->penerima_manfaat ?? '-',
            'tanggal_mulai' => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : null,
            'tanggal_selesai' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : null,
        ];

        $kode_mak = $kegiatan->bukti_mak ?? '-';

        $rab_items = [];
        $anggaran_disetujui = 0;
        $anggaran_realisasi = 0;

        $lpjItemMap = collect();
        if ($kegiatan->lpj && $kegiatan->lpj->items) {
            $lpjItemMap = $kegiatan->lpj->items->keyBy(function ($item) {
                return $this->makeItemKey($item->uraian, $item->rincian, $item->vol1, $item->vol2, $item->harga);
            });
        }

        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->rabs as $rab) {
                $cat = $rab->kategori->nama_kategori ?? 'Lainnya';
                $key = $this->makeItemKey($rab->uraian, $rab->rincian, $rab->vol1, $rab->vol2, $rab->harga);
                $lpjItem = $lpjItemMap->get($key);
                $realisasi = $lpjItem ? (float) $lpjItem->realisasi : 0;

                $rab_items[$cat][] = [
                    'id' => $rab->rab_item_id,
                    'uraian' => $rab->uraian,
                    'rincian' => $rab->rincian,
                    'vol1' => $rab->vol1,
                    'sat1' => $rab->sat1,
                    'vol2' => $rab->vol2,
                    'sat2' => $rab->sat2,
                    'harga' => $rab->harga,
                    'realisasi' => $realisasi,
                    'keterangan' => $lpjItem->komentar ?? '',
                    'catatan_item' => $lpjItem->komentar ?? '',
                ];

                $anggaran_disetujui += $rab->vol1 * ($rab->vol2 ?? 1) * $rab->harga;
                $anggaran_realisasi += $realisasi;
            }
        }

        $iku_data = $kegiatan->kak ? array_filter(array_map('trim', explode(',', $kegiatan->kak->iku ?? ''))) : [];
        $catatan_revisi = $kegiatan->lpj ? $kegiatan->lpj->komentar_revisi : null;

        return view('bendahara.lpj.detail', compact(
            'id', 'status', 'rab_items', 'kegiatan_data', 'catatan_revisi', 
            'from', 'kode_mak', 'anggaran_disetujui', 'anggaran_realisasi', 'iku_data'
        ));
    }

    private function mapLpjStatusLabel(Lpj $lpj): string
    {
        return match ((int) $lpj->status_id) {
            2 => 'Revisi',
            3 => 'Disetujui',
            4 => 'Ditolak',
            default => $lpj->komentar_revisi ? 'Telah Direvisi' : 'Menunggu Verifikasi',
        };
    }

    private function makeItemKey($uraian, $rincian, $vol1, $vol2, $harga): string
    {
        return implode('|', [trim((string) $uraian), trim((string) $rincian), $vol1, $vol2, $harga]);
    }
}
