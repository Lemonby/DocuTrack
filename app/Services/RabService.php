<?php

namespace App\Services;

use App\Model\RabModel;
use App\Exceptions\ValidationException;
use App\Exceptions\BusinessLogicException;
use Exception;

/**
 * RabService - Business logic untuk RAB (Rencana Anggaran Biaya)
 *
 * Service layer untuk RAB calculations, validations, dan business rules.
 *
 * @category Service
 * @package  DocuTrack\Services
 * @version  2.0.0
 */
class RabService
{
    /**
     * @var mysqli Database connection
     */
    private $db;

    /**
     * @var RabModel
     */
    private $rabModel;

    /**
     * Constructor
     *
     * @param mysqli $db Database connection
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->rabModel = new RabModel($db);
    }

    /**
     * Get RAB grouped by category
     *
     * @param int $kakId
     * @return array
     */
    public function getRABByKAK($kakId)
    {
        $query = "SELECT 
                    r.*, 
                    cat.namaKategori 
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
        mysqli_stmt_close($stmt);
        return $data;
    }

    /**
     * Get RAB for LPJ (with empty bukti fields)
     *
     * @param int $kakId
     * @return array
     */
    public function getRABForLPJ($kakId)
    {
        $query = "SELECT 
                    r.rabItemId as id,
                    r.uraian,
                    r.rincian,
                    r.vol1,
                    r.sat1,
                    r.vol2,
                    r.sat2,
                    r.harga as harga_satuan,
                    r.totalHarga as harga_plan,
                    NULL as bukti_file,
                    NULL as komentar,
                    cat.namaKategori
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
        mysqli_stmt_close($stmt);
        return $data;
    }

    /**
     * Calculate total RAB amount
     *
     * Business logic: Sum all RAB items for a KAK
     *
     * @param int $kakId
     * @return float
     */
    public function calculateTotalRAB($kakId)
    {
        $query = "SELECT SUM(totalHarga) as total FROM tbl_rab WHERE kakId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        return floatval($row['total'] ?? 0);
    }

    /**
     * Validate RAB total against jurusan plafon
     *
     * Business rule: Total RAB tidak boleh melebihi plafon jurusan
     *
     * @param int $kakId
     * @param string $jurusanId
     * @throws BusinessLogicException
     */
    public function validateRABAgainstPlafon($kakId, $jurusanId)
    {
        $totalRab = $this->calculateTotalRAB($kakId);
        $plafon = $this->getJurusanPlafon($jurusanId);

        if ($totalRab > $plafon) {
            throw new BusinessLogicException(
                "Total RAB (Rp " . number_format($totalRab, 0, ',', '.') .
                ") melebihi plafon jurusan (Rp " . number_format($plafon, 0, ',', '.') . ")",
                [
                    'total_rab' => $totalRab,
                    'plafon' => $plafon,
                    'jurusan_id' => $jurusanId
                ]
            );
        }
    }

    /**
     * Get jurusan plafon (placeholder - adjust according to real table)
     *
     * @param string $jurusanId
     * @return float
     */
    private function getJurusanPlafon($jurusanId)
    {
        // TODO: Query from tbl_jurusan or tbl_plafon_jurusan
        // For now, return default value
        $query = "SELECT plafon FROM tbl_jurusan WHERE namaJurusan = ? LIMIT 1";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "s", $jurusanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        // Default plafon if table doesn't exist yet
        return floatval($row['plafon'] ?? 100000000); // 100 juta default
    }

    /**
     * Format RAB items for display
     *
     * @param array $rabItems
     * @return array
     */
    public function formatRABForDisplay($rabItems)
    {
        $formatted = [];

        foreach ($rabItems as $kategori => $items) {
            $formattedItems = array_map(function ($item) {
                return [
                    'uraian' => $item['uraian'],
                    'rincian' => $item['rincian'],
                    'volume' => $item['vol1'] . ' ' . $item['sat1'] .
                               ($item['vol2'] > 1 ? ' x ' . $item['vol2'] . ' ' . $item['sat2'] : ''),
                    'harga_satuan' => 'Rp ' . number_format($item['harga'], 0, ',', '.'),
                    'total' => 'Rp ' . number_format($item['totalHarga'], 0, ',', '.')
                ];
            }, $items);

            $formatted[$kategori] = [
                'items' => $formattedItems,
                'subtotal' => array_sum(array_column($items, 'totalHarga'))
            ];
        }

        return $formatted;
    }
}
