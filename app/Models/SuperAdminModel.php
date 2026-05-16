<?php

declare(strict_types=1);

namespace App\Models;

use mysqli;
use Exception;

/**
 * SuperAdminModel - Super Admin Management Model
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.3.0 - Refactored for Strict Dependency Injection
 */

class SuperAdminModel
{
    /**
     * @var mysqli Database connection instance
     */
    private mysqli $db;

    /**
     * Constructor - Strict Dependency Injection
     * 
     * @param mysqli $db Database connection instance (Required)
     */
    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    // =========================================================
    // 1. STATISTIK DASHBOARD
    // =========================================================

    public function getDashboardStats(): array
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statusUtamaId = 3 THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN statusUtamaId = 1 THEN 1 ELSE 0 END) as menunggu,
                    SUM(CASE WHEN statusUtamaId = 2 THEN 1 ELSE 0 END) as revisi
                  FROM tbl_kegiatan";

        $result = mysqli_query($this->db, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return [
                'total' => (int)($row['total'] ?? 0),
                'disetujui' => (int)($row['disetujui'] ?? 0),
                'ditolak' => (int)($row['ditolak'] ?? 0),
                'menunggu' => (int)($row['menunggu'] ?? 0),
                'revisi' => (int)($row['revisi'] ?? 0)
            ];
        }

        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0, 'revisi' => 0];
    }

    public function getListJurusan(): array
    {
        $query = "SELECT namaJurusan FROM tbl_jurusan ORDER BY namaJurusan ASC";

        $result = mysqli_query($this->db, $query);
        $list = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row['namaJurusan'];
            }
        }

        return $list;
    }

    public function getListProdi(): array
    {
        $query = "SELECT DISTINCT prodiPenyelenggara as prodi 
                  FROM tbl_kegiatan 
                  WHERE prodiPenyelenggara IS NOT NULL AND prodiPenyelenggara != ''
                  ORDER BY prodiPenyelenggara ASC";

        $result = mysqli_query($this->db, $query);
        $list = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row['prodi'];
            }
        }

        return $list;
    }

    // =========================================================
    // 2. MONITORING KEGIATAN (GLOBAL - GOD MODE)
    // =========================================================

    /**
     * Get ALL activities for Super Admin monitoring (God Mode)
     * 
     * @param int $limit
     * @return array
     */
    public function getGlobalMonitoringKegiatan(int $limit = 50): array
    {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.jurusanPenyelenggara as jurusan,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as created_at,
                    k.statusUtamaId,
                    k.posisiId,
                    
                    -- Status Text Logic
                    CASE 
                        WHEN k.statusUtamaId = 3 THEN 'Disetujui'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.statusUtamaId = 2 THEN 'Revisi'
                        ELSE 'Menunggu'
                    END as status,
                    
                    -- Current Position/Role Logic
                    CASE 
                        WHEN k.posisiId = 1 THEN 'Admin'
                        WHEN k.posisiId = 2 THEN 'Verifikator'
                        WHEN k.posisiId = 3 THEN 'Wakil Direktur'
                        WHEN k.posisiId = 4 THEN 'PPK'
                        WHEN k.posisiId = 5 THEN 'Bendahara'
                        ELSE 'Unknown'
                    END as posisi_sekarang

                  FROM tbl_kegiatan k
                  ORDER BY k.createdAt DESC
                  LIMIT ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }

        return $data;
    }

    // =========================================================
    // 3. MONITORING LPJ (GLOBAL - GOD MODE)
    // =========================================================

    /**
     * Get ALL LPJs for Super Admin monitoring (God Mode)
     * 
     * @param int $limit
     * @return array
     */
    public function getGlobalMonitoringLPJ(int $limit = 50): array
    {
        $query = "SELECT 
                    l.lpjId as id,
                    l.submittedAt as tanggal_upload,
                    l.approvedAt,
                    l.grandTotalRealisasi as total_realisasi,
                    k.namaKegiatan as nama_kegiatan,
                    k.pemilikKegiatan as pengusul,
                    k.jurusanPenyelenggara as jurusan,
                    
                    CASE 
                        WHEN l.approvedAt IS NOT NULL THEN 'Disetujui'
                        WHEN l.komentarRevisi IS NOT NULL THEN 'Revisi'
                        WHEN l.submittedAt IS NOT NULL THEN 'Menunggu Verifikasi'
                        ELSE 'Draft'
                    END as status_lpj

                  FROM tbl_lpj l
                  JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                  ORDER BY l.submittedAt DESC
                  LIMIT ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }

        return $data;
    }

    // =========================================================
    // 4. KELOLA AKUN - List Users
    // =========================================================

    public function getAllUsers(): array
    {
        // Safe check for columns existence
        $checkCols = mysqli_query($this->db, "SHOW COLUMNS FROM tbl_user LIKE 'status'");
        $hasStatus = mysqli_num_rows($checkCols) > 0;
        
        $checkColsCreated = mysqli_query($this->db, "SHOW COLUMNS FROM tbl_user LIKE 'created_at'");
        $hasCreatedAt = mysqli_num_rows($checkColsCreated) > 0;

        $statusField = $hasStatus ? "u.status" : "'Aktif' as status";
        $dateField = $hasCreatedAt ? "u.created_at" : "NOW()";

        $query = "SELECT 
                    u.userId as id,
                    u.nama,
                    u.email,
                    r.namaRole as role,
                    u.namaJurusan as jurusan,
                    $statusField,
                    $dateField as last_login
                  FROM tbl_user u
                  LEFT JOIN tbl_role r ON u.roleId = r.roleId
                  ORDER BY u.userId ASC";

        $result = mysqli_query($this->db, $query);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $data[] = $row;
            }
        }

        return $data;
    }

    public function getUserById(int $userId): ?array
    {
        $query = "SELECT u.*, r.namaRole 
                  FROM tbl_user u
                  LEFT JOIN tbl_role r ON u.roleId = r.roleId
                  WHERE u.userId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ?: null;
    }

    public function createUser(array $data): bool
    {
        $fields = "nama, email, password, roleId, namaJurusan";
        $placeholders = "?, ?, ?, ?, ?";
        $types = "sssis";
        $values = [
            $data['nama'],
            $data['email'],
            $data['password'],
            (int)$data['roleId'],
            $data['namaJurusan'] ?? ''
        ];

        $checkCols = mysqli_query($this->db, "SHOW COLUMNS FROM tbl_user LIKE 'status'");
        if (mysqli_num_rows($checkCols) > 0 && isset($data['status'])) {
            $fields .= ", status";
            $placeholders .= ", ?";
            $types .= "s";
            $values[] = $data['status'];
        }

        $query = "INSERT INTO tbl_user ($fields) VALUES ($placeholders)";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$values);

        return mysqli_stmt_execute($stmt);
    }

    public function updateUser(int $userId, array $data): bool
    {
        $fields = [];
        $values = [];
        $types = "";

        if (!empty($data['nama'])) {
            $fields[] = "nama = ?";
            $values[] = $data['nama'];
            $types .= "s";
        }

        if (!empty($data['email'])) {
            $fields[] = "email = ?";
            $values[] = $data['email'];
            $types .= "s";
        }

        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $values[] = $data['password'];
            $types .= "s";
        }

        if (isset($data['roleId'])) {
            $fields[] = "roleId = ?";
            $values[] = (int)$data['roleId'];
            $types .= "i";
        }

        if (isset($data['namaJurusan'])) {
            $fields[] = "namaJurusan = ?";
            $values[] = $data['namaJurusan'];
            $types .= "s";
        }

        $checkCols = mysqli_query($this->db, "SHOW COLUMNS FROM tbl_user LIKE 'status'");
        if (mysqli_num_rows($checkCols) > 0 && isset($data['status'])) {
            $fields[] = "status = ?";
            $values[] = $data['status'];
            $types .= "s";
        }

        if (empty($fields)) {
            return false;
        }

        $values[] = $userId;
        $types .= "i";

        $query = "UPDATE tbl_user SET " . implode(", ", $fields) . " WHERE userId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$values);

        return mysqli_stmt_execute($stmt);
    }

    public function deleteUser(int $userId): bool
    {
        $query = "DELETE FROM tbl_user WHERE userId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);

        return mysqli_stmt_execute($stmt);
    }

    public function getAllRoles(): array
    {
        $query = "SELECT roleId, namaRole FROM tbl_role ORDER BY roleId ASC";

        $result = mysqli_query($this->db, $query);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }

        return $data;
    }

    // =========================================================
    // 6. IKU (Indikator Kinerja Utama)
    // =========================================================

    public function getAllIKU(): array
    {
        // Check if table exists
        $checkTable = mysqli_query($this->db, "SHOW TABLES LIKE 'tbl_iku'");

        if (mysqli_num_rows($checkTable) == 0) {
            return [];
        }

        $query = "SELECT id, indikator_kinerja as nama, deskripsi FROM tbl_iku ORDER BY created_at DESC";
        $result = mysqli_query($this->db, $query);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function createIKU(string $nama, string $deskripsi): bool
    {
        $query = "INSERT INTO tbl_iku (indikator_kinerja, deskripsi) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ss", $nama, $deskripsi);
        return mysqli_stmt_execute($stmt);
    }

    public function updateIKU(int $id, string $nama, string $deskripsi): bool
    {
        $query = "UPDATE tbl_iku SET indikator_kinerja = ?, deskripsi = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ssi", $nama, $deskripsi, $id);
        return mysqli_stmt_execute($stmt);
    }

    public function deleteIKU(int $id): bool
    {
        $query = "DELETE FROM tbl_iku WHERE id = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }
}