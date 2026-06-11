<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiMonitoringController extends Controller
{
    public function logSummaries(): JsonResponse
    {
        $summaries = DB::table('ai_log_summaries')->latest('created_at')->paginate(20);

        return response()->json(['success' => true, 'data' => $summaries]);
    }

    public function securityAlerts(): JsonResponse
    {
        $alerts = DB::table('ai_security_alerts')->latest('created_at')->paginate(20);

        return response()->json(['success' => true, 'data' => $alerts]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_summaries' => DB::table('ai_log_summaries')->count(),
                'total_alerts' => DB::table('ai_security_alerts')->count(),
                'high_severity_alerts' => DB::table('ai_security_alerts')->where('severity', 'high')->count(),
            ],
        ]);
    }

    public function getAiSettings(): JsonResponse
    {
        $isActive = AppSetting::getValue('ai_agents_active', false);

        return response()->json([
            'success' => true,
            'data' => [
                'ai_agents_active' => $isActive,
            ],
        ]);
    }

    public function updateAiSettings(Request $request): JsonResponse
    {
        $request->validate([
            'ai_agents_active' => 'required|boolean',
        ]);

        $oldValue = AppSetting::getValue('ai_agents_active', false);
        $newValue = $request->input('ai_agents_active');

        AppSetting::setValue('ai_agents_active', $newValue, 'boolean');

        (new ActivityLogService)->log(
            $request->user()->user_id,
            $newValue ? 'activate_ai_agents' : 'deactivate_ai_agents',
            'security',
            'app_setting',
            null,
            'Superadmin changed AI agents activation status',
            ['ai_agents_active' => $oldValue],
            ['ai_agents_active' => $newValue]
        );

        return response()->json([
            'success' => true,
            'message' => 'Status AI Agents berhasil diperbarui.',
            'data' => [
                'ai_agents_active' => $newValue,
            ],
        ]);
    }
}
