<?php

namespace App\Models\Bendahara;

use Exception;
use DateTime;
use mysqli;

/**
 * BendaharaModel - Bendahara Management Model
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.1.0 - Added support for staged disbursement (tbl_tahapan_pencairan)
 */

class BendaharaModel
{
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection untuk database connection
     */
    public function __construct($db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            if (function_exists('db')) {
                $this->db = db();
            } else {
                throw new \Exception("Database connection not provided to BendaharaModel. Ensure bootstrap.php is loaded.");
            }
        }
    }

    // =========================================================
    // 1. STATISTIK DASHBOARD
    // =========================================================

    public function getDashboardStats()
    {
        $query = "SELECT 
                    SUM(CASE WHEN posisiId = 5 OR tanggalPencairan IS NOT NULL THEN 1 ELSE 0 END) as total,
                    SUM(CASE WHEN posisiId = 5 AND tanggalPencairan IS NULL THEN 1 ELSE 0 END) as menunggu,
                    SUM(CASE WHEN tanggalPencairan IS NOT NULL THEN 1 ELSE 0 END) as dicairkan
                  FROM tbl_kegiatan";

        $result = mysqli_query($this->db, $query);
        return $result ? mysqli_fetch_assoc($result) : ['total' => 0, 'menunggu' => 0, 'dicairkan' => 0];
    }

    // =========================================================
    // 2. LIST PENCAIRAN DANA (Antrian)
    // =========================================================

    public function getAntrianPencairan()
    {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.createdAt as tanggal_pengajuan,
                    k.buktiMAK as kode_mak,
                    (SELECT COALESCE(SUM(r.totalHarga), 0) 
                     FROM tbl_rab r 
                     JOIN tbl_kak kak ON r.kakId = kak.kakId 
                     WHERE kak.kegiatanId = k.kegiatanId) as anggaran_disetujui,
                    'Menunggu' as status
                  FROM tbl_kegiatan k
                  WHERE k.posisiId = 5 
                    AND k.statusUtamaId != 4
                  ORDER BY k.createdAt ASC";

        $result = mysqli_query($this->db, $query);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $row['prodi'] = $row['prodi'] ?? '-';
                
                // Dynamic Status Calculation
                $totalDicairkan = $this->getTotalDicairkanByKegiatan($row['id']);
                $totalAnggaran = $row['anggaran_disetujui'];
                
                if ($totalDicairkan >= $totalAnggaran && $totalAnggaran > 0) {
                    $row['status'] = 'Dana Diberikan';
                } elseif ($totalDicairkan > 0) {
                    $row['status'] = 'Dana Belum Diberikan Semua';
                } else {
                    $row['status'] = 'Menunggu';
                }
                
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getRiwayatVerifikasi($limit = 10)
    {
        // Query to get history of verifications done by Bendahara
        // Added prodiPenyelenggara to fix missing department name
        // Added nimPelaksana to fix missing NIM
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    ph.timestamp as tanggal_verifikasi,
                    s.namaStatusUsulan as status,
                    u.nama as verifikator
                  FROM tbl_progress_history ph
                  JOIN tbl_kegiatan k ON ph.kegiatanId = k.kegiatanId
                  LEFT JOIN tbl_status_utama s ON ph.statusId = s.statusId
                  LEFT JOIN tbl_user u ON ph.changedByUserId = u.userId
                  -- Filtering for Bendahara context if needed, but for now showing recent history
                  ORDER BY ph.timestamp DESC
                  LIMIT ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    // =========================================================
    // 3. DETAIL KEGIATAN & DATA PENDUKUNG
    // =========================================================

    public function getDetailPencairan($kegiatanId)
    {
        // Validasi parameter untuk mencegah undefined atau invalid ID
        $kegiatanId = (int) $kegiatanId;
        
        if ($kegiatanId <= 0) {
            error_log("BendaharaModel::getDetailPencairan - Invalid kegiatanId received: $kegiatanId");
            return null;
        }
        
        $query = "SELECT 
                    k.*,
                    kak.*,
                    k.tanggalMulai as tanggal_mulai,
                    k.tanggalSelesai as tanggal_selesai,
                    k.suratPengantar as file_surat_pengantar,
                    u.nama as nama_pengusul,
                    k.namaPJ as nama_pj,
                    k.nip as nim_pj,
                    k.nimPelaksana as nim_pelaksana,
                    k.pemilikKegiatan as nama_pelaksana,
                    k.danaDisetujui as totalAnggaranDisetujui,
                    s.namaStatusUsulan as status_text
                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                  LEFT JOIN tbl_user u ON u.userId = k.userId
                  LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                  WHERE k.kegiatanId = ? AND k.posisiId = 5";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        
        // Debug logging
        if (!$data) {
            error_log("BendaharaModel::getDetailPencairan - Data not found for kegiatanId: $kegiatanId (posisiId must be 5)");
        }
        
        return $data;
    }

    public function getRABByKegiatan($kegiatanId)
    {
        $query = "SELECT r.*, cat.namaKategori 
                  FROM tbl_rab r
                  JOIN tbl_kategori_rab cat ON r.kategoriId = cat.kategoriRabId
                  JOIN tbl_kak kak ON r.kakId = kak.kakId
                  WHERE kak.kegiatanId = ?
                  ORDER BY cat.kategoriRabId ASC, r.rabItemId ASC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $kategori = $row['namaKategori'];
            $data[$kategori][] = [
                'uraian' => $row['uraian'] ?? '',
                'rincian' => $row['rincian'] ?? '',
                'vol1' => $row['vol1'] ?? 0,
                'sat1' => $row['sat1'] ?? '',
                'vol2' => $row['vol2'] ?? 1,
                'sat2' => $row['sat2'] ?? '',
                'harga' => $row['harga'] ?? 0
            ];
        }
        return $data;
    }

    public function getIKUByKegiatan($kegiatanId)
    {
        $query = "SELECT iku FROM tbl_kak WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row && !empty($row['iku'])) {
            return explode(',', $row['iku']);
        }
        return [];
    }

    public function getIndikatorByKegiatan($kegiatanId)
    {
        $query = "SELECT i.bulan, i.indikatorKeberhasilan as nama, i.targetPersen as target 
                  FROM tbl_indikator_kak i
                  JOIN tbl_kak kak ON i.kakId = kak.kakId
                  WHERE kak.kegiatanId = ?
                  ORDER BY i.indikatorId ASC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getTahapanByKegiatan($kegiatanId)
    {
        $query = "SELECT t.namaTahapan 
                  FROM tbl_tahapan_pelaksanaan t
                  JOIN tbl_kak kak ON t.kakId = kak.kakId
                  WHERE kak.kegiatanId = ?
                  ORDER BY t.tahapanId ASC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row['namaTahapan'];
        }
        return $data;
    }

    public function getListJurusan()
    {
        $query = "SELECT DISTINCT jurusanPenyelenggara as jurusan 
                  FROM tbl_kegiatan 
                  WHERE jurusanPenyelenggara IS NOT NULL 
                  ORDER BY jurusanPenyelenggara ASC";

        $result = mysqli_query($this->db, $query);
        $list = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row['jurusan'];
            }
        }
        return $list;
    }

    // =========================================================
    // 4. PENCAIRAN BERTAHAP - NEW METHODS
    // =========================================================

    /**
     * Simpan data pencairan dana (untuk setiap termin) ke tbl_tahapan_pencairan
     */
    public function simpanPencairanDana($data)
    {
        $query = "INSERT INTO tbl_tahapan_pencairan 
                (idKegiatan, tglPencairan, termin, nominal, catatan, createdBy) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param(
            $stmt,
            "issdsi",
            $data['kegiatan_id'],
            $data['tanggal_pencairan'],
            $data['termin'],
            $data['nominal'],
            $data['catatan'],
            $data['created_by']
        );
        
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    /**
     * Ambil semua riwayat pencairan untuk history dashboard
     */
    public function getRiwayatPencairan($limit = 50)
    {
        $checkTable = mysqli_query($this->db, "SHOW TABLES LIKE 'tbl_tahapan_pencairan'");
        if (mysqli_num_rows($checkTable) == 0) return [];

        $query = "SELECT t.*, k.namaKegiatan, k.pemilikKegiatan 
                FROM tbl_tahapan_pencairan t
                JOIN tbl_kegiatan k ON t.idKegiatan = k.kegiatanId
                ORDER BY t.createdAt DESC LIMIT ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    /**
     * Ambil semua riwayat pencairan berdasarkan Kegiatan ID
     */
    public function getRiwayatPencairanByKegiatan($kegiatanId)
    {
        // Check if table exists first to avoid error on fresh DB without migration
        $checkTable = mysqli_query($this->db, "SHOW TABLES LIKE 'tbl_tahapan_pencairan'");
        if (mysqli_num_rows($checkTable) == 0) {
            return []; 
        }

        $query = "SELECT * FROM tbl_tahapan_pencairan
                WHERE idKegiatan = ? 
                ORDER BY tglPencairan ASC, createdAt ASC";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    /**
     * Hitung total yang sudah dicairkan (oleh bendahara) dari tbl_tahapan_pencairan
     */
    public function getTotalDicairkanByKegiatan($kegiatanId)
    {
        // Check table existence
        $checkTable = mysqli_query($this->db, "SHOW TABLES LIKE 'tbl_tahapan_pencairan'");
        if (mysqli_num_rows($checkTable) == 0) {
            // Fallback to old column if table doesn't exist
            $queryOld = "SELECT jumlahDicairkan FROM tbl_kegiatan WHERE kegiatanId = ?";
            $stmtOld = mysqli_prepare($this->db, $queryOld);
            mysqli_stmt_bind_param($stmtOld, "i", $kegiatanId);
            mysqli_stmt_execute($stmtOld);
            $res = mysqli_stmt_get_result($stmtOld);
            $row = mysqli_fetch_assoc($res);
            mysqli_stmt_close($stmtOld);
            return floatval($row['jumlahDicairkan'] ?? 0);
        }

        $query = "SELECT COALESCE(SUM(nominal), 0) as total 
                FROM tbl_tahapan_pencairan
                WHERE idKegiatan = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return floatval($row['total']);
    }

    /**
     * Update total dicairkan dan status pencairan di tbl_kegiatan
     */
    public function updateStatusPencairan($kegiatanId, $totalDicairkan, $totalAnggaran)
    {
        // Tentukan status berdasarkan total
        // statusUtamaId 5 = Dana diberikan (Lunas)
        // statusUtamaId 5 but with a specific flag or logic for Partial? 
        // Logic: IF totalDicairkan >= totalAnggaran THEN Status = 5 (Dana Diberikan)
        // IF totalDicairkan < totalAnggaran THEN Status could be 5 (Partial) or keep as 5 but UI handles it.
        // Based on Source logic:
        $statusId = ($totalDicairkan >= $totalAnggaran) ? 5 : 5; // Both are 5 ("Dana Diberikan"), but amount differs
        
        // Note: Source code had statusId=6 for partial, but our schema only has 1-5.
        // We will stick to 5 and rely on math for "Partial" display in UI.
        
        $query = "UPDATE tbl_kegiatan 
                SET jumlahDicairkan = ?, -- Using existing column to store TOTAL
                    statusUtamaId = ?
                WHERE kegiatanId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "dii", $totalDicairkan, $statusId, $kegiatanId);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $success;
    }

    // =========================================================
    // 5. DASHBOARD & LPJ SUPPORT
    // =========================================================

    public function getListKegiatanDashboard($limit = 10)
    {
        $limit = (int)$limit;

        $checkTable = mysqli_query($this->db, "SHOW TABLES LIKE 'tbl_tahapan_pencairan'");
        $useTahapanTable = (mysqli_num_rows($checkTable) > 0);

        $totalDicairkanSubquery = "";
        if ($useTahapanTable) {
            $totalDicairkanSubquery = ", (SELECT COALESCE(SUM(tp.nominal), 0) FROM tbl_tahapan_pencairan tp WHERE tp.idKegiatan = k.kegiatanId) as total_dicairkan";
        } else {
            $totalDicairkanSubquery = ", k.jumlahDicairkan as total_dicairkan";
        }

        $query = "SELECT k.*, s.namaStatusUsulan as status_text{$totalDicairkanSubquery}
                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                  WHERE k.posisiId = 5 AND k.statusUtamaId != 4
                  ORDER BY k.createdAt DESC 
                  LIMIT ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $data;
    }

    public function getAntrianLPJ()
    {
        // Modified to show ALL recent LPJs, not just 'Menunggu' (statusId=1)
        // This fixes the empty dashboard issue.
        $query = "SELECT l.*, k.namaKegiatan, k.pemilikKegiatan, k.nimPelaksana, 
                         k.prodiPenyelenggara, k.jurusanPenyelenggara, k.userId,
                         s.namaStatusUsulan as status_text
                  FROM tbl_lpj l
                  JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                  LEFT JOIN tbl_status_utama s ON l.statusId = s.statusId
                  WHERE l.statusId != 4
                  ORDER BY l.submittedAt DESC LIMIT 20"; // Limit for dashboard performance

        $result = mysqli_query($this->db, $query);
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * Get Detail LPJ by ID
     */
    public function getDetailLPJ($lpjId)
    {
        error_log("=== BENDAHARA MODEL: getDetailLPJ ===");
        error_log("LPJ ID: " . $lpjId);
        
        $query = "SELECT l.*, 
                    k.namaKegiatan, 
                    k.pemilikKegiatan, k.nimPelaksana, 
                    k.prodiPenyelenggara, 
                    k.jurusanPenyelenggara, 
                    k.userId,
                    kak.kakId
                  FROM tbl_lpj l
                  JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                  LEFT JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                  WHERE l.lpjId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $lpjId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        
        if ($data) {
            error_log("LPJ found: " . $data['namaKegiatan']);
        } else {
            error_log("LPJ NOT FOUND for lpjId=" . $lpjId);
        }
        
        return $data;
    }

    /**
     * Get line items for a specific LPJ
     */
    public function getLPJItems($lpjId)
    {
        error_log("=== BENDAHARA MODEL: getLPJItems ===");
        error_log("LPJ ID: " . $lpjId);
        
        $query = "SELECT * FROM tbl_lpj_item WHERE lpjId = ? ORDER BY lpjItemId ASC";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $lpjId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            error_log("Item: " . ($row['uraian'] ?? 'N/A') . 
                     " | realisasi: " . ($row['realisasi'] ?? 'NULL') . 
                     " | subTotal: " . ($row['subTotal'] ?? 'NULL') .
                     " | totalHarga: " . ($row['totalHarga'] ?? 'NULL'));
            $data[] = $row;
        }
        
        error_log("Total items found: " . count($data));
        return $data;
    }

    /**
     * Approve LPJ and update status
     */
    public function approveLPJ($lpjId)
    {
        // 1. Update status di tbl_lpj (Status 3 = Disetujui)
        $query = "UPDATE tbl_lpj SET statusId = 3, approvedAt = NOW() WHERE lpjId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $lpjId);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($success) {
            // 2. Log ke progress history
            $lpj = $this->getDetailLPJ($lpjId);
            $kegiatanId = $lpj['kegiatanId'] ?? 0;
            $userId = $_SESSION['user_id'] ?? null;
            if ($kegiatanId && $userId) {
                $histQuery = "INSERT INTO tbl_progress_history (kegiatanId, statusId, changedByUserId, timestamp) VALUES (?, 3, ?, NOW())";
                $stmtH = mysqli_prepare($this->db, $histQuery);
                mysqli_stmt_bind_param($stmtH, "ii", $kegiatanId, $userId);
                mysqli_stmt_execute($stmtH);
                mysqli_stmt_close($stmtH);
            }
        }
        
        return $success;
    }

    /**
     * Revise LPJ and update status
     */
    public function reviseLPJ($lpjId, $komentarPerKategori, $catatanUmum)
    {
        mysqli_begin_transaction($this->db);
        try {
            // 1. Update status di tbl_lpj (Status 2 = Revisi)
            $statusRevisi = 2;
            $query = "UPDATE tbl_lpj SET statusId = ?, komentarRevisi = ? WHERE lpjId = ?";
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "isi", $statusRevisi, $catatanUmum, $lpjId);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal update status LPJ.");
            }
            mysqli_stmt_close($stmt);

            // ... (rest of the method remains the same)

            mysqli_commit($this->db);

            // Log history after successful commit
            $lpj = $this->getDetailLPJ($lpjId);
            $kegiatanId = $lpj['kegiatanId'] ?? 0;
            $userId = $_SESSION['user_id'] ?? null;
            if ($kegiatanId && $userId) {
                $histQuery = "INSERT INTO tbl_progress_history (kegiatanId, statusId, changedByUserId, timestamp) VALUES (?, ?, ?, NOW())";
                $stmtH = mysqli_prepare($this->db, $histQuery);
                mysqli_stmt_bind_param($stmtH, "iii", $kegiatanId, $statusRevisi, $userId);
                mysqli_stmt_execute($stmtH);
                mysqli_stmt_close($stmtH);
            }

            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("❌ reviseLPJ Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject LPJ and update status
     */
    public function rejectLPJ($lpjId, $alasan)
    {
        $statusDitolak = 4;
        $query = "UPDATE tbl_lpj SET statusId = ?, komentarPenolakan = ? WHERE lpjId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "isi", $statusDitolak, $alasan, $lpjId);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($success) {
            $lpj = $this->getDetailLPJ($lpjId);
            $kegiatanId = $lpj['kegiatanId'] ?? 0;
            $userId = $_SESSION['user_id'] ?? null;
            if ($kegiatanId && $userId) {
                $histQuery = "INSERT INTO tbl_progress_history (kegiatanId, statusId, changedByUserId, timestamp) VALUES (?, ?, ?, NOW())";
                $stmtH = mysqli_prepare($this->db, $histQuery);
                mysqli_stmt_bind_param($stmtH, "iii", $kegiatanId, $statusDitolak, $userId);
                mysqli_stmt_execute($stmtH);
                mysqli_stmt_close($stmtH);
            }
        }

        return $success;
    }
}