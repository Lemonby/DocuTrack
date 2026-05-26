<?php

namespace App\Http\Controllers;

use App\Models\LogStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class WebNotifikasiController extends Controller
{
    /**
     * Get list of notifications for the logged in web user.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Fetch top 15 latest notifications
        $notifikasi = LogStatus::where('user_id', $userId)
            ->orderByDesc('id')
            ->take(15)
            ->get();

        $unreadCount = LogStatus::where('user_id', $userId)
            ->unread()
            ->count();

        $formatted = $notifikasi->map(function ($notif) {
            $konten = $notif->konten_json;
            return [
                'id' => $notif->id,
                'judul' => $konten['judul'] ?? 'Notifikasi',
                'pesan' => $konten['pesan'] ?? '',
                'link' => $konten['link'] ?? '#',
                'tipe_log' => str_replace('NOTIFIKASI_', '', $notif->tipe_log),
                'created_at' => $notif->created_at ? $notif->created_at->toISOString() : now()->toISOString(),
                'status' => $notif->status
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $formatted,
                'unread_count' => $unreadCount
            ]
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $notif = LogStatus::where('user_id', $userId)->findOrFail($id);
        $notif->update(['status' => 'DIBACA']);

        return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sudah dibaca.']);
    }

    /**
     * Mark all notifications for the user as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        LogStatus::where('user_id', $userId)
            ->unread()
            ->update(['status' => 'DIBACA']);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sudah dibaca.']);
    }
}
