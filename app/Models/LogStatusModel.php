<?php

namespace App\Models;

use Exception;
use mysqli;

class LogStatusModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function executeQuery($sql, $params = [])
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }

        if (!empty($params)) {
            $types = "";
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= "i";
                } elseif (is_float($param)) {
                    $types .= "d";
                } else {
                    $types .= "s";
                }
            }
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        return $stmt;
    }

    /**
     * Get unread notifications for a user.
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUnreadNotifications(int $userId, int $limit = 10): array
    {
        $sql = "SELECT * FROM tbl_log_status 
                WHERE user_id = ? AND tipe_log LIKE 'NOTIFIKASI_%'
                ORDER BY created_at DESC
                LIMIT ?";
        $stmt = $this->executeQuery($sql, [$userId, $limit]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get the count of unread notifications for a user.
     * @param int $userId
     * @return int
     */
    public function getUnreadNotificationCount(int $userId): int
    {
        $sql = "SELECT COUNT(id) as count FROM tbl_log_status 
                WHERE user_id = ? AND tipe_log LIKE 'NOTIFIKASI_%' AND status = 'BELUM_DIBACA'";
        $stmt = $this->executeQuery($sql, [$userId]);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }

    /**
     * Create a new log status entry.
     * @param array $data
     * @return int|false The ID of the new record, or false on failure.
     */
    public function create(array $data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO tbl_log_status ($columns) VALUES ($placeholders)";

        $stmt = $this->executeQuery($sql, array_values($data));
        return $this->db->insert_id;
    }

    /**
     * Mark a specific notification as read.
     * @param int $logId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $logId, int $userId): bool
    {
        $sql = "UPDATE tbl_log_status SET status = 'DIBACA' WHERE id = ? AND user_id = ?";
        $this->executeQuery($sql, [$logId, $userId]);
        return $this->db->affected_rows > 0;
    }

    /**
     * Mark all notifications for a user as read.
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead(int $userId): bool
    {
        $sql = "UPDATE tbl_log_status SET status = 'DIBACA' WHERE user_id = ? AND status = 'BELUM_DIBACA'";
        $this->executeQuery($sql, [$userId]);
        return true;
    }

    /**
     * Get User Info for Email
     * @param int $userId
     * @return array|false
     */
    public function getUserInfo(int $userId)
    {
        $sql = "SELECT nama, email FROM tbl_user WHERE userId = ?";
        $stmt = $this->executeQuery($sql, [$userId]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Get Kegiatan Info for Email
     * @param int $kegiatanId
     * @return array|false
     */
    public function getKegiatanInfo(int $kegiatanId)
    {
        $sql = "SELECT namaKegiatan, pemilikKegiatan, createdAt FROM tbl_kegiatan WHERE kegiatanId = ?";
        $stmt = $this->executeQuery($sql, [$kegiatanId]);
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
