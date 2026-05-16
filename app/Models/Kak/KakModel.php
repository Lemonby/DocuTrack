<?php

namespace App\Models\Kak;

use Exception;
use mysqli;

// Ensure mysqli is available if not globally imported

class KakModel
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
                throw new \Exception("Database connection not provided to KakModel. Ensure bootstrap.php is loaded.");
            }
        }
    }

    // ==== KAK METHODS ====

    /**
     * Insert main data into tbl_kak.
     *
     * @param int $kegiatanId
     * @param string $gambaranUmum
     * @param string $penerimaManfaat
     * @param string $metodePelaksanaan
     * @param string|null $iku
     * @return int|false New KAK ID on success, false on failure.
     */
    public function insertKAK($kegiatanId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $iku = null)
    {
        // Fixed: Table name tbl_kak, Columns: kegiatanId, gambaranUmum, penerimaManfaat, metodePelaksanaan, iku
        $stmt = mysqli_prepare($this->db, "
            INSERT INTO tbl_kak (kegiatanId, gambaranUmum, penerimaManfaat, metodePelaksanaan, iku, tglPembuatan)
            VALUES (?, ?, ?, ?, ?, CURDATE())
        ");
        if ($stmt === false) {
            error_log('KakModel::insertKAK - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'issss', $kegiatanId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $iku);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);
            return $newId;
        } else {
            error_log('KakModel::insertKAK - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Update main data in tbl_kak.
     *
     * @param int $kakId
     * @param string $gambaranUmum
     * @param string $penerimaManfaat
     * @param string $metodePelaksanaan
     * @param string|null $iku
     * @return bool True on success, false on failure.
     */
    public function updateKAK($kakId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $iku = null)
    {
        // Fixed: Column names matched to schema
        $stmt = mysqli_prepare($this->db, "
            UPDATE tbl_kak SET 
                gambaranUmum = ?, 
                penerimaManfaat = ?, 
                metodePelaksanaan = ?, 
                iku = ?
            WHERE kakId = ?
        ");
        if ($stmt === false) {
            error_log('KakModel::updateKAK - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ssssi', $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $iku, $kakId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KakModel::updateKAK - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Get single KAK data with all related indicators & stages.
     *
     * @param int $kakId
     * @return array|null KAK data array on success, null if not found.
     */
    public function getKAKWithRelationsById($kakId)
    {
        // Fixed: Column names and table joins
        $query = "
            SELECT 
                k.kakId, 
                k.kegiatanId, 
                k.gambaranUmum, 
                k.penerimaManfaat, 
                k.metodePelaksanaan, 
                k.iku,
                i.indikatorId,
                i.bulan,
                i.indikatorKeberhasilan,
                i.targetPersen,
                t.tahapanId,
                t.namaTahapan
            FROM tbl_kak k
            LEFT JOIN tbl_indikator_kak i ON k.kakId = i.kakId
            LEFT JOIN tbl_tahapan_pelaksanaan t ON k.kakId = t.kakId
            WHERE k.kakId = ?
            ORDER BY k.kakId ASC
        ";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('KakModel::getKAKWithRelationsById - Prepare failed: ' . mysqli_error($this->db));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $kakId);

        if (!mysqli_stmt_execute($stmt)) {
            error_log('KakModel::getKAKWithRelationsById - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $kakData = null;

        while ($row = mysqli_fetch_assoc($result)) {
            if ($kakData === null) {
                $kakData = [
                    'kakId' => $row['kakId'],
                    'kegiatanId' => $row['kegiatanId'],
                    'gambaranUmum' => $row['gambaranUmum'],
                    'penerimaManfaat' => $row['penerimaManfaat'],
                    'metodePelaksanaan' => $row['metodePelaksanaan'],
                    'iku' => $row['iku'],
                    'indikator_list' => [],
                    'tahapan_list' => []
                ];
            }

            if (!empty($row['indikatorId'])) {
                // Check duplicates in result set
                $exists = false;
                foreach ($kakData['indikator_list'] as $ind) {
                    if ($ind['indikatorId'] == $row['indikatorId']) {
                        $exists = true;
                        break;
                    }
                }
                
                if (!$exists) {
                    $kakData['indikator_list'][] = [
                        'indikatorId' => $row['indikatorId'],
                        'bulan' => $row['bulan'],
                        'indikatorKeberhasilan' => $row['indikatorKeberhasilan'],
                        'targetPersen' => $row['targetPersen']
                    ];
                }
            }

            if (!empty($row['tahapanId'])) {
                // Check duplicates in result set
                $exists = false;
                foreach ($kakData['tahapan_list'] as $tah) {
                    if ($tah['tahapanId'] == $row['tahapanId']) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) {
                    $kakData['tahapan_list'][] = [
                        'tahapanId' => $row['tahapanId'],
                        'namaTahapan' => $row['namaTahapan']
                    ];
                }
            }
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);

        return $kakData;
    }

    /**
     * Get all KAK data with complete indicators & stages (JOIN).
     *
     * @return array Array of KAK records.
     */
    public function getAllKAKWithRelations()
    {
        $query = "
            SELECT 
                k.kakId, 
                k.kegiatanId, 
                k.gambaranUmum, 
                k.penerimaManfaat, 
                k.metodePelaksanaan, 
                k.iku,
                i.indikatorId,
                i.bulan,
                i.indikatorKeberhasilan,
                i.targetPersen,
                t.tahapanId,
                t.namaTahapan
            FROM tbl_kak k
            LEFT JOIN tbl_indikator_kak i ON k.kakId = i.kakId
            LEFT JOIN tbl_tahapan_pelaksanaan t ON k.kakId = t.kakId
            ORDER BY k.kakId ASC
        ";

        $result = mysqli_query($this->db, $query);
        if (!$result) {
            error_log('KakModel::getAllKAKWithRelations - Query failed: ' . mysqli_error($this->db));
            return [];
        }

        $kakList = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $kakId = $row['kakId'];

            if (!isset($kakList[$kakId])) {
                $kakList[$kakId] = [
                    'kakId' => $row['kakId'],
                    'kegiatanId' => $row['kegiatanId'],
                    'gambaranUmum' => $row['gambaranUmum'],
                    'penerimaManfaat' => $row['penerimaManfaat'],
                    'metodePelaksanaan' => $row['metodePelaksanaan'],
                    'iku' => $row['iku'],
                    'indikator_list' => [],
                    'tahapan_list' => []
                ];
            }

            if (!empty($row['indikatorId'])) {
                $exists = false;
                foreach ($kakList[$kakId]['indikator_list'] as $item) {
                    if ($item['indikatorId'] == $row['indikatorId']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $kakList[$kakId]['indikator_list'][] = [
                        'indikatorId' => $row['indikatorId'],
                        'bulan' => $row['bulan'],
                        'indikatorKeberhasilan' => $row['indikatorKeberhasilan'],
                        'targetPersen' => $row['targetPersen']
                    ];
                }
            }

            if (!empty($row['tahapanId'])) {
                $exists = false;
                foreach ($kakList[$kakId]['tahapan_list'] as $item) {
                    if ($item['tahapanId'] == $row['tahapanId']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $kakList[$kakId]['tahapan_list'][] = [
                        'tahapanId' => $row['tahapanId'],
                        'namaTahapan' => $row['namaTahapan']
                    ];
                }
            }
        }

        mysqli_free_result($result);
        return array_values($kakList);
    }

    /**
     * Delete all KAK data and its relations (indicators & stages) using a transaction.
     *
     * @param int $kakId
     * @return bool True on success, false on failure.
     * @throws Exception If a database operation fails.
     */
    public function deleteKAK($kakId)
    {
        // Start Transaction
        mysqli_begin_transaction($this->db);

        try {
            // 1. Prepare & Execute Delete Stages
            $stmt1 = mysqli_prepare($this->db, "DELETE FROM tbl_tahapan_pelaksanaan WHERE kakId = ?");
            if ($stmt1 === false) {
                throw new Exception("KakModel::deleteKAK - Prepare tahapan failed: " . mysqli_error($this->db));
            }
            mysqli_stmt_bind_param($stmt1, 'i', $kakId);
            if (!mysqli_stmt_execute($stmt1)) {
                throw new Exception(mysqli_stmt_error($stmt1));
            }
            mysqli_stmt_close($stmt1);

            // 2. Prepare & Execute Delete Indicators
            // Fixed: Table name tbl_indikator_kak
            $stmt2 = mysqli_prepare($this->db, "DELETE FROM tbl_indikator_kak WHERE kakId = ?");
            if ($stmt2 === false) {
                throw new Exception("KakModel::deleteKAK - Prepare indikator failed: " . mysqli_error($this->db));
            }
            mysqli_stmt_bind_param($stmt2, 'i', $kakId);
            if (!mysqli_stmt_execute($stmt2)) {
                throw new Exception(mysqli_stmt_error($stmt2));
            }
            mysqli_stmt_close($stmt2);

            // 3. Prepare & Execute Delete Main KAK
            $stmt3 = mysqli_prepare($this->db, "DELETE FROM tbl_kak WHERE kakId = ?");
            if ($stmt3 === false) {
                throw new Exception("KakModel::deleteKAK - Prepare main KAK failed: " . mysqli_error($this->db));
            }
            mysqli_stmt_bind_param($stmt3, 'i', $kakId);
            if (!mysqli_stmt_execute($stmt3)) {
                throw new Exception(mysqli_stmt_error($stmt3));
            }
            mysqli_stmt_close($stmt3);

            // If all successful, commit
            mysqli_commit($this->db);
            return true;
        } catch (Exception $e) {
            // If any fails, rollback
            mysqli_rollback($this->db);
            error_log('KakModel::deleteKAK - Transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    // ==== STAGE METHODS ====

    /**
     * Insert multiple stages into tbl_tahapan_pelaksanaan.
     *
     * @param int $kakId
     * @param array $tahapanList
     * @return bool True on success, false on failure.
     */
    public function insertTahapanPelaksanaan($kakId, $tahapanList)
    {
        $stmt = mysqli_prepare($this->db, "
            INSERT INTO tbl_tahapan_pelaksanaan (kakId, namaTahapan)
            VALUES (?, ?)
        ");
        if ($stmt === false) {
            error_log('KakModel::insertTahapanPelaksanaan - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        foreach ($tahapanList as $tahapan) {
            mysqli_stmt_bind_param($stmt, 'is', $kakId, $tahapan);
            if (!mysqli_stmt_execute($stmt)) {
                error_log('KakModel::insertTahapanPelaksanaan - Execute failed: ' . mysqli_stmt_error($this->db));
            }
        }

        mysqli_stmt_close($stmt);
        return true;
    }

    // ==== KAK INDICATOR METHODS ====

    /**
     * Insert multiple indicators.
     *
     * @param int $kakId
     * @param array $indikatorList
     * @return bool True on success, false on failure.
     */
    public function insertIndikatorKinerja($kakId, $indikatorList)
    {
        // Fixed: Table name tbl_indikator_kak and column names
        $stmt = mysqli_prepare($this->db, "
            INSERT INTO tbl_indikator_kak (kakId, bulan, indikatorKeberhasilan, targetPersen)
            VALUES (?, ?, ?, ?)
        ");
        if ($stmt === false) {
            error_log('KakModel::insertIndikatorKinerja - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        foreach ($indikatorList as $indikator) {
            mysqli_stmt_bind_param(
                $stmt,
                'iisi',
                $kakId,
                $indikator['bulan'],
                $indikator['indikatorKeberhasilan'], // Fixed: snake_case to camelCase
                $indikator['targetPersen'] // Fixed: snake_case to camelCase
            );
            if (!mysqli_stmt_execute($stmt)) {
                error_log('KakModel::insertIndikatorKinerja - Execute failed: ' . mysqli_stmt_error($this->db));
            }
        }

        mysqli_stmt_close($stmt);
        return true;
    }
}