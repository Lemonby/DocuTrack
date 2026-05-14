<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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
}
