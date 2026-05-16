<?php

namespace App\Models\Rab;

use mysqli;
use Exception;

/**
 * RabItemModel - RAB (Rencana Anggaran Biaya) Item Model
 *
 * Model untuk mengelola tbl_rab dengan DI pattern.
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Converted from procedural to OOP
 */

class RabItemModel
{
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection untuk database connection
     *
     * @param mysqli $db Database connection dari Database::getInstance()->getConnection()
     */
    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Menyisipkan item RAB baru.
     *
     * @param int $kakId ID KAK terkait
     * @param int $kategoriId ID kategori RAB
     * @param string $uraian Uraian item
     * @param string $rincian Rincian detail item
     * @param string $sat1 Satuan 1
     * @param string $sat2 Satuan 2
     * @param float $vol1 Volume 1
     * @param float $vol2 Volume 2
     * @param float $harga Harga satuan
     * @param float $totalHarga Total harga item (dihitung)
     * @return int|false ID item RAB baru jika berhasil, false jika gagal
     * @throws Exception Jika operasi database gagal.
     */
    public function insertRabItem(
        int $kakId,
        int $kategoriId,
        string $uraian,
        string $rincian,
        string $sat1,
        string $sat2,
        float $vol1,
        float $vol2,
        float $harga,
        float $totalHarga
    ): int {
        $query = "INSERT INTO tbl_rab (kakId, kategoriId, uraian, rincian, sat1, sat2, vol1, vol2, harga, totalHarga)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            throw new Exception("RabItemModel::insertRabItem - Prepare failed: " . mysqli_error($this->db));
        }

        mysqli_stmt_bind_param(
            $stmt,
            'iissssdddd',
            $kakId,
            $kategoriId,
            $uraian,
            $rincian,
            $sat1,
            $sat2,
            $vol1,
            $vol2,
            $harga,
            $totalHarga
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("RabItemModel::insertRabItem - Execute failed: " . mysqli_stmt_error($stmt));
        }

        $newId = mysqli_insert_id($this->db);
        mysqli_stmt_close($stmt);
        return $newId;
    }

    /**
     * Retrieve RAB data by Kegiatan ID.
     * This method joins tbl_rab with tbl_kategori_rab.
     *
     * @param int $kegiatanId
     * @return array Structured array of RAB data [CategoryId => [CategoryName, Items => []]]
     * @throws Exception If database operation fails.
     */
    public function getRabByKegiatanId(int $kegiatanId): array
    {
        $query = "SELECT
                    r.rabItemId,
                    r.kakId,
                    r.kategoriId,
                    kr.namaKategori,
                    r.uraian,
                    r.rincian,
                    r.sat1,
                    r.sat2,
                    r.vol1,
                    r.vol2,
                    r.harga,
                    r.totalHarga
                  FROM tbl_rab r
                  JOIN tbl_kategori_rab kr ON r.kategoriId = kr.kategoriRabId
                  WHERE r.kakId = ?
                  ORDER BY kr.namaKategori, r.rabItemId";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            throw new Exception('RabItemModel::getRabByKegiatanId - Prepare failed: ' . mysqli_error($this->db));
        }

        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $rabData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $catId = $row['kategoriId'];
            if (!isset($rabData[$catId])) {
                $rabData[$catId] = [
                    'kategoriId' => $row['kategoriId'],
                    'namaKategori' => $row['namaKategori'],
                    'items' => []
                ];
            }
            $rabData[$catId]['items'][] = [
                'rabItemId' => $row['rabItemId'],
                'kakId' => $row['kakId'],
                'uraian' => $row['uraian'],
                'rincian' => $row['rincian'],
                'sat1' => $row['sat1'],
                'sat2' => $row['sat2'],
                'vol1' => $row['vol1'],
                'vol2' => $row['vol2'],
                'harga' => $row['harga'],
                'totalHarga' => $row['totalHarga']
            ];
        }
        mysqli_stmt_close($stmt);
        return $rabData;
    }
}