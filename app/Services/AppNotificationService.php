<?php

namespace App\Services;

use App\Mail\GenericNotificationMail;
use App\Models\LogStatus;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AppNotificationService
{
    /**
     * Send a notification and an optional email to a user.
     *
     * @param  string|null  $typeLog  e.g., 'INFO', 'WARNING', 'APPROVAL'
     */
    public static function send(
        int $userId,
        string $title,
        string $message,
        ?string $typeLog = 'INFO',
        ?int $referenceId = null,
        ?string $link = null,
        bool $sendEmail = true
    ): LogStatus {
        $kontenJson = [
            'judul' => $title,
            'pesan' => $message,
        ];

        if ($link) {
            $kontenJson['link'] = $link;
        }

        // Save to Database
        $notification = LogStatus::create([
            'user_id' => $userId,
            'tipe_log' => 'NOTIFIKASI_'.strtoupper($typeLog),
            'id_referensi' => $referenceId,
            'status' => 'BELUM_DIBACA',
            'konten_json' => $kontenJson,
        ]);

        // Send Email
        if ($sendEmail) {
            try {
                $user = User::find($userId);
                if ($user && $user->email) {
                    Mail::to($user->email)->queue(
                        new GenericNotificationMail(
                            $title,
                            $message,
                            $link,
                            $link ? 'Lihat Detail' : null
                        )
                    );
                }
            } catch (Exception $e) {
                Log::error("Failed to send notification email to User ID {$userId}: ".$e->getMessage());
            }
        }

        return $notification;
    }
}
