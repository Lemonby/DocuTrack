<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Iku;
use Illuminate\Http\Request;

class IkuController extends Controller
{
    public function index()
    {
        $list_iku = Iku::orderBy('tahun', 'desc')->get()->map(function ($iku) {
            $kode = $iku->kode_iku ?? 'IKU';
            $type = str_starts_with(strtoupper($kode), 'RENSTRA') ? 'RENSTRA' : 'IKU';
            $target = is_numeric($iku->target) ? $iku->target . '%' : ($iku->target ?? '0%');
            $capaian = is_numeric($iku->realisasi) ? $iku->realisasi . '%' : ($iku->realisasi ?? '0%');

            return [
                'id' => $iku->id,
                'type' => $type,
                'nama' => $iku->indikator_kinerja,
                'target' => $target,
                'capaian' => $capaian,
                'status' => 'Aktif',
                'kategori' => $kode,
                'tahun_periode' => (string) ($iku->tahun ?? '-'),
                'deskripsi' => $iku->deskripsi ?? '-',
            ];
        })->toArray();

        return view('superadmin.iku.index', compact('list_iku'));
    }
}
