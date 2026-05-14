<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => User::count(),
                'active_users' => User::active()->count(),
                'total_kegiatan' => Kegiatan::count(),
                'kegiatan_per_status' => Kegiatan::selectRaw('status_utama_id, COUNT(*) as total')
                    ->groupBy('status_utama_id')->get(),
                'recent_activity' => ActivityLog::with('user')
                    ->latest('created_at')->take(10)->get(),
            ],
        ]);
    }
}
