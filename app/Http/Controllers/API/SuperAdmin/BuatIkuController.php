<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Iku;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuatIkuController extends Controller
{
    public function index(): JsonResponse
    {
        $ikus = Iku::latest()->paginate(15);
        return response()->json(['success' => true, 'data' => $ikus]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_iku' => 'nullable|string|max:50',
            'indikator_kinerja' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'target' => 'nullable|string|max:100',
            'tahun' => 'nullable|integer|digits:4',
        ]);

        $iku = Iku::create($validated);

        return response()->json(['success' => true, 'message' => 'IKU berhasil dibuat.', 'data' => $iku], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Iku::findOrFail($id)]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $iku = Iku::findOrFail($id);
        $iku->update($request->validate([
            'kode_iku' => 'sometimes|string|max:50',
            'indikator_kinerja' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|nullable|string',
            'target' => 'sometimes|nullable|string|max:100',
            'tahun' => 'sometimes|nullable|integer|digits:4',
        ]));

        return response()->json(['success' => true, 'message' => 'IKU berhasil diperbarui.', 'data' => $iku]);
    }

    public function destroy(int $id): JsonResponse
    {
        Iku::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'IKU berhasil dihapus.']);
    }
}
