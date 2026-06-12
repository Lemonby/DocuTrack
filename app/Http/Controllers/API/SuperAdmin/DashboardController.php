<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\KegiatanResource;
use App\Models\ActivityLog;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $recentActivity = ActivityLog::with('user')->latest('created_at')->take(5)->get()->map(function ($log) {
            return [
                'time' => $log->created_at->format('H:i:s'),
                'event' => $log->keterangan ?? $log->aksi,
                'user' => $log->user->nama ?? 'System',
                'status' => 'success',
            ];
        });

        $activeUsers = User::active()->latest()->take(5)->get()->map(function ($u) {
            return [
                'name' => $u->nama,
                'role' => $u->getRoleNames()->first() ?? 'User',
                'last_seen' => $u->updated_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total' => Kegiatan::count(),
                    'disetujui' => Kegiatan::where('status_utama_id', 8)->count(),
                    'menunggu' => Kegiatan::whereNotIn('status_utama_id', [4, 8])->count(),
                    'ditolak' => Kegiatan::where('status_utama_id', 4)->count(),
                ],
                'server_load' => [
                    'cpu' => rand(15, 45),
                    'ram' => rand(30, 60),
                    'disk' => rand(50, 75),
                    'traffic' => rand(1, 5).'.'.rand(0, 9).'k',
                ],
                'recent_logs' => $recentActivity,
                'active_users' => $activeUsers,
                'recent_kegiatan' => KegiatanResource::collection(
                    Kegiatan::with(['statusUtama', 'user'])->latest()->take(5)->get()
                ),
            ],
        ]);
    }
}
