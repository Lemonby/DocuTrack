<?php

namespace App\Http\Controllers\API\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AppNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendNotificationController extends Controller
{
    /**
     * Send a notification and email to a specific user or all users.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,user_id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type_log' => 'nullable|string|max:50',
            'link' => 'nullable|url',
            'send_email' => 'boolean',
        ]);

        $title = $request->input('title');
        $message = $request->input('message');
        $typeLog = $request->input('type_log', 'INFO');
        $link = $request->input('link');
        $sendEmail = $request->input('send_email', true);

        if ($request->filled('user_id')) {
            $user = User::find($request->input('user_id'));
            if (! $user) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }
            AppNotificationService::send($user->user_id, $title, $message, $typeLog, null, $link, $sendEmail);
            $messageCount = 1;

            (new ActivityLogService)->log(
                $request->user()->user_id,
                'send_notification',
                'workflow', // Or another appropriate category
                'user',
                $user->user_id,
                "Superadmin sent a direct notification to {$user->nama}",
                null,
                ['title' => $title, 'message' => $message]
            );
        } else {
            // Broadcast to all active users
            $users = User::where('status', 'Aktif')->get();
            foreach ($users as $user) {
                AppNotificationService::send($user->user_id, $title, $message, $typeLog, null, $link, $sendEmail);
            }
            $messageCount = $users->count();

            (new ActivityLogService)->log(
                $request->user()->user_id,
                'broadcast_notification',
                'workflow',
                null,
                null,
                "Superadmin broadcasted a notification to {$messageCount} users",
                null,
                ['title' => $title, 'message' => $message]
            );
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully sent {$messageCount} notifications.",
        ]);
    }
}
