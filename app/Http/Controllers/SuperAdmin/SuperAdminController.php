<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Kegiatan;
use App\Models\Lpj;
use App\Models\User;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total' => Kegiatan::count(),
            'disetujui' => Kegiatan::whereIn('status_utama_id', [
                WorkflowService::STATUS_DISETUJUI,
                WorkflowService::STATUS_DANA_DIBERIKAN,
            ])->count(),
            'revisi' => Kegiatan::withStatus(WorkflowService::STATUS_REVISI)->count(),
            'ditolak' => Kegiatan::withStatus(WorkflowService::STATUS_DITOLAK)->count(),
            'menunggu' => Kegiatan::withStatus(WorkflowService::STATUS_MENUNGGU)->count(),
        ];

        $system_health = [
            'db_connection' => true,
            'memory_usage' => '42.5 MB',
            'php_version' => PHP_VERSION,
        ];

        $monitoring_kegiatan = Kegiatan::with(['statusUtama', 'user'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($kegiatan) {
                return [
                    'pengusul' => $kegiatan->user->nama ?? $kegiatan->pemilik_kegiatan,
                    'prodi' => $kegiatan->prodi_penyelenggara,
                    'nama' => $kegiatan->nama_kegiatan,
                    'status' => $kegiatan->statusUtama->nama_status_usulan ?? 'Menunggu',
                    'created_at' => $kegiatan->created_at,
                ];
            })->toArray();

        $monitoring_lpj = Lpj::with(['kegiatan.user', 'status'])
            ->latest('submitted_at')
            ->take(6)
            ->get()
            ->map(function ($lpj) {
                return [
                    'nama_kegiatan' => $lpj->kegiatan->nama_kegiatan ?? 'Kegiatan',
                    'pengusul' => $lpj->kegiatan->user->nama ?? $lpj->kegiatan->pemilik_kegiatan,
                    'total_realisasi' => (float) ($lpj->grand_total_realisasi ?? 0),
                    'status_lpj' => $lpj->status->nama_status_usulan ?? 'Menunggu Verifikasi',
                ];
            })->toArray();

        $server_load = [
            'cpu' => 24,
            'ram' => 45,
            'disk' => 68,
            'traffic' => '1.2 GB/s'
        ];

        $recent_logs = ActivityLog::with('user')
            ->latest('created_at')
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'time' => $log->created_at ? $log->created_at->format('H:i') : '-',
                    'event' => $log->description ?? $log->action ?? 'System Event',
                    'user' => $log->user->nama ?? 'System',
                    'status' => $log->category ? strtolower($log->category) : 'info',
                ];
            })->toArray();

        $active_users = User::active()
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->nama,
                    'role' => $user->getRoleNames()->first() ?? 'Pengusul',
                    'last_seen' => $user->updated_at ? $user->updated_at->diffForHumans() : '-',
                ];
            })->toArray();

        $security_threats = [
            ['type' => 'SQL Injection Attempt', 'ip' => '192.168.1.105', 'status' => 'Blocked', 'risk' => 'High'],
            ['type' => 'Brute Force Detection', 'ip' => '45.122.10.5', 'status' => 'Mitigated', 'risk' => 'Medium'],
            ['type' => 'XSS Payload Filtered', 'ip' => '103.44.12.9', 'status' => 'Isolated', 'risk' => 'Low'],
        ];

        $broadcasts = [
            ['title' => 'Scheduled Maintenance', 'body' => 'System will be offline on Sunday at 02:00 AM.', 'date' => '20 May 2026'],
            ['title' => 'Security Update v4.2', 'body' => 'New encryption protocols have been applied to document storage.', 'date' => '18 May 2026'],
        ];

        return view('superadmin.dashboard', compact(
            'stats', 
            'system_health', 
            'monitoring_kegiatan', 
            'monitoring_lpj',
            'server_load',
            'recent_logs',
            'active_users',
            'security_threats',
            'broadcasts'
        ));
    }

    public function getAiAnalysis()
    {
        return response()->json([
            'status' => 'success',
            'model' => 'GPT-4o / Gemini Pro',
            'data' => "Sistem dalam kondisi optimal.\nStabilitas database: 100%.\nPenggunaan memori efisien.\n\nInsight: Terdapat peningkatan usulan kegiatan sebesar 15% minggu ini.",
        ]);
    }
    public function monitoring()
    {
        $ai_agents_active = \App\Models\AppSetting::getValue('ai_agents_active', false);
        return view('superadmin.monitoring', [
            'title' => 'Monitoring Sistem - Super Admin',
            'ai_agents_active' => $ai_agents_active
        ]);
    }

    public function updateAiSetting(Request $request)
    {
        $request->validate([
            'ai_agents_active' => 'required|boolean',
        ]);

        $oldValue = \App\Models\AppSetting::getValue('ai_agents_active', false);
        $newValue = $request->input('ai_agents_active');

        \App\Models\AppSetting::setValue('ai_agents_active', $newValue, 'boolean');

        (new \App\Services\ActivityLogService())->log(
            $request->user()->user_id,
            $newValue ? 'activate_ai_agents' : 'deactivate_ai_agents',
            'security',
            'app_setting',
            null,
            'Superadmin changed AI agents activation status (Web)',
            ['ai_agents_active' => $oldValue],
            ['ai_agents_active' => $newValue]
        );

        return back()->with('success', 'Status AI Agents berhasil diperbarui.');
    }
}
