<?php

namespace App\Models;

/**
 * LoginModel - Authentication Model
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Refactored to remove constructor trap
 */

class LoginModel
{
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection untuk database connection
     *
     * @param mysqli|null $db Database connection (optional for backward compatibility)
     */
    public function __construct($db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            // Fallback to global db() helper function from bootstrap.php
            if (function_exists('db')) {
                $this->db = db();
            } else {
                throw new \Exception("Database connection not provided to LoginModel. Ensure bootstrap.php is loaded.");
            }
        }
    }

    /**
     * Mengambil data user berdasarkan email
     */
    public function getUserByEmail($email)
    {
        // Catatan: Parameter $password dihapus dari sini.
        // Model tugasnya hanya mengambil data. Pengecekan password dilakukan di Controller (AuthController).

        // 3. Perbaikan Query: Gunakan '?' bukan ':email' untuk MySQLi
        $query = "SELECT 
            u.userId,
            u.nama,
            u.email,
            u.password,
            u.roleId,
            r.namaRole,    /* <--- Ini mengambil nama role dari tabel tbl_role */
            j.namaJurusan
          FROM tbl_user u
          LEFT JOIN tbl_role r ON u.roleId = r.roleId
          LEFT JOIN tbl_jurusan j ON u.namaJurusan = j.namaJurusan
          WHERE u.email = ?";

        // 4. Prepare Statement menggunakan $this->db
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt) {
            // 5. Binding Parameter (s = string) menggantikan tanda tanya (?) dengan isi variabel $email
            mysqli_stmt_bind_param($stmt, "s", $email);

            // 6. Eksekusi
            mysqli_stmt_execute($stmt);

            // 7. Ambil Hasilnya
            $result = mysqli_stmt_get_result($stmt);

            // 8. Kembalikan data dalam bentuk Array Asosiatif (agar enak dipakai $user['nama'])
            if ($row = mysqli_fetch_assoc($result)) {
                mysqli_stmt_close($stmt);
                return $row;
            } else {
                mysqli_stmt_close($stmt);
                return false; // Email tidak ditemukan
            }
        } else {
            // Jika query error (misal salah nama tabel) - throw exception instead of die()
            throw new \RuntimeException("LoginModel Query Error: " . mysqli_error($this->db));
        }
    }
}
