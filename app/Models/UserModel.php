<?php

namespace App\Models;

use Exception;
use mysqli;

/**
 * UserModel - Main Model for User Management
 *
 * Mengelola data pengguna (tbl_user) dan peran (tbl_role).
 * Menggabungkan fungsionalitas legacy dan modern.
 *
 * @package App\Models
 */
class UserModel
{
    /**
     * @var mysqli Database connection instance
     */
    private $db;
    protected $table = 'tbl_user';

    /**
     * Constructor
     *
     * @param mysqli $db Database connection instance
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    // ==========================================
    // AUTHENTICATION & SECURITY
    // ==========================================

    /**
     * Memverifikasi login user berdasarkan email dan password.
     *
     * @param string $email User email address
     * @param string $password Plain text password to verify
     * @return array|false User data array if successful, false otherwise
     */
    public function verifyUserLogin(string $email, string $password)
    {
        // Fix: Use tbl_user and tbl_role (singular) per schema
        // Fix: Use correct column names (userId, roleId, namaRole)
        $query = "SELECT u.*, r.namaRole 
                  FROM tbl_user u 
                  JOIN tbl_role r ON u.roleId = r.roleId
                  WHERE u.email = ? AND u.status = 'Aktif'";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('UserModel::verifyUserLogin - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 's', $email);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // Jika user ditemukan dan password cocok
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
        } else {
            error_log('UserModel::verifyUserLogin - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
        }

        return false;
    }

    // ==========================================
    // CORE CRUD OPERATIONS
    // ==========================================

    /**
     * Mengambil satu data user spesifik berdasarkan ID.
     *
     * @param int $userId User ID to retrieve
     * @return array|null User data array if found, null otherwise
     */
    public function getById(int $userId): ?array
    {
        $query = "SELECT u.*, r.namaRole 
                  FROM tbl_user u
                  JOIN tbl_role r ON u.roleId = r.roleId
                  WHERE u.userId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('UserModel::getById - Prepare failed: ' . mysqli_error($this->db));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $userId);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $user ?: null;
        } else {
            error_log('UserModel::getById - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }
    }

    /**
     * Alias for getById for compatibility
     */
    public function getUserById(int $userId): ?array
    {
        return $this->getById($userId);
    }

    /**
     * Mengambil semua data user dengan nama role-nya.
     *
     * @return array Array of user records with role information
     */
    public function getAllUsers(): array
    {
        $query = "SELECT u.userId, u.nama, u.email, u.namaJurusan, u.status, r.namaRole, u.created_at 
                  FROM tbl_user u
                  JOIN tbl_role r ON u.roleId = r.roleId
                  ORDER BY u.created_at DESC";

        $result = mysqli_query($this->db, $query);
        if ($result === false) {
            error_log('UserModel::getAllUsers - Query failed: ' . mysqli_error($this->db));
            return [];
        }

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }

        mysqli_free_result($result);
        return $users;
    }

    /**
     * Menambah user baru ke tbl_user.
     *
     * @param string $userName
     * @param int $roleId
     * @param int|null $prodiId (Not used in schema directly, maybe mapped to namaJurusan?)
     * @param string $email
     * @param string $password
     * @param string $confirmPassword
     * @return bool
     */
    public function insertUser(
        string $userName,
        int $roleId,
        ?string $namaJurusan, // Changed from int $prodiId to string match schema
        string $email,
        string $password,
        string $confirmPassword
    ): bool {
        try {
            if ($password !== $confirmPassword) {
                throw new Exception('Password dan konfirmasi password tidak sesuai.');
            }
        } catch (Exception $e) {
            error_log('UserModel::insertUser - Validation error: ' . $e->getMessage());
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Schema: userId, nama, email, password, roleId, namaJurusan, created_at, status
        $query = "INSERT INTO tbl_user (nama, roleId, namaJurusan, email, password, status) 
                  VALUES (?, ?, ?, ?, ?, 'Aktif')";
        
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('UserModel::insertUser - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'sisss', $userName, $roleId, $namaJurusan, $email, $passwordHash);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('UserModel::insertUser - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengupdate data user.
     *
     * @param int $userId
     * @param string $userName
     * @param int $roleId
     * @param string|null $namaJurusan
     * @param string $email
     * @param string $status ('Aktif' or 'Tidak Aktif')
     * @return bool
     */
    public function updateUser(
        int $userId,
        string $userName,
        int $roleId,
        ?string $namaJurusan,
        string $email,
        string $status
    ): bool {
        $query = "UPDATE tbl_user SET nama = ?, roleId = ?, namaJurusan = ?, email = ?, status = ? WHERE userId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('UserModel::updateUser - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'sisssi', $userName, $roleId, $namaJurusan, $email, $status, $userId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('UserModel::updateUser - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengubah password user.
     */
    public function updateUserPassword(
        int $userId,
        string $newPassword,
        string $confirmPassword
    ): bool {
        if ($newPassword !== $confirmPassword) {
            return false;
        }

        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        $query = "UPDATE tbl_user SET password = ? WHERE userId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('UserModel::updateUserPassword - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'si', $passwordHash, $userId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('UserModel::updateUserPassword - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function deleteUser(int $userId): bool
    {
        $query = "DELETE FROM tbl_user WHERE userId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('UserModel::deleteUser - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $userId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('UserModel::deleteUser - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    // ==========================================
    // ROLE HELPER METHODS
    // ==========================================

    public function getAllRoles(): array
    {
        $query = "SELECT roleId, namaRole FROM tbl_role ORDER BY namaRole ASC";
        $result = mysqli_query($this->db, $query);

        if ($result === false) {
            error_log('UserModel::getAllRoles - Query failed: ' . mysqli_error($this->db));
            return [];
        }

        $roles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = $row;
        }
        mysqli_free_result($result);
        return $roles;
    }

    public function getUsersByRole(string $roleName): array
    {
        $query = "SELECT u.userId, u.nama, u.email, u.roleId
                  FROM tbl_user u
                  JOIN tbl_role r ON u.roleId = r.roleId
                  WHERE r.namaRole = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "s", $roleName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $users;
    }
    
    /**
     * Get user ID by email address.
     *
     * @param string $email
     * @return int|null
     */
    public function getUserIdByEmail(string $email): ?int
    {
        $query = "SELECT userId FROM {$this->table} WHERE email = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user['userId'] ?? null;
    }
}