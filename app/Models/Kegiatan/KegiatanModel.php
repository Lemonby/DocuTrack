<?php

namespace App\Models\Kegiatan;

use Exception;
use mysqli;

// Ensure mysqli is available if not globally imported

class KegiatanModel
{
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection for database connection
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
                throw new \Exception("Database connection not provided to KegiatanModel. Ensure bootstrap.php is loaded.");
            }
        }
    }

    // ==== KEGIATAN FUNCTIONS ====

    /**
     * Menyisipkan data kegiatan baru.
     * (Sesuai skema image_90fc45.png)
     */
    public function insertKegiatan($pengusul_id, $diinput_oleh_user_id, $nama_kegiatan, $kode_mak)
    {
        // Status default: 1=Menunggu, posisi: 1=Admin
        $statusUtamaId = 1; // Menunggu
        $posisiId = 1; // Admin
        $wadirTujuan = 1; // Default Wadir 1

        $query = "INSERT INTO tbl_kegiatan (
                    userId, namaKegiatan, buktiMAK, 
                    statusUtamaId, posisiId, wadirTujuan
                  ) VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('KegiatanModel::insertKegiatan - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'issiii',
            $pengusul_id, // userId
            $nama_kegiatan, // namaKegiatan
            $kode_mak, // buktiMAK (code MAK)
            $statusUtamaId,
            $posisiId,
            $wadirTujuan
        );

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);
            return $newId; // Mengembalikan ID kegiatan yg baru dibuat
        } else {
            error_log('KegiatanModel::insertKegiatan - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function getAllKegiatanForAntrian()
    {
        $query = "SELECT 
                    k.kegiatan_id as id,
                    k.nama_kegiatan,
                    k.statusUtamaId as status,
                    u.nama as nama_pengusul,
                    k.prodiPenyelenggara as nama_prodi
                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_user u ON k.userId = u.userId
                  ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        if ($result === false) {
            error_log('KegiatanModel::getAllKegiatanForAntrian - Query failed: ' . mysqli_error($this->db));
            return [];
        }

        $antrian = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $antrian[] = $row;
        }
        mysqli_free_result($result);
        return $antrian;
    }

    // Mengambil semua data kegiatan berdasarkan ID pengusul.
    public function getKegiatanByPengusulId($pengusul_id)
    {
        $query = "SELECT * FROM tbl_kegiatan WHERE userId = ? ORDER BY createdAt DESC";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('KegiatanModel::getKegiatanByPengusulId - Prepare failed: ' . mysqli_error($this->db));
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $pengusul_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $kegiatan_list = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $kegiatan_list[] = $row;
            }
            mysqli_stmt_close($stmt);
            return $kegiatan_list;
        } else {
            error_log('KegiatanModel::getKegiatanByPengusulId - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return [];
        }
    }

    // Mengupdate data utama kegiatan (yang bisa diubah oleh pengusul, misal saat revisi)
    public function updateNamaKegiatanMak($kegiatan_id, $nama_kegiatan, $kode_mak)
    {
        $query = "UPDATE tbl_kegiatan SET namaKegiatan = ?, buktiMAK = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('KegiatanModel::updateNamaKegiatanMak - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ssi', $nama_kegiatan, $kode_mak, $kegiatan_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KegiatanModel::updateNamaKegiatanMak - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    // --- FUNGSI UPDATE STATUS (WORKFLOW) ---

    // Mengupdate kolom status_global.
    public function updateStatusGlobal($kegiatan_id, $status_utama_id)
    {
        $query = "UPDATE tbl_kegiatan SET statusUtamaId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('KegiatanModel::updateStatusGlobal - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'ii', $status_utama_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KegiatanModel::updateStatusGlobal - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    // Update position to Verifikator (posisiId=2)
    public function updateStatusVerifikator($kegiatan_id, $posisi_id = 2)
    {
        $query = "UPDATE tbl_kegiatan SET posisiId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('KegiatanModel::updateStatusVerifikator - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'ii', $posisi_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KegiatanModel::updateStatusVerifikator - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    // Update position to Wadir (posisiId=3)
    public function updateStatusWadir($kegiatan_id, $posisi_id = 3)
    {
        $query = "UPDATE tbl_kegiatan SET posisiId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('KegiatanModel::updateStatusWadir - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'ii', $posisi_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KegiatanModel::updateStatusWadir - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    // Update position to PPK (posisiId=4)
    public function updateStatusPpk($kegiatan_id, $posisi_id = 4)
    {
        $query = "UPDATE tbl_kegiatan SET posisiId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('KegiatanModel::updateStatusPpk - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'ii', $posisi_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KegiatanModel::updateStatusPpk - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    // Update position to Bendahara (posisiId=5)
    public function updateStatusPencairan($kegiatan_id, $posisi_id = 5)
    {
        $query = "UPDATE tbl_kegiatan SET posisiId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('KegiatanModel::updateStatusPencairan - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'ii', $posisi_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KegiatanModel::updateStatusPencairan - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    // Update pencairan dana info
    public function updatePencairanDana($kegiatan_id, $jumlah_dana_dicairkan, $metode_pencairan = 'dana_penuh', $catatan = '')
    {
        $query = "UPDATE tbl_kegiatan 
                  SET jumlahDicairkan = ?, 
                      metodePencairan = ?, 
                      tanggalPencairan = NOW(),
                      catatanBendahara = ?,
                      statusUtamaId = 3,
                      posisiId = 5
                  WHERE kegiatanId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('KegiatanModel::updatePencairanDana - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        // Corrected bind_param: match SQL with function parameters
        mysqli_stmt_bind_param(
            $stmt,
            'dssi',
            $jumlah_dana_dicairkan,
            $metode_pencairan,
            $catatan,
            $kegiatan_id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KegiatanModel::updatePencairanDana - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Menghapus data kegiatan berdasarkan ID.
     *
     * @param int $kegiatan_id ID kegiatan yang akan dihapus
     * @return bool True jika berhasil, false jika gagal
     */
    public function deleteKegiatan($kegiatan_id)
    {
        $query = "DELETE FROM tbl_kegiatan WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('KegiatanModel::deleteKegiatan - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $kegiatan_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KegiatanModel::deleteKegiatan - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    // =========================================================
    // DASHBOARD & STATISTICS METHODS - MVC Compliance (Dec 2025)
    // Moved from KegiatanService to proper Model layer
    // =========================================================

    /**
     * Get dashboard statistics for kegiatan
     *
     * @return array Statistics data with keys: total, disetujui, ditolak, menunggu
     */
    public function getDashboardStats()
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN posisiId = 5 AND tanggalPencairan IS NOT NULL THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN statusUtamaId != 4 AND (posisiId != 5 OR tanggalPencairan IS NULL) THEN 1 ELSE 0 END) as menunggu
                FROM tbl_kegiatan";

        $result = mysqli_query($this->db, $query);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0];
    }

    /**
     * Get dashboard KAK list with optional jurusan filter
     *
     * @param string|null $jurusan Optional filter by jurusan name
     * @return array Array of kegiatan data
     */
    public function getDashboardKAK($jurusan = null)
    {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    CONCAT(k.pemilikKegiatan, ' (', k.nimPelaksana, '), ', k.prodiPenyelenggara) as pengusul,
                    k.createdAt as tanggal_pengajuan,
                    k.posisiId as posisi,
                    k.statusUtamaId,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.statusUtamaId = 2 THEN 'Revisi'
                        WHEN k.statusUtamaId = 5 THEN 'Disetujui'
                        WHEN k.posisiId = 1 AND k.statusUtamaId = 3 THEN 'Usulan Disetujui'
                        WHEN k.posisiId = 1 AND k.statusUtamaId = 1 THEN 'Menunggu'
                        WHEN k.posisiId = 2 THEN 'DiVerifikator'
                        WHEN k.posisiId = 4 THEN 'Di PPK'
                        WHEN k.posisiId = 3 THEN 'Di Wadir'
                        WHEN k.posisiId = 5 AND k.tanggalPencairan IS NULL THEN 'Di Bendahara'
                        WHEN k.posisiId = 5 AND k.tanggalPencairan IS NOT NULL THEN 'Dana Diberikan'
                        ELSE 'Menunggu'
                    END as status
                FROM tbl_kegiatan k
                LEFT JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId";

        // Add WHERE clause if jurusan filter is provided
        if ($jurusan !== null && !empty($jurusan)) {
            $query .= " WHERE k.jurusanPenyelenggara = ?";
        }

        $query .= " ORDER BY k.createdAt DESC";

        // Execute with or without parameter binding
        if ($jurusan !== null && !empty($jurusan)) {
            $stmt = mysqli_prepare($this->db, $query);
            if ($stmt === false) {
                error_log('KegiatanModel::getDashboardKAK - Prepare failed: ' . mysqli_error($this->db));
                return [];
            }
            mysqli_stmt_bind_param($stmt, 's', $jurusan);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            $result = mysqli_query($this->db, $query);
            if ($result === false) {
                error_log('KegiatanModel::getDashboardKAK - Query failed: ' . mysqli_error($this->db));
                return [];
            }
        }

        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            mysqli_free_result($result);
        }

        return $data;
    }
}
