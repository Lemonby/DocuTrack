<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotifikasiResource;
use App\Models\LogStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifikasi = LogStatus::where('user_id', $request->user()->user_id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => NotifikasiResource::collection($notifikasi),
            'meta' => [
                'total' => $notifikasi->total(),
                'unread' => LogStatus::where('user_id', $request->user()->user_id)->unread()->count(),
            ],
        ]);
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notif = LogStatus::where('user_id', $request->user()->user_id)->findOrFail($id);
        $notif->update(['status' => 'DIBACA']);

        return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        LogStatus::where('user_id', $request->user()->user_id)
            ->unread()
            ->update(['status' => 'DIBACA']);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }
}
