<?php

declare(strict_types=1);

namespace App\Models;

use mysqli;
use RuntimeException;
use Throwable;

/**
 * VerifikatorModel - Verifikator Management Model
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Refactored to remove constructor trap
 */
class VerifikatorModel
{
    private mysqli $db;

    public function __construct(?mysqli $db = null)
    {
        if ($db instanceof mysqli) {
            $this->db = $db;
            return;
        }

        // Fallback to global db() helper function from bootstrap.php
        if (function_exists('db')) {
            $this->db = db();
        } else {
            // Last resort: get from Database singleton
            try {
                $this->db = \App\Core\Database::getInstance()->getConnection();
            } catch (\Exception $e) {
                throw new \Exception("Database connection not provided to VerifikatorModel. " . $e->getMessage());
            }
        }
    }

    /**
     * Mengambil data statistik untuk dashboard.
     */
    public function getDashboardStats()
    {
        $query = "SELECT
                    COUNT(*) as total,
                    SUM(CASE
                        WHEN posisiId IN (4, 3, 5) AND statusUtamaId != 4 OR statusUtamaId IN (5, 6)  THEN 1
                        ELSE 0
                    END) as disetujui,
                    SUM(CASE
                        WHEN statusUtamaId = 4 THEN 1
                        ELSE 0
                    END) as ditolak,
                    SUM(CASE
                        WHEN posisiId = 2 THEN 1
                        ELSE 0
                    END) as pending
                FROM tbl_kegiatan";

        $result = mysqli_query($this->db, $query);
        if ($result) {
            $data = mysqli_fetch_assoc($result);
            return [
                'total' => $data['total'],
                'disetujui' => $data['disetujui'],
                'ditolak' => $data['ditolak'],
                'pending' => $data['pending']
            ];
        }
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'pending' => 0];
    }

    /**
     * Mengambil daftar KAK untuk tabel.
     */
    public function getDashboardKAK()
    {
        $query = "SELECT
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.pemilikKegiatan as pengusul,
                    k.createdAt as tanggal_pengajuan,
                    s.namaStatusUsulan as status
                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.posisiId = 2
                ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($row['status'])) {
                    $row['status'] = ucfirst($row['status']);
                } else {
                    $row['status'] = 'Menunggu';
                }
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Mengambil indikator KAK.
     */
    public function getIndikatorByKAK($kakId)
    {
        $query = "SELECT bulan, indikatorKeberhasilan as nama, targetPersen as target 
                  FROM tbl_indikator_kak 
                  WHERE kakId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Mengambil tahapan pelaksanaan.
     */
    public function getTahapanByKAK($kakId)
    {
        $query = "SELECT namaTahapan FROM tbl_tahapan_pelaksanaan WHERE kakId = ? ORDER BY tahapanId ASC";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row['namaTahapan'];
        }
        return $data;
    }

    /**
     * Mengambil RAB (dikelompokkan berdasarkan kategori).
     */
    public function getRABByKAK($kakId)
    {
        $query = "SELECT r.*, cat.namaKategori
                  FROM tbl_rab r
                  JOIN tbl_kategori_rab cat ON r.kategoriId = cat.kategoriRabId
                  WHERE r.kakId = ?
                  ORDER BY cat.kategoriRabId ASC, r.rabItemId ASC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[$row['namaKategori']][] = $row;
        }
        return $data;
    }

    /**
     * Memperbarui status kegiatan.
     */
    public function updateStatus($kegiatanId, $statusId)
    {
        $query = "UPDATE tbl_kegiatan SET statusUtamaId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $statusId, $kegiatanId);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Mengambil daftar jurusan.
     */
    public function getListJurusan()
    {
        $query = "SELECT * FROM tbl_jurusan j";
        $stmt = mysqli_prepare($this->db, $query);

        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->db));
            return [];
        }

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return [];
        }

        $result = mysqli_stmt_get_result($stmt);
        $jurusan = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $jurusan[] = $row;
        }

        mysqli_stmt_close($stmt);
        return $jurusan;
    }

    /**
     * Mengambil data riwayat verifikasi.
     * Menggunakan pendekatan "Last Decision Snapshot":
     * Status yang ditampilkan adalah status TERAKHIR yang diberikan oleh Verifikator.
     * Tidak peduli posisi dokumen sekarang ada di mana (PPK/Wadir), yang penting keputusan verifikator.
     */
    public function getRiwayat($userJurusan = null)
    {
        // Subquery untuk mendapatkan ID history TERAKHIR yang dilakukan oleh Verifikator (Role 2) per kegiatan
        // Logika: Group by kegiatanId, ambil MAX(progressHistoryId) dimana usernya adalah verifikator
        $query = "SELECT
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.createdAt as tanggal_pengajuan,
                    CASE
                        WHEN latest_verif.statusId = 3 THEN 'Disetujui'
                        WHEN latest_verif.statusId = 2 THEN 'Revisi'
                        WHEN latest_verif.statusId = 4 THEN 'Ditolak'
                        ELSE 'Diproses' -- Fallback, seharusnya tidak muncul jika data konsisten
                    END as status
                  FROM tbl_kegiatan k
                  INNER JOIN (
                      SELECT ph.kegiatanId, ph.statusId
                      FROM tbl_progress_history ph
                      JOIN tbl_user u ON ph.changedByUserId = u.userId
                      WHERE u.roleId = 2 -- Filter hanya aksi User Role Verifikator
                      AND ph.progressHistoryId IN (
                          SELECT MAX(ph_inner.progressHistoryId)
                          FROM tbl_progress_history ph_inner
                          JOIN tbl_user u_inner ON ph_inner.changedByUserId = u_inner.userId
                          WHERE u_inner.roleId = 2
                          GROUP BY ph_inner.kegiatanId
                      )
                  ) latest_verif ON k.kegiatanId = latest_verif.kegiatanId
                  WHERE 1=1";

        $types = "";
        $params = [];

        if (!empty($userJurusan)) {
            $query .= " AND k.jurusanPenyelenggara = ?";
            $types .= "s";
            $params[] = $userJurusan;
        }

        $query .= " ORDER BY k.createdAt DESC";

        $stmt = $this->db->prepare($query);
        if ($stmt === false) {
             error_log("Prepare failed in getRiwayat: " . $this->db->error);
             return [];
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $stmt->close();
        return $data;
    }

    /**
     * Mengambil detail kegiatan.
     */
    public function getDetailKegiatan($kegiatanId)
    {
        $query = "SELECT
                    k.*,
                    kak.*,
                    k.tanggalMulai as tanggal_mulai,
                    k.tanggalSelesai as tanggal_selesai,
                    k.suratPengantar as file_surat_pengantar,
                    k.pemilikKegiatan as nama_pengusul,
                    k.namaPJ as nama_pj,
                    k.nip as nim_pj,
                    k.nimPelaksana as nim_pelaksana,
                    k.pemilikKegiatan as nama_pelaksana,
                    s.namaStatusUsulan as status_text
                FROM tbl_kegiatan k
                JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                LEFT JOIN tbl_user u ON u.userId = k.userId
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                WHERE k.kegiatanId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_assoc($result);
    }

    /**
     * Memperbarui status kegiatan untuk revisi.
     * @param int $kegiatanId
     * @param array $komentarRevisi
     * @return bool
     * @throws RuntimeException
     * @throws Throwable
     */
    public function updateKegiatanRevisionStatus(int $kegiatanId, array $komentarRevisi = []): bool
    {
        $statusRevisi = 2; // Revisi
        $currentPosisi = 2; // Verifikator
        $backToPosisi = 1; // Kembali ke Admin/Pengusul
        $userId = isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])
            ? (int) $_SESSION['user_id']
            : null;
        $roleId = 2; // Role ID for Verifikator

        $connection = $this->db;
        $transactionStarted = false;

        try {
            $connection->begin_transaction();
            $transactionStarted = true;

            // Update status kegiatan
            $query = "UPDATE tbl_kegiatan
                      SET statusUtamaId = ?, posisiId = ?
                      WHERE kegiatanId = ?";

            $stmt = $connection->prepare($query);
            if ($stmt === false) {
                throw new RuntimeException('Gagal menyiapkan statement update kegiatan.');
            }
            $stmt->bind_param("iii", $statusRevisi, $backToPosisi, $kegiatanId);

            if (!$stmt->execute()) {
                throw new RuntimeException('Gagal update kegiatan: ' . $stmt->error);
            }
            $stmt->close();

            // Catat History
            $historyId = $this->insertProgressHistory(
                $kegiatanId,
                $statusRevisi,
                $currentPosisi,
                $backToPosisi,
                $userId,
                'verifikator_revise'
            );

            if ($historyId <= 0) {
                throw new RuntimeException('Gagal mencatat riwayat.');
            }

            // Insert revision comments
            if (!empty($komentarRevisi)) {
                foreach ($komentarRevisi as $komentar) {
                    $this->insertRevisiComment(
                        $historyId,
                        $userId,
                        $roleId,
                        $komentar['komentar'] ?? '',
                        $komentar['targetTabel'] ?? 'tbl_kegiatan',
                        $komentar['targetKolom'] ?? null
                    );
                }
            }

            $connection->commit();
            return true;
        } catch (Throwable $e) {
            if ($transactionStarted) {
                $connection->rollback();
            }
            error_log('reviseUsulan (Model) Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Memasukkan record ke tabel tbl_progress_history.
     */
    private function insertProgressHistory(
        int $kegiatanId,
        int $statusId,
        int $fromPosisi,
        int $toPosisi,
        ?int $userId,
        string $actionType
    ): int {
        $sql = 'INSERT INTO tbl_progress_history (kegiatanId, statusId, changedByUserId) VALUES (?, ?, ';
        $placeholders = $userId === null ? 'NULL)' : '?)';
        $stmt = $this->db->prepare($sql . $placeholders);

        if ($stmt === false) {
            throw new RuntimeException('Gagal menyiapkan statement progress history.');
        }

        if ($userId === null) {
            $stmt->bind_param('ii', $kegiatanId, $statusId);
        } else {
            $stmt->bind_param('iii', $kegiatanId, $statusId, $userId);
        }

        $stmt->execute();
        $insertId = (int) $this->db->insert_id;
        $stmt->close();

        return $insertId;
    }

    /**
     * Memasukkan komentar revisi ke tabel tbl_revisi_comment.
     */
    private function insertRevisiComment(
        $historyId,
        $userId,
        $roleId,
        $komentar,
        $targetTabel = null,
        $targetKolom = null
    ) {
        $query = "INSERT INTO tbl_revisi_comment
                  (progressHistoryId, komentarRevisi, targetTabel, targetKolom)
                  VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "isss", $historyId, $komentar, $targetTabel, $targetKolom);

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    /**
     * Memperbarui status kegiatan untuk approval.
     * @param int $kegiatanId
     * @param string $kodeMak
     * @param float $danaDisetujui Grand total RAB yang disetujui
     * @param string|null $catatan
     * @return bool
     * @throws RuntimeException
     * @throws Throwable
     */
    public function updateKegiatanApprovalStatus(int $kegiatanId, string $kodeMak, float $danaDisetujui, ?string $catatan = null): bool
    {
        $trimmedMak = trim($kodeMak);

        if ($trimmedMak === '') {
            throw new RuntimeException('Kode MAK tidak boleh kosong.');
        }

        if ($danaDisetujui < 0) {
            throw new RuntimeException('Dana yang disetujui tidak boleh negatif.');
        }

        $note = null;
        if ($catatan !== null) {
            $catatanTrimmed = trim($catatan);
            if ($catatanTrimmed !== '') {
                $note = $catatanTrimmed;
            }
        }

        $connection = $this->db;
        $currentPosisi = 2; // Verifikator
        $nextPosisi = 1;    // Admin/Pengusul (DIKEMBALIKAN: untuk input rincian kegiatan)
        $nextStatus = 3;    // Disetujui Verifikator - Menunggu Rincian

        $userId = isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])
            ? (int) $_SESSION['user_id']
            : null;

        $transactionStarted = false;

        try {
            $connection->begin_transaction();
            $transactionStarted = true;

            // Lock row untuk mencegah race condition
            $lockStmt = $connection->prepare('SELECT kegiatanId FROM tbl_kegiatan WHERE kegiatanId = ? FOR UPDATE');
            if ($lockStmt === false) {
                throw new RuntimeException('Gagal menyiapkan statement lock.');
            }
            $lockStmt->bind_param('i', $kegiatanId);
            $lockStmt->execute();
            $result = $lockStmt->get_result();
            if ($result->num_rows === 0) {
                throw new RuntimeException('Kegiatan tidak ditemukan.');
            }
            $lockStmt->close();

            // Prepare Update Query
            $sql = 'UPDATE tbl_kegiatan
                    SET statusUtamaId = ?,
                        posisiId = ?,
                        buktiMAK = ?,
                        umpanBalikVerifikator = ?,
                        danaDisetujui = ?
                    WHERE kegiatanId = ?';

            $updateStmt = $connection->prepare($sql);
            if ($updateStmt === false) {
                throw new RuntimeException('Gagal menyiapkan statement update.');
            }

            $updateStmt->bind_param('iissdi', $nextStatus, $nextPosisi, $trimmedMak, $note, $danaDisetujui, $kegiatanId);

            if (!$updateStmt->execute()) {
                throw new RuntimeException('Gagal update kegiatan: ' . $updateStmt->error);
            }
            $updateStmt->close();

            // Catat History
            $historyId = $this->insertProgressHistory(
                $kegiatanId,
                $nextStatus,
                $currentPosisi,
                $nextPosisi,
                $userId,
                'verifikator_approve'
            );

            if ($historyId <= 0) {
                throw new RuntimeException('Gagal mencatat riwayat.');
            }

            $connection->commit();
            return true;
        } catch (Throwable $e) {
            if ($transactionStarted) {
                $connection->rollback();
            }
            error_log('approveUsulan Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Memperbarui status kegiatan untuk penolakan.
     * @param int $kegiatanId
     * @param string $alasanPenolakan
     * @return bool
     * @throws RuntimeException
     * @throws Throwable
     */
    public function updateKegiatanRejectionStatus(int $kegiatanId, string $alasanPenolakan = ''): bool
    {
        $statusDitolak = 4;
        $currentPosisi = 2; // Verifikator
        $userId = isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])
            ? (int) $_SESSION['user_id']
            : null;
        $roleId = 2; // Role ID for Verifikator

        $connection = $this->db;
        $transactionStarted = false;

        try {
            $connection->begin_transaction();
            $transactionStarted = true;

            // Update kegiatan status
            $query = "UPDATE tbl_kegiatan
                      SET statusUtamaId = ?
                      WHERE kegiatanId = ?";

            $stmt = $connection->prepare($query);
            if ($stmt === false) {
                throw new RuntimeException('Gagal menyiapkan statement update kegiatan.');
            }
            $stmt->bind_param("ii", $statusDitolak, $kegiatanId);

            if (!$stmt->execute()) {
                throw new RuntimeException('Gagal update kegiatan: ' . $stmt->error);
            }
            $stmt->close();

            // Catat History
            $historyId = $this->insertProgressHistory(
                $kegiatanId,
                $statusDitolak,
                $currentPosisi,
                $currentPosisi, // Tetap di posisi Verifikator karena ditolak
                $userId,
                'verifikator_reject'
            );

            if ($historyId <= 0) {
                throw new RuntimeException('Gagal mencatat riwayat.');
            }

            // Insert rejection comment (targetKolom = NULL untuk alasan umum penolakan)
            if (!empty($alasanPenolakan)) {
                $insertResult = $this->insertRevisiComment(
                    $historyId,
                    $userId,
                    $roleId,
                    $alasanPenolakan,
                    'tbl_kegiatan',
                    null // NULL karena ini alasan penolakan umum, bukan spesifik ke kolom
                );
                
                if (!$insertResult) {
                    error_log("REJECT - Failed to insert rejection comment for kegiatan $kegiatanId");
                } else {
                    error_log("REJECT - Success insert comment for kegiatan $kegiatanId, historyId $historyId");
                }
            } else {
                error_log("REJECT - No rejection reason provided for kegiatan $kegiatanId");
            }

            $connection->commit();
            return true;
        } catch (Throwable $e) {
            if ($transactionStarted) {
                $connection->rollback();
            }
            error_log('rejectUsulan Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengambil data proposal untuk monitoring.
     */
    public function getProposalMonitoring()
    {
        $query = "SELECT
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.jurusanPenyelenggara as jurusan,
                    s.namaStatusUsulan as status,
                    r.namaRole as tahap_sekarang
                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                LEFT JOIN tbl_role r ON k.posisiId = r.roleId
                ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                if (isset($row['status'])) {
                    $row['status'] = ucfirst(strtolower($row['status']));
                } else {
                    $row['status'] = 'Menunggu';
                }
                if (!isset($row['tahap_sekarang']) || empty($row['tahap_sekarang'])) {
                    $row['tahap_sekarang'] = 'Admin';
                }
                $data[] = $row;
            }
        }
        return $data;
    }
}
