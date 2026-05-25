<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Services\KegiatanService;
use App\Services\WorkflowService;

class PencairanDanaController extends Controller
{
    public function index()
    {
        $kegiatanList = Kegiatan::with(['statusUtama', 'user'])
            ->whereIn('status_utama_id', [
                WorkflowService::STATUS_DISETUJUI,
                WorkflowService::STATUS_DANA_DIBERIKAN,
            ])
            ->latest()
            ->get();

        $list_kak = $kegiatanList->map(function ($kegiatan) {
            $statusLabel = $kegiatan->status_utama_id === WorkflowService::STATUS_DANA_DIBERIKAN
                ? 'Sudah Dicairkan'
                : 'Belum Dicairkan';

            return [
                'id' => $kegiatan->kegiatan_id,
                'nama' => $kegiatan->nama_kegiatan,
                'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                'nim' => $kegiatan->nim_pelaksana,
                'prodi' => $kegiatan->prodi_penyelenggara,
                'jurusan' => $kegiatan->jurusan_penyelenggara,
                'tanggal_pengajuan' => $kegiatan->created_at ? $kegiatan->created_at->format('Y-m-d') : null,
                'status' => $statusLabel,
            ];
        })->toArray();
        return view('bendahara.pencairan-dana.index', compact('list_kak'));
    }

    public function detail($id)
    {
        $kegiatan = (new KegiatanService())->getDetailLengkap((int) $id);

        $rab_data = [];
        $anggaran_disetujui = 0;
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
                $anggaran_disetujui += $rab->vol1 * ($rab->vol2 ?? 1) * $rab->harga;
            }
        }

        $jumlah_dicairkan = (float) ($kegiatan->jumlah_dicairkan ?? 0);
        $sisa_dana = $anggaran_disetujui - $jumlah_dicairkan;

        $status = $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu';
        if ($jumlah_dicairkan > 0 && $jumlah_dicairkan < $anggaran_disetujui) {
            $status = 'Dana Belum Diberikan Semua';
        }

        $lpj_status = $kegiatan->lpj ? $this->mapLpjStatusLabel($kegiatan->lpj) : 'Belum Ada';
        $riwayat_pencairan = $kegiatan->tahapanPencairans->map(function ($tahap) {
            return [
                'tanggal_pencairan' => $tahap->tgl_pencairan ? $tahap->tgl_pencairan->format('Y-m-d') : null,
                'termin' => $tahap->termin,
                'nominal' => $tahap->nominal,
                'catatan' => $tahap->catatan,
            ];
        })->toArray();

        $boleh_cairkan = $jumlah_dicairkan < $anggaran_disetujui && $kegiatan->status_utama_id !== WorkflowService::STATUS_DITOLAK;

        $iku_data = $kegiatan->kak ? array_filter(array_map('trim', explode(',', $kegiatan->kak->iku ?? ''))) : [];

        $tahapan_pelaksanaan = [];
        $indikator_keberhasilan = [];
        if ($kegiatan->kak) {
            foreach ($kegiatan->kak->tahapans as $index => $tahap) {
                $tahapan_pelaksanaan[$index + 1] = $tahap->nama_tahapan;
            }
            foreach ($kegiatan->kak->indikators as $ind) {
                $key = $ind->bulan ?: (count($indikator_keberhasilan) + 1);
                $indikator_keberhasilan[$key] = [
                    'target_persen' => $ind->target_persen,
                    'deskripsi' => $ind->indikator_keberhasilan,
                ];
            }
        }

        $kegiatan_data = [
            'id' => $kegiatan->kegiatan_id,
            'nama_pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
            'nim_nip' => $kegiatan->nim_pelaksana,
            'nim_pengusul' => $kegiatan->nim_pelaksana,
            'nama_pelaksana' => $kegiatan->pemilik_kegiatan ?? '-',
            'nama_penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_penanggung_jawab' => $kegiatan->nip ?? '-',
            'penanggung_jawab' => $kegiatan->nama_pj ?? '-',
            'nip_pj' => $kegiatan->nip ?? '-',
            'jurusan' => $kegiatan->jurusan_penyelenggara,
            'prodi' => $kegiatan->prodi_penyelenggara,
            'nama_kegiatan' => $kegiatan->nama_kegiatan,
            'wadir_tujuan' => $kegiatan->wadir->nama_wadir ?? $kegiatan->wadir_tujuan,
            'penerima_manfaat' => $kegiatan->kak->penerima_manfaat ?? '-',
            'gambaran_umum' => $kegiatan->kak->gambaran_umum ?? '-',
            'metode_pelaksanaan' => $kegiatan->kak->metode_pelaksanaan ?? '-',
            'tahapan_kegiatan' => $kegiatan->kak ? $kegiatan->kak->tahapans->pluck('nama_tahapan')->implode("\n") : '',
            'tanggal_mulai' => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : null,
            'tanggal_selesai' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : null,
        ];

        $kode_mak = $kegiatan->bukti_mak ?? '-';
        $catatan_bendahara = $kegiatan->catatan_bendahara ?? '';

        return view('bendahara.pencairan-dana.detail', compact(
            'id', 'status', 'lpj_status', 'iku_data', 'rab_data', 'kegiatan_data',
            'tahapan_pelaksanaan', 'indikator_keberhasilan', 'anggaran_disetujui', 'jumlah_dicairkan',
            'sisa_dana', 'boleh_cairkan', 'riwayat_pencairan',
            'kode_mak', 'catatan_bendahara'
        ));
    }

    private function mapLpjStatusLabel($lpj): string
    {
        return match ((int) $lpj->status_id) {
            2 => 'Revisi',
            3 => 'Disetujui',
            4 => 'Ditolak',
            default => $lpj->komentar_revisi ? 'Telah Direvisi' : 'Menunggu Verifikasi',
        };
    }
}
