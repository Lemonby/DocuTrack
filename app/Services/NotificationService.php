<?php

namespace App\Services;

use App\Core\Mailer;
use App\Models\Kegiatan\KegiatanModel;
use App\Models\UserModel; // Assuming a UserModel exists to get user emails
use Exception;

class NotificationService
{
    private $db;
    private $mailer;
    private $kegiatanModel;
    private $userModel;
    private LogStatusService $logStatusService;

    public function __construct($db)
    {
        $this->db = $db;
        $this->mailer = new Mailer();
        $this->kegiatanModel = new KegiatanModel($db);
        $this->userModel = new UserModel($db);
        $this->logStatusService = new LogStatusService($db);
    }

    /**
     * Sends a notification for a Kegiatan-related event.
     *
     * @param int $kegiatanId The ID of the kegiatan.
     * @param string $notificationType Type of notification (e.g., 'new_submission', 'rincian_updated', 'approved', 'rejected').
     * @param int|null $recipientUserId Optional: Specific user ID to send the notification to. If null, it will determine based on type.
     * @param string|null $additionalMessage Optional: An additional message for the email body.
     * @throws Exception
     */
    public function sendKegiatanNotification(int $kegiatanId, string $notificationType, ?int $recipientUserId = null, ?string $additionalMessage = null): void
    {
        $kegiatan = $this->kegiatanModel->getById($kegiatanId);

        if (!$kegiatan) {
            error_log("NotificationService: Kegiatan ID {$kegiatanId} not found. Cannot send notification.");
            return;
        }

        // Determine recipient(s) and email content based on notification type
        $to = [];
        $subject = "[DocuTrack] Notifikasi Kegiatan";
        $pesan_pembuka = "";
        $status_label = "";
        $status_color_class = "bg-blue"; // Default color
        $link_action = BASE_URL . '/admin/pengajuan-kegiatan/show/' . $kegiatanId; // Default link

        // Determine current user for the default sender (if applicable)
        $currentUserId = $_SESSION['user_id'] ?? null;
        $currentUserEmail = null;
        if ($currentUserId) {
            $currentUser = $this->userModel->getById($currentUserId);
            $currentUserEmail = $currentUser['email'] ?? null;
        }

        switch ($notificationType) {
            case 'new_submission':
                // Notify the next person in the workflow (e.g., Verifikator)
                // And the submitter (user who created the kegiatan)
                $submitter = $this->userModel->getById($kegiatan['userId']);
                if ($submitter && $submitter['email']) {
                    $to[] = ['email' => $submitter['email'], 'name' => $submitter['nama']];
                }

                // Determine the next role to be notified. For 'new_submission', it's usually the Verifikator or based on 'posisiId'
                // Assuming 'posisiId' 2 is Verifikator for initial submission
                // You might need a more sophisticated way to find the actual Verifikator's email, e.g., from a role mapping or specific user in that role.
                $verifikatorUsers = $this->userModel->getUsersByRole('Verifikator'); // Custom method needed in UserModel
                foreach($verifikatorUsers as $verifikator) {
                    if ($verifikator['email']) {
                        $to[] = ['email' => $verifikator['email'], 'name' => $verifikator['nama']];
                    }
                }
                
                $subject = "[DocuTrack] Pengajuan Kegiatan Baru: " . $kegiatan['namaKegiatan'];
                $pesan_pembuka = "Pengajuan kegiatan baru telah dibuat oleh {$kegiatan['pemilikKegiatan']}. Mohon segera ditinjau.";
                $status_label = "Menunggu Verifikasi";
                $status_color_class = "bg-yellow"; // Status menunggu
                $link_action = BASE_URL . '/verifikator/telaah/show/' . $kegiatanId; // Link for verifikator
                break;

            case 'rincian_updated':
                // Notify PPK and original submitter
                $ppkUsers = $this->userModel->getUsersByRole('PPK'); // Custom method needed in UserModel
                foreach($ppkUsers as $ppk) {
                    if ($ppk['email']) {
                        $to[] = ['email' => $ppk['email'], 'name' => $ppk['nama']];
                    }
                }
                $submitter = $this->userModel->getById($kegiatan['userId']);
                if ($submitter && $submitter['email']) {
                    $to[] = ['email' => $submitter['email'], 'name' => $submitter['nama']];
                }

                $subject = "[DocuTrack] Rincian Kegiatan Diperbarui: " . $kegiatan['namaKegiatan'];
                $pesan_pembuka = "Rincian untuk kegiatan \"{$kegiatan['namaKegiatan']}\" telah diperbarui dan menunggu persetujuan dari PPK.";
                $status_label = "Menunggu Persetujuan PPK";
                $status_color_class = "bg-yellow"; // Status menunggu
                $link_action = BASE_URL . '/ppk/telaah/show/' . $kegiatanId; // Link for PPK
                break;

            case 'approved':
                // Notify the submitter
                $submitter = $this->userModel->getById($kegiatan['userId']);
                if ($submitter && $submitter['email']) {
                    $to[] = ['email' => $submitter['email'], 'name' => $submitter['nama']];
                }
                $subject = "[DocuTrack] Kegiatan Disetujui: " . $kegiatan['namaKegiatan'];
                $pesan_pembuka = "Kegiatan \"{$kegiatan['namaKegiatan']}\" telah disetujui.";
                $status_label = "Disetujui";
                $status_color_class = "bg-green"; // Status disetujui
                break;

            case 'rejected':
                // Notify the submitter
                $submitter = $this->userModel->getById($kegiatan['userId']);
                if ($submitter && $submitter['email']) {
                    $to[] = ['email' => $submitter['email'], 'name' => $submitter['nama']];
                }
                $subject = "[DocuTrack] Kegiatan Ditolak: " . $kegiatan['namaKegiatan'];
                $pesan_pembuka = "Kegiatan \"{$kegiatan['namaKegiatan']}\" telah ditolak.";
                $status_label = "Ditolak";
                $status_color_class = "bg-red"; // Status ditolak
                break;
            case 'funds_disbursed':
                // Notify the submitter
                $submitter = $this->userModel->getById($kegiatan['userId']);
                if ($submitter && $submitter['email']) {
                    $to[] = ['email' => $submitter['email'], 'name' => $submitter['nama']];
                }
                $subject = "[DocuTrack] Dana Kegiatan Dicairkan: " . $kegiatan['namaKegiatan'];
                $pesan_pembuka = "Dana untuk kegiatan \"{$kegiatan['namaKegiatan']}\" telah dicairkan." . ($additionalMessage ? "<br>" . $additionalMessage : "");
                $status_label = "Dana Dicairkan";
                $status_color_class = "bg-green"; // Status Dana Dicairkan
                $link_action = BASE_URL . '/admin/pengajuan-kegiatan/show/' . $kegiatanId; // Link for admin to view
                break;

            case 'lpj_submitted':
                // Notify the submitter (Admin) and the Bendahara who verifies LPJ
                $submitter = $this->userModel->getById($kegiatan['userId']);
                if ($submitter && $submitter['email']) {
                    $to[] = ['email' => $submitter['email'], 'name' => $submitter['nama']];
                }
                $bendaharaUsers = $this->userModel->getUsersByRole('Bendahara'); // Assuming 'Bendahara' role
                foreach($bendaharaUsers as $bendahara) {
                    if ($bendahara['email']) {
                        $to[] = ['email' => $bendahara['email'], 'name' => $bendahara['nama']];
                    }
                }
                $subject = "[DocuTrack] LPJ Baru Diajukan: " . $kegiatan['namaKegiatan'];
                $pesan_pembuka = "LPJ untuk kegiatan \"{$kegiatan['namaKegiatan']}\" telah diajukan. Mohon segera ditinjau.";
                $status_label = "LPJ Diajukan";
                $status_color_class = "bg-blue"; // Status diajukan
                $link_action = BASE_URL . '/bendahara/pengajuan-lpj/show/' . $kegiatanId; // Link for Bendahara to view LPJ
                break;

            case 'lpj_verified':
                // Notify the submitter (Admin)
                $submitter = $this->userModel->getById($kegiatan['userId']);
                if ($submitter && $submitter['email']) {
                    $to[] = ['email' => $submitter['email'], 'name' => $submitter['nama']];
                }
                $subject = "[DocuTrack] LPJ Diverifikasi: " . $kegiatan['namaKegiatan'];
                $pesan_pembuka = "LPJ untuk kegiatan \"{$kegiatan['namaKegiatan']}\" telah diverifikasi.";
                $status_label = "LPJ Diverifikasi";
                $status_color_class = "bg-green"; // Status diverifikasi
                $link_action = BASE_URL . '/admin/pengajuan-lpj/show/' . $kegiatanId; // Link for Admin to view LPJ
                break;

            case 'lpj_rejected':
                // Notify the submitter (Admin) with rejection comments
                $submitter = $this->userModel->getById($kegiatan['userId']);
                if ($submitter && $submitter['email']) {
                    $to[] = ['email' => $submitter['email'], 'name' => $submitter['nama']];
                }
                $subject = "[DocuTrack] LPJ Ditolak: " . $kegiatan['namaKegiatan'];
                $pesan_pembuka = "LPJ untuk kegiatan \"{$kegiatan['namaKegiatan']}\" telah ditolak." . ($additionalMessage ? "<br><br>Alasan Penolakan: " . $additionalMessage : "");
                $status_label = "LPJ Ditolak";
                $status_color_class = "bg-red"; // Status ditolak
                $link_action = BASE_URL . '/admin/pengajuan-lpj/show/' . $kegiatanId; // Link for Admin to view LPJ
                break;

            case 'lpj_revised':
                // Notify the Bendahara about LPJ revision
                $bendaharaUsers = $this->userModel->getUsersByRole('Bendahara'); // Assuming 'Bendahara' role
                foreach($bendaharaUsers as $bendahara) {
                    if ($bendahara['email']) {
                        $to[] = ['email' => $bendahara['email'], 'name' => $bendahara['nama']];
                    }
                }
                $subject = "[DocuTrack] LPJ Revisi Dikirim: " . $kegiatan['namaKegiatan'];
                $pesan_pembuka = "Revisi LPJ untuk kegiatan \"{$kegiatan['namaKegiatan']}\" telah dikirim." . ($additionalMessage ? "<br><br>Catatan Revisi: " . $additionalMessage : "");
                $status_label = "LPJ Revisi Dikirim";
                $status_color_class = "bg-yellow"; // Status revisi
                $link_action = BASE_URL . '/bendahara/pengajuan-lpj/show/' . $kegiatanId; // Link for Bendahara to view LPJ
                break;

            // Add other notification types as needed (e.g., 'revision_requested', 'dana_cair')
            default:
                error_log("NotificationService: Unknown notification type '{$notificationType}'. No email sent.");
                return;
        }

        // Override recipient if specific user ID is provided
        if ($recipientUserId !== null) {
            $user = $this->userModel->getById($recipientUserId);
            if ($user && $user['email']) {
                $to = [['email' => $user['email'], 'name' => $user['nama']]]; // Clear previous recipients
                // Adjust link_action based on recipient's role if needed, e.g., admin, verifikator, etc.
                // For simplicity, we keep the default or type-specific link here.
            } else {
                error_log("NotificationService: Recipient user ID {$recipientUserId} not found or has no email.");
                return;
            }
        }
        
        // Ensure unique recipients
        $uniqueTo = [];
        foreach ($to as $rec) {
            $uniqueTo[$rec['email']] = $rec;
        }
        $to = array_values($uniqueTo);


        if (empty($to)) {
            error_log("NotificationService: No valid recipients found for notification type '{$notificationType}' for Kegiatan ID {$kegiatanId}.");
            return;
        }

        $detail_kegiatan = [
            'namaKegiatan' => $kegiatan['namaKegiatan'],
            'pemilikKegiatan' => $kegiatan['pemilikKegiatan'],
            'createdAt' => $kegiatan['createdAt'] // Use createdAt from kegiatan data
        ];

        // Combine additional message if provided
        if ($additionalMessage) {
            $pesan_pembuka .= "<br><br>Catatan: " . $additionalMessage;
        }


        $emailData = [
            'nama_penerima' => 'Penerima', // Will be replaced for each recipient
            'pesan_pembuka' => $pesan_pembuka,
            'status_color_class' => $status_color_class,
            'status_label' => $status_label,
            'detail_kegiatan' => $detail_kegiatan,
            'catatan_tambahan' => $additionalMessage, // Pass additional message as catatan_tambahan
            'link_action' => $link_action
        ];

        // Create in-app notifications for all recipients
        foreach ($to as $recipient) {
            try {
                $recipientUserId = $this->userModel->getUserIdByEmail($recipient['email']);
                if ($recipientUserId) {
                    $this->logStatusService->createNotification($recipientUserId, $notificationType, $pesan_pembuka, $kegiatanId);
                }
            } catch (Exception $e) {
                error_log("NotificationService: Failed to create in-app notification for {$recipient['email']} for Kegiatan ID {$kegiatanId} ({$notificationType}): " . $e->getMessage());
            }
        }

        foreach ($to as $recipient) {
            try {
                $emailData['nama_penerima'] = $recipient['name'] ?? explode('@', $recipient['email'])[0]; // Personalize recipient name
                $this->mailer->send($recipient['email'], $subject, 'notification', $emailData);
                error_log("NotificationService: Email sent to {$recipient['email']} for Kegiatan ID {$kegiatanId} ({$notificationType}).");
            } catch (Exception $e) {
                error_log("NotificationService: Failed to send email to {$recipient['email']} for Kegiatan ID {$kegiatanId} ({$notificationType}): " . $e->getMessage());
            }
        }
    }
}
