<?php

namespace App\Models\Lpj;

use Exception;
use mysqli;

// Ensure mysqli is available if not globally imported

/**
 * LpjModel - LPJ (Laporan Pertanggungjawaban) Management Model
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Converted from procedural to class-based with DI
 */

class LpjModel
{
    private $db;

    public function __construct($db = null)
    {
        if ($db !== null) {
            $this->db = $db;
        } else {
            // Fallback to global db() helper function from bootstrap.php
            if (function_exists('db')) {
                $this->db = db();
            } else {
                throw new \Exception("Database connection not provided to LpjModel. Ensure bootstrap.php is loaded.");
            }
        }
    }

    /**
     * Membuat record LPJ baru sebagai DRAFT
     * statusId = 1 (default Menunggu) tapi submittedAt = NULL menandakan Draft
     * Logic: submittedAt IS NULL → Draft, submittedAt IS NOT NULL → Submitted
     */
    public function insertLpj($kegiatan_id)
    {
        $grand_total_default = 0.00;
        // statusId = 1 (Menunggu) sesuai DEFAULT schema
        // submittedAt = NULL → Status Draft (belum disubmit)
        // Saat submit nanti, submittedAt akan diisi NOW() untuk menandakan sudah di-submit
        $query = "INSERT INTO tbl_lpj (kegiatanId, grandTotalRealisasi, statusId, submittedAt, approvedAt) 
                  VALUES (?, ?, 1, NULL, NULL)";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::insertLpj - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'id', $kegiatan_id, $grand_total_default);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($this->db);            
            error_log("✅ LPJ Draft created: lpjId={$newId}, kegiatanId={$kegiatan_id}, statusId=1, submittedAt=NULL (Draft)");
            mysqli_stmt_close($stmt);
            return $newId;
        } else {
            error_log('LpjModel::insertLpj - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengupdate grandTotalRealisasi di tbl_lpj
     */
    public function updateLpjGrandTotal($lpj_id)
    {
        $query = "UPDATE tbl_lpj SET grandTotalRealisasi = 
                    (SELECT COALESCE(SUM(realisasi), 120) FROM tbl_lpj_item WHERE lpjId = ?)
                  WHERE lpjId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::updateLpjGrandTotal - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ii', $lpj_id, $lpj_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::updateLpjGrandTotal - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengupdate status LPJ (submittedAt atau approvedAt)
     */
    public function updateLpjStatus($lpj_id, $new_status)
    {
        $statusId = null;
        $timestampColumn = null;

        switch ($new_status) {
            case 'Submitted':
                $statusId = 1; // Menunggu (LPJ submitted, awaiting review)
                $timestampColumn = 'submittedAt';
                break;
            case 'Revised':
                $statusId = 2; // Revisi (LPJ sent back for revision)
                break;
            case 'Approved':
                $statusId = 3; // Disetujui (LPJ approved)
                $timestampColumn = 'approvedAt';
                break;
            case 'Rejected':
                $statusId = 4; // Ditolak (LPJ rejected)
                break;
            default:
                error_log('LpjModel::updateLpjStatus - Invalid status provided: ' . $new_status);
                return false;
        }

        $query = "UPDATE tbl_lpj SET statusId = ?";
        $bind_params = 'ii';
        $bind_values = [$statusId, $lpj_id];

        if ($timestampColumn) {
            $query .= ", {$timestampColumn} = NOW()";
        }
        $query .= " WHERE lpjId = ?";

        error_log("🔍 DEBUG updateLpjStatus: lpjId={$lpj_id}, status={$new_status}, statusId={$statusId}, query={$query}");

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('❌ LpjModel::updateLpjStatus - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, $bind_params, ...$bind_values);

        if (mysqli_stmt_execute($stmt)) {
            $affected = mysqli_stmt_affected_rows($stmt);
            error_log("✅ LpjModel::updateLpjStatus - Success: lpjId={$lpj_id}, statusId={$statusId}, {$timestampColumn}=NOW(), affected={$affected}");
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('❌ LpjModel::updateLpjStatus - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Memperbarui atau membuat file bukti untuk item LPJ tertentu (UPSERT).
     * 
     * Jika record belum ada di tbl_lpj_item, akan create record baru dari data RAB.
     * Jika sudah ada, akan update fileBukti saja.
     *
     * @param int $lpjItemId  ID dari RAB item (rabItemId)
     * @param string $filename  Nama file yang diupload
     * @param int|null $lpjId  ID LPJ (optional untuk UPSERT)
     * @param array $rabData  Data dari RAB untuk create record baru
     * @return bool True jika berhasil, false jika gagal
     */
    public function updateFileBukti(int $lpjItemId, string $filename, int $lpjId = null, array $rabData = []): bool
    {
        if ($lpjId) {
            // Check if record exists
            $checkQuery = "SELECT lpjItemId FROM tbl_lpj_item WHERE lpjItemId = ? AND lpjId = ?";
            $checkStmt = mysqli_prepare($this->db, $checkQuery);
            mysqli_stmt_bind_param($checkStmt, "ii", $lpjItemId, $lpjId);
            mysqli_stmt_execute($checkStmt);
            $result = mysqli_stmt_get_result($checkStmt);
            $exists = mysqli_fetch_assoc($result);
            mysqli_stmt_close($checkStmt);
            
            if ($exists) {
                // UPDATE existing record
                $query = "UPDATE tbl_lpj_item SET fileBukti = ? WHERE lpjItemId = ? AND lpjId = ?";
                $stmt = mysqli_prepare($this->db, $query);
                if (!$stmt) {
                    error_log('LpjModel::updateFileBukti - UPDATE Prepare failed: ' . mysqli_error($this->db));
                    return false;
                }
                mysqli_stmt_bind_param($stmt, "sii", $filename, $lpjItemId, $lpjId);
            } else {
                // INSERT new record
                $jenisBelanja = $rabData['kategori'] ?? 'Lainnya';
                $uraian = $rabData['uraian'] ?? '';
                $rincian = $rabData['rincian'] ?? '';
                $totalHarga = $rabData['harga_plan'] ?? 0;
                
                $query = "INSERT INTO tbl_lpj_item (lpjItemId, lpjId, jenisBelanja, uraian, rincian, totalHarga, fileBukti, createdAt) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = mysqli_prepare($this->db, $query);
                if (!$stmt) {
                    error_log('LpjModel::updateFileBukti - INSERT Prepare failed: ' . mysqli_error($this->db));
                    return false;
                }
                mysqli_stmt_bind_param($stmt, "iisssds", $lpjItemId, $lpjId, $jenisBelanja, $uraian, $rincian, $totalHarga, $filename);
            }
        } else {
            // Fallback: Simple UPDATE (backward compatibility)
            $query = "UPDATE tbl_lpj_item SET fileBukti = ? WHERE lpjItemId = ?";
            $stmt = mysqli_prepare($this->db, $query);
            if (!$stmt) {
                error_log('LpjModel::updateFileBukti - Fallback Prepare failed: ' . mysqli_error($this->db));
                return false;
            }
            mysqli_stmt_bind_param($stmt, "si", $filename, $lpjItemId);
        }

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::updateFileBukti - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
 * ✅ FIXED: Insert atau Update LPJ Item HANYA untuk kolom fileBukti
 * Tidak menyentuh kolom lain - realisasi akan diisi saat submit LPJ
 * 
 * @param int $lpjId
 * @param int $rabItemId
 * @param string $filename Nama file bukti yang diupload
 * @param array $itemData Data item dari RAB (uraian, rincian, dll)
 * @return bool
 */
public function upsertLpjItemBukti(int $lpjId, int $rabItemId, string $filename, array $itemData): bool
{
    mysqli_begin_transaction($this->db);
    try {
        // Cek apakah item sudah ada di tbl_lpj_item berdasarkan rabItemId
        $checkQuery = "SELECT lpjItemId, fileBukti FROM tbl_lpj_item 
                       WHERE lpjId = ? AND rabItemId = ? FOR UPDATE";
        
        $stmt = mysqli_prepare($this->db, $checkQuery);
        mysqli_stmt_bind_param($stmt, "ii", $lpjId, $rabItemId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existing = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($existing) {
            // ✅ UPDATE: Item sudah ada, HANYA update fileBukti (preserve realisasi)
            $updateQuery = "UPDATE tbl_lpj_item 
                           SET fileBukti = ?
                           WHERE lpjItemId = ?";
            
            $stmt = mysqli_prepare($this->db, $updateQuery);
            mysqli_stmt_bind_param($stmt, "si", $filename, $existing['lpjItemId']);
            $result = mysqli_stmt_execute($stmt);
            
            error_log("✅ UPDATE lpjItemId={$existing['lpjItemId']}: fileBukti={$filename} (realisasi preserved)");
            
            mysqli_stmt_close($stmt);
            
        } else {
            // ✅ INSERT: Item belum ada, buat record baru dengan realisasi = 0 (akan diisi saat submit)
            $insertQuery = "INSERT INTO tbl_lpj_item 
                            (lpjId, rabItemId, kategoriId, uraian, rincian,
                             sat1, sat2, vol1, vol2, harga, totalHarga,
                             fileBukti, createdAt) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($this->db, $insertQuery);
            
            $kategoriId = $itemData['kategoriId'] ?? 0;
            $uraian = $itemData['uraian'] ?? '';
            $rincian = $itemData['rincian'] ?? '';
            $sat1 = $itemData['sat1'] ?? '';
            $sat2 = $itemData['sat2'] ?? '';
            $vol1 = $itemData['vol1'] ?? 0;
            $vol2 = $itemData['vol2'] ?? 0;
            $hargaSatuan = $itemData['hargaSatuan'] ?? $itemData['harga_satuan'] ?? 0;
            $totalRencana = $itemData['totalRencana'] ?? $itemData['total_harga'] ?? 0;
            
            mysqli_stmt_bind_param(
                $stmt,
                "iiissssdddds",
                $lpjId,
                $rabItemId,
                $kategoriId,
                $uraian,
                $rincian,
                $sat1,
                $sat2,
                $vol1,
                $vol2,
                $hargaSatuan,
                $totalRencana,
                $filename
            );
            
            $result = mysqli_stmt_execute($stmt);
            
            if ($result) {
                $newLpjItemId = mysqli_insert_id($this->db);
                error_log("✅ INSERT lpjItemId={$newLpjItemId}: rabItemId={$rabItemId}, realisasi=0 (default), fileBukti={$filename}");
            } else {
                throw new Exception("INSERT FAILED for rabItemId={$rabItemId}: " . mysqli_error($this->db));
            }
            
            mysqli_stmt_close($stmt);
        }
        
        mysqli_commit($this->db);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($this->db);
        error_log('LpjModel::upsertLpjItemBukti - Transaction failed: ' . $e->getMessage());
        return false;
    }
}

        /**
     * ✅ BARU: Ambil data RAB item untuk digunakan saat upload
     * 
     * @param int $rabItemId
     * @return array|null
     */
    public function getRABItemById(int $rabItemId): ?array
    {
        $query = "SELECT 
                    r.rabItemId,
                    r.kategoriId,
                    r.uraian,
                    r.rincian,
                    r.vol1,
                    r.sat1,
                    r.vol2,
                    r.sat2,
                    r.harga as hargaSatuan,
                    r.totalHarga as totalRencana,
                    cat.namaKategori
                  FROM tbl_rab r
                  JOIN tbl_kategori_rab cat ON r.kategoriId = cat.kategoriRabId
                  WHERE r.rabItemId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $rabItemId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return $data;
    }

    /**
     * ✅ BARU: Hitung jumlah bukti yang sudah diupload untuk LPJ tertentu
     * 
     * @param int $lpjId
     * @return array ['total' => int, 'uploaded' => int]
     */
    public function countUploadedBukti(int $lpjId): array
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN fileBukti IS NOT NULL AND fileBukti != '' THEN 1 ELSE 0 END) as uploaded
                  FROM tbl_lpj_item
                  WHERE lpjId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $lpjId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return [
            'total' => (int)($data['total'] ?? 0),
            'uploaded' => (int)($data['uploaded'] ?? 0)
        ];
    }

    /**
     * ✅ Alias method untuk compatibility
     * @param int $lpjId
     * @return array ['total' => int, 'uploaded' => int]
     */
    public function getUploadBuktiStatus(int $lpjId): array
    {
        return $this->countUploadedBukti($lpjId);
    }

    /**
     * Update status LPJ menjadi 'Menunggu' (submitted)
     * 
     * @param int $lpjId
     * @return bool
     */
    public function markLpjAsSubmitted(int $lpjId): bool
    {
        $query = "UPDATE tbl_lpj 
                  SET statusId = 1, submittedAt = NOW()
                  WHERE lpjId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $lpjId);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    /**
     * Memasukkan item-item LPJ secara batch
     * 
     * @param int $lpjId
     * @param array $items
     * @return bool
     */
    public function insertLpjItems(int $lpjId, array $items): bool
    {
        mysqli_begin_transaction($this->db);
        try {
            $query = "INSERT INTO tbl_lpj_item 
                      (lpjId, jenisBelanja, uraian, rincian, sat1, sat2, vol1, vol2, harga, totalHarga, realisasi, fileBukti, createdAt) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($this->db, $query);
            if (!$stmt) {
                throw new Exception("LpjModel::insertLpjItems - Prepare failed: " . mysqli_error($this->db));
            }

            foreach ($items as $item) {
                $jenisBelanja = $item['jenis_belanja'] ?? '';
                $uraian = $item['uraian'] ?? '';
                $rincian = $item['rincian'] ?? '';
                $sat1 = $item['sat1'] ?? $item['satuan'] ?? '';
                $sat2 = $item['sat2'] ?? '';
                $vol1 = floatval($item['vol1'] ?? 1);
                $vol2 = floatval($item['vol2'] ?? 1);
                $harga = floatval($item['harga'] ?? $item['total_harga'] ?? 0);
                $totalHarga = floatval($item['total_harga'] ?? $item['sub_total'] ?? 0);
                $realisasi = floatval($item['realisasi'] ?? $totalHarga);
                $fileBukti = $item['file_bukti'] ?? $item['file_bukti_nota'] ?? null;

                mysqli_stmt_bind_param(
                    $stmt,
                    "isssssddddds",
                    $lpjId,
                    $jenisBelanja,
                    $uraian,
                    $rincian,
                    $sat1,
                    $sat2,
                    $vol1,
                    $vol2,
                    $harga,
                    $totalHarga,
                    $realisasi,
                    $fileBukti
                );

                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("LpjModel::insertLpjItems - Execute failed: " . mysqli_stmt_error($stmt));
                }
            }

            mysqli_stmt_close($stmt);
            mysqli_commit($this->db);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Menghapus semua item LPJ berdasarkan lpjId
     */
    public function deleteLpjItemsByLpjId($lpj_id)
    {
        $query = "DELETE FROM tbl_lpj_item WHERE lpjId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('LpjModel::deleteLpjItemsByLpjId - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $lpj_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::deleteLpjItemsByLpjId - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengambil jumlah dana yang sudah dicairkan untuk suatu kegiatan.
     * 
     * @param int $kegiatanId
     * @return float
     */
    public function getDisbursedAmount(int $kegiatanId): float
    {
        $query = "SELECT danaDiSetujui as total FROM tbl_kegiatan WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if (!$stmt) return 0.0;
        
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return (float)($row['total'] ?? 0.0);
    }

    /**
     * Mengambil satu data LPJ lengkap dengan semua item-itemnya
     */
    public function getLpjWithItemsById($lpj_id)
    {
        $query = "SELECT l.*, i.*, kak.kakId 
                  FROM tbl_lpj l
                  LEFT JOIN tbl_lpj_item i ON l.lpjId = i.lpjId
                  LEFT JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                  LEFT JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                  WHERE l.lpjId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::getLpjWithItemsById - Prepare failed: ' . mysqli_error($this->db));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $lpj_id);

        if (!mysqli_stmt_execute($stmt)) {
            error_log('LpjModel::getLpjWithItemsById - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $lpjData = null;

        while ($row = mysqli_fetch_assoc($result)) {
            if ($lpjData === null) {
                $lpjData = [
                    'lpj_id' => $row['lpjId'],
                    'kegiatan_id' => $row['kegiatanId'],
                    'kakId' => $row['kakId'], // ✅ FIXED: Tambahkan kakId dari tbl_kegiatan
                    'grand_total_realisasi' => $row['grandTotalRealisasi'],
                    'status_id' => $row['statusId'],
                    'submitted_at' => $row['submittedAt'],
                    'approved_at' => $row['approvedAt'],
                    'tenggat_lpj' => $row['tenggatLpj'],
                    'items' => []
                ];
            }

            if (!empty($row['lpjItemId'])) {
                $lpjData['items'][] = [
                    'lpj_item_id' => $row['lpjItemId'],
                    'kategori_id' => $row['kategoriId'] ?? null,
                    'jenis_belanja' => $row['jenisBelanja'] ?? null,
                    'uraian' => $row['uraian'],
                    'rincian' => $row['rincian'],
                    'sat1' => $row['sat1'] ?? null,
                    'sat2' => $row['sat2'] ?? null,
                    'vol1' => $row['vol1'] ?? null,
                    'vol2' => $row['vol2'] ?? null,
                    'harga' => $row['harga'] ?? null,
                    'total_rencana' => $row['totalHarga'],
                    'realisasi' => $row['realisasi'] ?? $row['subTotal'] ?? 0,
                    'file_bukti' => $row['fileBukti'],
                    'komentar' => $row['komentar'] ?? null
                ];
            }
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);

        return $lpjData;
    }

    /**
     * Mengambil data LPJ (dengan item) berdasarkan ID Kegiatan
     */
    public function getLpjWithItemsByKegiatanId($kegiatan_id)
    {
        $query_lpj_id = "SELECT lpjId FROM tbl_lpj WHERE kegiatanId = ? LIMIT 1";
        $stmt_find = mysqli_prepare($this->db, $query_lpj_id);

        if ($stmt_find === false) {
             error_log('LpjModel::getLpjWithItemsByKegiatanId - Prepare failed: ' . mysqli_error($this->db));
             return null;
        }

        mysqli_stmt_bind_param($stmt_find, 'i', $kegiatan_id);

        if (mysqli_stmt_execute($stmt_find)) {
            $result = mysqli_stmt_get_result($stmt_find);
            $lpj = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt_find);

            if ($lpj && !empty($lpj['lpjId'])) {
                return $this->getLpjWithItemsById($lpj['lpjId']);
            } else {
                return null;
            }
        } else {
            error_log('LpjModel::getLpjWithItemsByKegiatanId - Execute failed: ' . mysqli_stmt_error($stmt_find));
            mysqli_stmt_close($stmt_find);
            return null;
        }
    }

    /**
     * Menghapus LPJ dan SEMUA item-itemnya secara aman (Transactional Delete)
     */
    public function deleteLpjWithItems($lpj_id)
    {
        mysqli_begin_transaction($this->db);

        try {
            // 1. Hapus semua item
            $stmt1 = mysqli_prepare($this->db, "DELETE FROM tbl_lpj_item WHERE lpjId = ?");
            mysqli_stmt_bind_param($stmt1, 'i', $lpj_id);
            if (!mysqli_stmt_execute($stmt1)) {
                throw new Exception(mysqli_stmt_error($stmt1));
            }
            mysqli_stmt_close($stmt1);

            // 2. Hapus LPJ induk
            $stmt2 = mysqli_prepare($this->db, "DELETE FROM tbl_lpj WHERE lpjId = ?");
            mysqli_stmt_bind_param($stmt2, 'i', $lpj_id);
            if (!mysqli_stmt_execute($stmt2)) {
                throw new Exception(mysqli_stmt_error($stmt2));
            }
            mysqli_stmt_close($stmt2);

            mysqli_commit($this->db);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log('LpjModel::deleteLpjWithItems - Transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    public function tolakLpj(int $lpjId, string $komentar): bool
    {
        $query = "UPDATE tbl_lpj SET statusId = 4, komentarPenolakan = ?, submittedAt = NOW() WHERE lpjId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::tolakLpj - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'si', $komentar, $lpjId);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::tolakLpj - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
 * Memperbarui realisasi item LPJ.
 * Digunakan saat submit LPJ dari Admin.
 * 
 * ✅ FIXED: Accept both 'realisasi' and 'total' for backward compatibility
 * 
 * @param int $lpjId
 * @param array $items Array of items with 'id' (rabItemId), 'realisasi'/'total', and other details
 * @return bool
 */
public function updateLpjItemsRealisasi(int $lpjId, array $items): bool
{
    error_log("🔄 updateLpjItemsRealisasi called with lpjId={$lpjId}, items count=" . count($items));
    
    // ✅ SAFETY: Deduplicate items by rabItemId (keep last non-zero or last entry)
    $deduplicatedItems = [];
    foreach ($items as $item) {
        $rabItemId = (int)($item['id'] ?? 0);
        if ($rabItemId > 0) {
            $realisasi = floatval($item['realisasi'] ?? $item['total'] ?? 0);
            
            // Keep this item if: no previous entry OR this has realisasi > 0
            if (!isset($deduplicatedItems[$rabItemId]) || $realisasi > 0) {
                $deduplicatedItems[$rabItemId] = $item;
            }
        }
    }
    
    $items = array_values($deduplicatedItems);
    error_log("✅ After deduplication: " . count($items) . " unique items");
    
    mysqli_begin_transaction($this->db);
    try {
        foreach ($items as $index => $item) {
            $rabItemId = (int)($item['id'] ?? 0);
            
            if ($rabItemId <= 0) {
                error_log("⚠️ Item #{$index}: Invalid rabItemId, skipping");
                continue;
            }
            
            // ✅ FIX: Accept both 'realisasi' and 'total' for backward compatibility
            // DECIMAL(15,2) max: 9999999999999.99, default = 0
            $realisasi = floatval($item['realisasi'] ?? $item['total'] ?? 0);
            
            // ✅ Validate range for DECIMAL(15,2)
            if ($realisasi < 0 || $realisasi > 9999999999999.99) {
                error_log("❌ Item #{$index}: realisasi out of range for DECIMAL(15,2): {$realisasi}");
                throw new Exception("Realisasi untuk item #{$index} melebihi batas maksimal (Rp 9.999.999.999.999,99)");
            }
            
            error_log("📝 Processing Item #{$index}: rabItemId={$rabItemId}, realisasi={$realisasi}");
            
            // Get RAB details for this item
            $rabData = $this->getRABItemById($rabItemId);
            if (!$rabData) {
                error_log("❌ Item #{$index}: RAB data not found for rabItemId={$rabItemId}, skipping");
                continue;
            }

            $kategoriId = $rabData['kategoriId'] ?? 0;
            $uraian = $item['uraian'] ?? $rabData['uraian'] ?? '';
            $rincian = $item['rincian'] ?? $rabData['rincian'] ?? '';
            $sat1 = $rabData['sat1'] ?? '';
            $sat2 = $rabData['sat2'] ?? '';
            $vol1 = floatval($rabData['vol1'] ?? 1);
            $vol2 = floatval($rabData['vol2'] ?? 1);
            $harga = floatval($rabData['hargaSatuan'] ?? 0);
            $totalHarga = floatval($rabData['totalRencana'] ?? 0);
            
            // ✅ Preserve existing fileBukti if not provided
            $fileBukti = !empty($item['file_bukti']) ? $item['file_bukti'] : null;

            // Check if item exists by rabItemId
            $checkQuery = "SELECT lpjItemId, fileBukti, realisasi FROM tbl_lpj_item WHERE lpjId = ? AND rabItemId = ?";
            $stmtCheck = mysqli_prepare($this->db, $checkQuery);
            mysqli_stmt_bind_param($stmtCheck, "ii", $lpjId, $rabItemId);
            mysqli_stmt_execute($stmtCheck);
            $resultCheck = mysqli_stmt_get_result($stmtCheck);
            $existing = mysqli_fetch_assoc($resultCheck);
            mysqli_stmt_close($stmtCheck);

            if ($existing) {
                // ✅ UPDATE existing item - PRESERVE fileBukti if new one not provided
                $finalFileBukti = $fileBukti ?? $existing['fileBukti'];
                
                $updateQuery = "UPDATE tbl_lpj_item SET 
                                uraian = ?,
                                rincian = ?, 
                                sat1 = ?, 
                                sat2 = ?, 
                                vol1 = ?, 
                                vol2 = ?, 
                                harga = ?, 
                                totalHarga = ?, 
                                realisasi = ?, 
                                fileBukti = ?
                                WHERE lpjItemId = ?";
                
                $stmtUpdate = mysqli_prepare($this->db, $updateQuery);
                if (!$stmtUpdate) {
                    throw new Exception("❌ Failed to prepare UPDATE statement: " . mysqli_error($this->db));
                }
                
                mysqli_stmt_bind_param($stmtUpdate, "ssssdddddsi", 
                    $uraian,
                    $rincian, 
                    $sat1, 
                    $sat2, 
                    $vol1, 
                    $vol2, 
                    $harga, 
                    $totalHarga, 
                    $realisasi, 
                    $finalFileBukti, 
                    $existing['lpjItemId']
                );
                
                if (mysqli_stmt_execute($stmtUpdate)) {
                    error_log("✅ Item #{$index} UPDATED: lpjItemId={$existing['lpjItemId']}, realisasi={$realisasi}, fileBukti={$finalFileBukti}");
                } else {
                    throw new Exception("❌ Item #{$index} UPDATE FAILED: " . mysqli_stmt_error($stmtUpdate));
                }
                
                mysqli_stmt_close($stmtUpdate);
                
            } else {
                // ✅ INSERT new item
                $insertQuery = "INSERT INTO tbl_lpj_item 
                                (lpjId, rabItemId, kategoriId, uraian, rincian, sat1, sat2, vol1, vol2, harga, totalHarga, realisasi, fileBukti, createdAt) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
                $stmtInsert = mysqli_prepare($this->db, $insertQuery);
                if (!$stmtInsert) {
                    throw new Exception("❌ Failed to prepare INSERT statement: " . mysqli_error($this->db));
                }
                
                mysqli_stmt_bind_param($stmtInsert, "iiissssddddds", 
                    $lpjId, 
                    $rabItemId, 
                    $kategoriId, 
                    $uraian, 
                    $rincian, 
                    $sat1, 
                    $sat2, 
                    $vol1, 
                    $vol2, 
                    $harga, 
                    $totalHarga, 
                    $realisasi, 
                    $fileBukti
                );
                
                if (mysqli_stmt_execute($stmtInsert)) {
                    $newLpjItemId = mysqli_insert_id($this->db);
                    error_log("✅ Item #{$index} INSERTED: lpjItemId={$newLpjItemId}, rabItemId={$rabItemId}, realisasi={$realisasi}");
                } else {
                    throw new Exception("❌ Item #{$index} INSERT FAILED: " . mysqli_stmt_error($stmtInsert));
                }
                
                mysqli_stmt_close($stmtInsert);
            }
        }
        
        mysqli_commit($this->db);
        error_log("✅ updateLpjItemsRealisasi completed for lpjId={$lpjId}");
        return true;
    } catch (Exception $e) {
        mysqli_rollback($this->db);
        error_log('LpjModel::updateLpjItemsRealisasi - Transaction failed: ' . $e->getMessage());
        return false;
    }
}

    public function submitRevisiLpj(int $lpjId, string $komentarRevisi): bool
    {
        // Status 2 = Revisi
        $query = "UPDATE tbl_lpj SET statusId = 2, komentarRevisi = ?, submittedAt = NOW() WHERE lpjId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::submitRevisiLpj - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'si', $komentarRevisi, $lpjId);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::submitRevisiLpj - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * ✅ NEW: Get total anggaran (budget) from RAB for a specific KAK
     * Used for aggregate validation where total realisasi must equal total anggaran
     * 
     * @param int $kakId
     * @return float Total anggaran from tbl_rab
     */
    public function getTotalAnggaranByKakId(int $kakId): float
    {
        $query = "SELECT COALESCE(SUM(totalHarga), 0) as totalAnggaran 
                  FROM tbl_rab 
                  WHERE kakId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        $total = floatval($row['totalAnggaran'] ?? 0);
        error_log("📊 Total Anggaran for kakId={$kakId}: Rp " . number_format($total, 2));
        
        return $total;
    }

    /**
     * Mengambil data LPJ untuk tampilan dashboard.
     *
     * @return array Array berisi data LPJ.
     */
    public function getDashboardLPJ(): array
    {
        $query = "SELECT 
                    l.lpjId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    l.submittedAt as tanggal_pengajuan,
                    l.approvedAt,
                    l.tenggatLpj,
                    CASE 
                        WHEN l.submittedAt IS NOT NULL AND l.statusId = 3 THEN 'Setuju'
                        WHEN l.submittedAt IS NOT NULL AND l.statusId = 2 THEN 'Revisi'
                        WHEN l.submittedAt IS NOT NULL AND l.statusId = 4 THEN 'Ditolak'
                        WHEN l.submittedAt IS NOT NULL AND l.statusId = 1 THEN 'Menunggu'
                        WHEN l.submittedAt IS NULL AND EXISTS (
                            SELECT 1 FROM tbl_lpj_item li 
                            WHERE li.lpjId = l.lpjId 
                            AND (li.fileBukti IS NULL OR li.fileBukti = '') OR (l.submittedAt IS NULL AND l.statusId = 1)
                        ) THEN 'Menunggu_Upload'
                        WHEN l.submittedAt IS NULL AND EXISTS (
                            SELECT 1 FROM tbl_lpj_item li 
                            WHERE li.lpjId = l.lpjId
                        ) THEN 'Siap_Submit'
                        ELSE 'Draft'
                    END as status
                  FROM tbl_lpj l
                  JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                  ORDER BY 
                    CASE 
                        WHEN l.statusId = 1 AND l.submittedAt IS NOT NULL THEN 1
                        WHEN l.statusId = 2 THEN 2
                        WHEN l.submittedAt IS NULL THEN 3
                        ELSE 4
                    END,
                    l.submittedAt DESC
                  LIMIT 10";

        $result = mysqli_query($this->db, $query);
        if ($result === false) {
            error_log('LpjModel::getDashboardLPJ - Query failed: ' . mysqli_error($this->db));
            return [];
        }

        $lpjData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $lpjData[] = $row;
        }

        return $lpjData;
    }
}