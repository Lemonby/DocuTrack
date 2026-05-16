<?php

namespace App\Models;

use mysqli;
use Exception;

/**
 * KategoriRabModel - Model for tbl_kategori_rab
 *
 * @category Model
 * @package  DocuTrack
 */
class KategoriRabModel
{
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor
     *
     * @param mysqli $db Database connection
     */
    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    /**
     * Get an existing RAB category by name or create a new one.
     *
     * @param string $namaKategori The name of the category.
     * @return int The ID of the existing or newly created category.
     * @throws Exception If database operation fails.
     */
    public function getOrCreateKategori(string $namaKategori): int
    {
        // Check if category exists
        $checkQuery = "SELECT kategoriRabId FROM tbl_kategori_rab WHERE namaKategori = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $checkQuery);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement for checking category: " . mysqli_error($this->db));
        }
        mysqli_stmt_bind_param($stmt, "s", $namaKategori);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            mysqli_stmt_close($stmt);
            return (int)$row['kategoriRabId'];
        }
        mysqli_stmt_close($stmt);

        // Create new category
        $insertQuery = "INSERT INTO tbl_kategori_rab (namaKategori) VALUES (?)";
        $stmt = mysqli_prepare($this->db, $insertQuery);
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement for inserting category: " . mysqli_error($this->db));
        }
        mysqli_stmt_bind_param($stmt, "s", $namaKategori);
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to insert new category: " . mysqli_stmt_error($stmt));
        }
        $kategoriId = mysqli_insert_id($this->db);
        mysqli_stmt_close($stmt);

        return (int)$kategoriId;
    }
}
