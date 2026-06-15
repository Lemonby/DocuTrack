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
            $target = is_numeric($iku->target) ? $iku->target.'%' : ($iku->target ?? '0%');
            $capaian = is_numeric($iku->realisasi) ? $iku->realisasi.'%' : ($iku->realisasi ?? '0%');

            return [
                'id' => $iku->id,
                'type' => $type,
                'nama' => $iku->indikator_kinerja,
                'target' => $target,
                'capaian' => $capaian,
                'status' => $iku->status ?? 'Aktif',
                'kategori' => $kode,
                'tahun_periode' => (string) ($iku->tahun ?? '-'),
                'deskripsi' => $iku->deskripsi ?? '-',
            ];
        })->toArray();

        return view('superadmin.iku.index', compact('list_iku'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:IKU,RENSTRA',
            'nama' => 'required|string|max:255',
            'tahun_periode' => 'required|integer',
            'target' => 'required|string|max:100',
            'deskripsi' => 'required|string',
        ]);

        $type = $validated['type'];
        $count = Iku::where('kode_iku', 'like', $type . '%')->count();
        $kode_iku = $type . '_' . ($count + 1);

        $iku = Iku::create([
            'kode_iku' => $kode_iku,
            'indikator_kinerja' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'target' => $validated['target'],
            'realisasi' => '0',
            'tahun' => $validated['tahun_periode'],
            'status' => 'Aktif',
        ]);

        $targetFormatted = is_numeric($iku->target) ? $iku->target.'%' : ($iku->target ?? '0%');
        $capaianFormatted = is_numeric($iku->realisasi) ? $iku->realisasi.'%' : ($iku->realisasi ?? '0%');

        return response()->json([
            'success' => true,
            'iku' => [
                'id' => $iku->id,
                'type' => $type,
                'nama' => $iku->indikator_kinerja,
                'target' => $targetFormatted,
                'capaian' => $capaianFormatted,
                'status' => $iku->status,
                'kategori' => $iku->kode_iku,
                'tahun_periode' => (string) ($iku->tahun ?? '-'),
                'deskripsi' => $iku->deskripsi ?? '-',
            ],
            'message' => 'Indikator berhasil ditambahkan.',
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:ikus,id',
            'type' => 'required|in:IKU,RENSTRA',
            'nama' => 'required|string|max:255',
            'tahun_periode' => 'required|integer',
            'target' => 'required|string|max:100',
            'deskripsi' => 'required|string',
        ]);

        $iku = Iku::findOrFail($validated['id']);
        
        $kode_iku = $iku->kode_iku;
        $currentType = str_starts_with(strtoupper($kode_iku ?? ''), 'RENSTRA') ? 'RENSTRA' : 'IKU';
        if ($currentType !== $validated['type']) {
            $type = $validated['type'];
            $count = Iku::where('kode_iku', 'like', $type . '%')->count();
            $kode_iku = $type . '_' . ($count + 1);
        }

        $iku->update([
            'kode_iku' => $kode_iku,
            'indikator_kinerja' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'],
            'target' => $validated['target'],
            'tahun' => $validated['tahun_periode'],
        ]);

        $targetFormatted = is_numeric($iku->target) ? $iku->target.'%' : ($iku->target ?? '0%');
        $capaianFormatted = is_numeric($iku->realisasi) ? $iku->realisasi.'%' : ($iku->realisasi ?? '0%');

        return response()->json([
            'success' => true,
            'iku' => [
                'id' => $iku->id,
                'type' => $validated['type'],
                'nama' => $iku->indikator_kinerja,
                'target' => $targetFormatted,
                'capaian' => $capaianFormatted,
                'status' => $iku->status ?? 'Aktif',
                'kategori' => $iku->kode_iku,
                'tahun_periode' => (string) ($iku->tahun ?? '-'),
                'deskripsi' => $iku->deskripsi ?? '-',
            ],
            'message' => 'Indikator berhasil diperbarui.',
        ]);
    }

    public function destroy($id)
    {
        $iku = Iku::findOrFail($id);
        $iku->delete();

        return response()->json([
            'success' => true,
            'message' => 'Indikator berhasil dihapus.',
        ]);
    }

    public function toggleStatus($id)
    {
        $iku = Iku::findOrFail($id);
        $iku->status = $iku->status === 'Aktif' ? 'Non-Aktif' : 'Aktif';
        $iku->save();

        return response()->json([
            'success' => true,
            'status' => $iku->status,
            'message' => 'Status indikator berhasil diubah menjadi ' . $iku->status,
        ]);
    }
}
