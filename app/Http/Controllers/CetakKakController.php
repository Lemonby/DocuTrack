<?php

namespace App\Http\Controllers;

use App\Services\KegiatanService;
use App\Services\PdfService;
use Illuminate\Http\Response;

class CetakKakController extends Controller
{
    /**
     * Generate KAK PDF for a specific kegiatan.
     *
     * @param  int  $id
     * @return Response
     */
    public function cetak($id)
    {
        $kegiatan = (new KegiatanService)->getDetailLengkap($id);

        $kegiatan_data = [
            'nama_pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
            'nim_pengusul' => $kegiatan->nim_pelaksana,
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_pj' => $kegiatan->nip ?? '-',
            'wadir_tujuan' => $kegiatan->wadir->nama_wadir ?? 'Wadir',
            'penerima_manfaat' => $kegiatan->kak->penerima_manfaat ?? '-',
            'gambaran_umum' => $kegiatan->kak->gambaran_umum ?? '-',
            'metode_pelaksanaan' => $kegiatan->kak->metode_pelaksanaan ?? '-',
            'tahapan_kegiatan' => $kegiatan->kak ? $kegiatan->kak->tahapans->pluck('nama_tahapan')->implode("\n") : '-',
            'tanggal_mulai' => $kegiatan->tanggal_mulai ?? null,
            'tanggal_selesai' => $kegiatan->tanggal_selesai ?? null,
        ];

        $iku_data = $kegiatan->kak ? array_filter(array_map('trim', explode(',', $kegiatan->kak->iku ?? ''))) : [];

        $indikator_data = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->indikators as $ind) {
                $indikator_data[] = [
                    'bulan' => $ind->bulan,
                    'nama' => $ind->indikator_keberhasilan,
                    'target' => $ind->target_persen,
                ];
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
                    'harga' => $rab->harga,
                ];
            }
        }

        $kode_mak = $kegiatan->bukti_mak ?? '';

        $pdfService = new PdfService;
        $filename = 'KAK_'.str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $kegiatan->nama_kegiatan).'.pdf';

        return $pdfService->generate(
            resource_path('views/pdf/kak_template.php'),
            compact('kegiatan_data', 'iku_data', 'indikator_data', 'rab_data', 'kode_mak'),
            $filename
        );
    }
}
