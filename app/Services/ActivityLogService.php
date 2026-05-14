<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\LogStatus;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * Record an activity in the audit trail.
     */
    public function log(
        int $userId,
        string $action,
        string $category = 'workflow',
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        ?array $oldValue = null,
        ?array $newValue = null,
        ?Request $request = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'category' => $category,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }

    /**
     * Create a notification for a user.
     */
    public function createNotification(int $userId, string $type, string $message, ?int $referenceId = null): LogStatus
    {
        return LogStatus::create([
            'user_id' => $userId,
            'tipe_log' => $type,
            'id_referensi' => $referenceId,
            'status' => 'BELUM_DIBACA',
            'konten_json' => ['message' => $message, 'created_at' => now()->toISOString()],
        ]);
    }
}
