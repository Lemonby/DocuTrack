<?php

namespace App\Services;

use App\Models\LogStatusModel;
use App\Core\Mailer;
use Throwable;

class LogStatusService
{
    private $logStatusModel;
    private $mailer;

    public function __construct($db)
    {
        $this->logStatusModel = new LogStatusModel($db);
        // Mailer diinstansiasi hanya saat dibutuhkan untuk menghemat resource,
        // atau bisa di-inject via constructor jika menggunakan container.
        // Di sini kita lazy load di method createNotification saja atau constructor jika ringan.
        // Untuk amannya, kita load saat butuh kirim email.
    }

    /**
     * Create a new notification for a user and send email.
     * @param int    $userId The ID of the recipient user.
     * @param string $tipe The type of notification (e.g., 'APPROVAL', 'REJECTION', 'REMINDER').
     * @param string $pesan The message content of the notification.
     * @param int|null $refId The ID of the related entity (e.g., kegiatan_id).
     * @param string $tipeLogSuffix Suffix for log type in DB (defaults to $tipe).
     * @param int|null $kegiatanIdExplicit Optional explicit kegiatan ID if refId is ambiguous.
     * @return bool
     */
    public function createNotification(int $userId, string $tipe, string $pesan, int $refId = null, string $tipeLogSuffix = null, ?int $kegiatanIdExplicit = null): bool
    {
        $link = '#'; // Default link
        if ($refId) {
            // Asumsi link default ke detail kegiatan, bisa disesuaikan per role nanti jika perlu
            // Namun karena ini notifikasi untuk Pengusul, biasanya ke detail pengajuan mereka
            $link = "/docutrack/public/pengajuan/detail/" . $refId;
        }

        $judul = "Notifikasi DocuTrack";
        $tipeLogDB = 'NOTIFIKASI_' . ($tipeLogSuffix ?: strtoupper($tipe));

        $data = [
            'user_id' => $userId,
            'tipe_log' => $tipeLogDB,
            'id_referensi' => $refId,
            'status' => 'BELUM_DIBACA',
            'konten_json' => json_encode(['judul' => $judul, 'pesan' => $pesan, 'link' => $link])
        ];

        // 1. Simpan ke Database
        $created = (bool)$this->logStatusModel->create($data);

        // 2. Kirim Email (Fire and Forget logic ideally, but synchronous here)
        if ($created) {
            $this->sendEmailNotification($userId, $tipe, $pesan, $link, $refId ?: $kegiatanIdExplicit);
        }

        return $created;
    }

    /**
     * Helper untuk mengirim email
     */
    private function sendEmailNotification($userId, $tipe, $pesan, $link, $kegiatanId)
    {
        try {
            $user = $this->logStatusModel->getUserInfo($userId);
            if (!$user || empty($user['email'])) {
                return;
            }

            // Inisialisasi Mailer
            $mailer = new Mailer();

            // Persiapkan Data View
            $kegiatan = null;
            if ($kegiatanId) {
                $kegiatan = $this->logStatusModel->getKegiatanInfo($kegiatanId);
            }

            // Tentukan Warna & Label
            $statusColorClass = 'bg-blue';
            $statusLabel = 'INFORMASI';

            switch (strtoupper($tipe)) {
                case 'APPROVAL':
                    $statusColorClass = 'bg-green';
                    $statusLabel = 'DISETUJUI';
                    break;
                case 'REJECTION':
                    $statusColorClass = 'bg-red';
                    $statusLabel = 'DITOLAK';
                    break;
                case 'REVISION':
                    $statusColorClass = 'bg-yellow';
                    $statusLabel = 'PERLU REVISI';
                    break;
                case 'PENCAIRAN':
                    $statusColorClass = 'bg-green';
                    $statusLabel = 'DANA CAIR';
                    break;
            }

            // Coba ekstrak "Alasan" atau "Catatan" dari pesan jika ada pola "Alasan: ..."
            $catatanTambahan = '';
            if (preg_match('/(Alasan|Catatan|Komentar):\s*(.*)/i', $pesan, $matches)) {
                $catatanTambahan = $matches[2];
                // Hapus catatan dari pesan utama agar tidak duplikat (opsional, disini kita biarkan pesan asli utuh atau dipotong)
            }

            $emailData = [
                'nama_penerima' => $user['nama'],
                'pesan_pembuka' => $pesan,
                'status_color_class' => $statusColorClass,
                'status_label' => $statusLabel,
                'detail_kegiatan' => $kegiatan,
                'catatan_tambahan' => $catatanTambahan,
                'link_action' => 'http://' . $_SERVER['HTTP_HOST'] . $link // Ensure absolute URL
            ];

            // Subject Email
            $subject = "[DocuTrack] " . ucfirst(strtolower($statusLabel)) . ": " . ($kegiatan['namaKegiatan'] ?? 'Notifikasi Baru');

            // Kirim
            $mailer->send($user['email'], $subject, 'notification', $emailData);
        } catch (Throwable $e) {
            // Jangan biarkan error email menghentikan proses utama
            error_log("Gagal mengirim email notifikasi ke UserID {$userId}: " . $e->getMessage());
        }
    }

    /**
     * Get notifications for a user, formatted for display.
     * @param int $userId
     * @return array
     */
    public function getNotificationsForUser(int $userId): array
    {
        $notifications = $this->logStatusModel->getUnreadNotifications($userId);
        $unreadCount = $this->logStatusModel->getUnreadNotificationCount($userId);

        $formatted = [];
        foreach ($notifications as $notif) {
            $konten = json_decode($notif['konten_json'] ?? '{}', true);
            $formatted[] = [
                'id' => $notif['id'],
                'judul' => $notif['judul'] ?? 'Notifikasi',
                'pesan' => $konten['pesan'] ?? '',
                'link' => $konten['link'] ?? '#',
                'tipe_log' => str_replace('NOTIFIKASI_', '', $notif['tipe_log']), // Helper frontend ikon
                'created_at' => $notif['created_at'], // Helper frontend time ago
                'status' => $notif['status']
            ];
        }

        return [
            'items' => $formatted,
            'unread_count' => $unreadCount
        ];
    }

    /**
     * Mark a notification as read.
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function markNotificationAsRead(int $notificationId, int $userId): bool
    {
        return $this->logStatusModel->markAsRead($notificationId, $userId);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(int $userId): bool
    {
        return $this->logStatusModel->markAllAsRead($userId);
    }

    /**
     * Helper to format timestamp into "time ago" string.
     * @param string $timestamp
     * @return string
     */
    private function timeAgo(string $timestamp): string
    {
        $time = strtotime($timestamp);
        $diff = time() - $time;

        if ($diff < 60) {
            return 'baru saja';
        }

        $intervals = [
            31536000 => 'tahun',
            2592000 => 'bulan',
            604800 => 'minggu',
            86400 => 'hari',
            3600 => 'jam',
            60 => 'menit'
        ];

        foreach ($intervals as $secs => $str) {
            $d = $diff / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . $str . ' yang lalu';
            }
        }
        return 'beberapa detik yang lalu';
    }
}
