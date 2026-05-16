<?php

namespace App\Models;

use App\Core\Database;
use mysqli;

class DirekturModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get statistik umum dashboard
     */
    public function getStatistikUmum(): array
    {
        $query = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN (k.posisiId >= 5 OR (k.posisiId = 1 AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NOT NULL)) AND l.statusId = 3 THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN k.statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                SUM(CASE WHEN k.posisiId = 4 AND k.statusUtamaId = 1 OR l.statusId = 1 THEN 1 ELSE 0 END) as menunggu,
                SUM(CASE WHEN k.statusUtamaId = 2 THEN 1 ELSE 0 END) as revisi,
                SUM(CASE WHEN k.statusUtamaId = 1 THEN 1 ELSE 0 END) as menunggu
            FROM tbl_kegiatan k
            LEFT JOIN tbl_lpj l ON k.kegiatanId = l.kegiatanId
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return [
            'total' => 0,
            'disetujui' => 0,
            'ditolak' => 0,
            'menunggu' => 0,
            'revisi' => 0
        ];
    }

    /**
     * Get data usulan per jurusan dengan filter periode
     */
    public function getUsulanPerJurusan(string $periode = 'all'): array
    {
        $whereClause = $this->getPeriodeWhereClause($periode);

        $query = "
            SELECT 
                j.namaJurusan,
                COUNT(k.kegiatanId) as jumlah_usulan
            FROM tbl_jurusan j
            LEFT JOIN tbl_kegiatan k ON j.namaJurusan = k.jurusanPenyelenggara
            $whereClause
            GROUP BY j.namaJurusan
            ORDER BY jumlah_usulan DESC
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        $labels = [];
        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['namaJurusan'];
                $data[] = (int)$row['jumlah_usulan'];
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get total dana keluar per jurusan
     * 
     * Menggunakan kolom danaDisetujui dari tbl_kegiatan yang merupakan
     * sum dari total dana yang diajukan untuk setiap kegiatan yang sudah disetujui.
     */
    public function getTotalDanaPerJurusan(): array
    {
        // Query menggunakan danaDisetujui sebagai dasar perhitungan
        // danaDisetujui = total dana yang diajukan untuk kegiatan tersebut
        // Kondisi:
        // - statusUtamaId = 3 (Disetujui) ATAU
        // - statusUtamaId IN (5,6) dengan posisiId IN (1,5) (Dana sudah cair/selesai)
        $query = "
            SELECT 
                j.namaJurusan,
                COALESCE(SUM(k.danaDisetujui), 0) as total_dana
            FROM tbl_jurusan j
            LEFT JOIN tbl_kegiatan k ON j.namaJurusan = k.jurusanPenyelenggara 
                AND (
                    k.statusUtamaId = 3 
                    OR (k.statusUtamaId IN (5, 6) AND k.posisiId IN (1, 5))
                )
                AND k.danaDisetujui IS NOT NULL
                AND k.danaDisetujui > 0
            GROUP BY j.namaJurusan
            HAVING total_dana > 0
            ORDER BY total_dana DESC
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        $labels = [];
        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['namaJurusan'];
                $data[] = (int)$row['total_dana'];
            }
        }

        // Jika tidak ada data, return array kosong dengan pesan
        if (empty($labels)) {
            return [
                'labels' => ['Belum ada data'],
                'data' => [0]
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get list semua jurusan
     */
    public function getListJurusan(): array
    {
        $query = "SELECT namaJurusan FROM tbl_jurusan ORDER BY namaJurusan ASC";
        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        $jurusanList = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $jurusanList[] = $row['namaJurusan'];
            }
        }

        return $jurusanList;
    }

    /**
     * Get data kegiatan untuk chart (dengan timestamp)
     */
    public function getDataKegiatanForChart(): array
    {
        $query = "
            SELECT 
                k.kegiatanId,
                k.namaKegiatan,
                k.jurusanPenyelenggara,
                k.createdAt,
                s.namaStatusUsulan as status
            FROM tbl_kegiatan k
            LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
            ORDER BY k.createdAt DESC
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        $kegiatanList = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $kegiatanList[] = $row;
            }
        }

        return $kegiatanList;
    }

    /**
     * Get summary statistik untuk info boxes chart
     */
    public function getSummaryStatistik(string $periode = 'all'): array
    {
        $whereClause = $this->getPeriodeWhereClause($periode);

        $query = "
            SELECT 
                COUNT(DISTINCT k.jurusanPenyelenggara) as total_jurusan,
                COUNT(*) as total_usulan,
                COALESCE(MAX(jumlah_per_jurusan), 0) as max_usulan,
                COALESCE(AVG(jumlah_per_jurusan), 0) as avg_usulan
            FROM (
                SELECT 
                    k.jurusanPenyelenggara,
                    COUNT(*) as jumlah_per_jurusan
                FROM tbl_kegiatan k
                $whereClause
                GROUP BY k.jurusanPenyelenggara
            ) as sub
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return [
                'total_jurusan' => (int)($row['total_jurusan'] ?? 0),
                'total_usulan' => (int)($row['total_usulan'] ?? 0),
                'max_usulan' => (int)($row['max_usulan'] ?? 0),
                'avg_usulan' => round($row['avg_usulan'] ?? 0, 1)
            ];
        }

        return [
            'total_jurusan' => 0,
            'total_usulan' => 0,
            'max_usulan' => 0,
            'avg_usulan' => 0
        ];
    }

    /**
     * Get daftar pengajuan dengan pagination
     */
    public function getDaftarPengajuan(int $page = 1, int $perPage = 5, ?string $search = null, ?string $jurusan = null): array
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];
        $types = '';

        if (!empty($search)) {
            $conditions[] = "(k.namaKegiatan LIKE ? OR k.pemilikKegiatan LIKE ? OR k.nimPelaksana LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'sss';
        }

        if (!empty($jurusan)) {
            $conditions[] = "k.jurusanPenyelenggara = ?";
            $params[] = $jurusan;
            $types .= 's';
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM tbl_kegiatan k $whereClause";
        
        if (!empty($params)) {
            $countStmt = $this->db->prepare($countQuery);
            if (!$countStmt) {
                throw new \Exception("Prepare failed: " . $this->db->error);
            }
            $countStmt->bind_param($types, ...$params);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $totalItems = (int)$countResult->fetch_assoc()['total'];
            $countStmt->close();
        } else {
            $countResult = $this->db->query($countQuery);
            if (!$countResult) {
                throw new \Exception("Query failed: " . $this->db->error);
            }
            $totalItems = (int)$countResult->fetch_assoc()['total'];
        }

        // ✅ FIXED: Tambahkan kolom estimasi_dana dan lengkapi query dengan ORDER BY & LIMIT
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan,
                    k.pemilikKegiatan,
                    k.nimPelaksana,
                    k.prodiPenyelenggara,
                    k.jurusanPenyelenggara,
                    k.danaDisetujui as estimasi_dana,    
                    k.createdAt as tanggal,
                    k.posisiId,
                    k.statusUtamaId,
                    l.statusId as lpj_status_id,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak' 
                        WHEN k.posisiId = 1 AND k.statusUtamaId NOT IN (4, 5, 6) THEN 'Pengajuan'
                        WHEN k.posisiId = 2 AND k.statusUtamaId != 4 THEN 'Verifikasi'
                        WHEN k.posisiId = 4 THEN 'ACC PPK'
                        WHEN k.posisiId = 3 THEN 'ACC WD'
                        WHEN k.posisiId IN (1) AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NOT NULL AND l.statusId = 1 THEN 'LPJ'
                        WHEN k.posisiId = 5 OR (k.posisiId = 1 AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NULL) THEN 'Dana Cair'
                        ELSE 'Unknown'
                    END as tahap_sekarang,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN (k.posisiId >= 5 OR (k.posisiId = 1 AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NOT NULL)) AND l.statusId = 3 THEN 'Approved'
                        WHEN k.statusUtamaId = 1 OR l.statusId = 1 THEN 'Menunggu'
                        ELSE 'In Process'
                    END as status
                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_lpj l ON k.kegiatanId = l.kegiatanId 
                  $whereClause
                  ORDER BY k.createdAt DESC
                  LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->db->error);
        }
        
        // ✅ FIXED: Sekarang bind params match dengan placeholder di query
        $bindParams = $params;
        $bindParams[] = $perPage;
        $bindParams[] = $offset;
        $bindTypes = $types . 'ii';
        
        $stmt->bind_param($bindTypes, ...$bindParams);
        
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        $stmt->close();

        return [
            'items' => $items,
            'total' => $totalItems,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalItems > 0 ? (int)ceil($totalItems / $perPage) : 0
        ];
    }

    /**
     * Mengambil data monitoring untuk Direktur dengan filtering dan pagination.
     *
     * Method ini mengambil data kegiatan untuk monitoring dengan berbagai filter:
     * - 'menunggu': Hanya usulan yang menunggu approval
     * - 'approved': Usulan yang sudah disetujui
     * - 'ditolak': Usulan yang ditolak (statusUtamaId = 4)
     * - 'in process': Usulan yang masih dalam proses
     * - 'lpj': Usulan yang dalam tahap LPJ
     *
     * @param int $page Halaman saat ini untuk pagination
     * @param int $perPage Jumlah item per halaman
     * @param string $search Kata kunci pencarian (nama kegiatan atau pengusul)
     * @param string $statusFilter Filter status: 'semua', 'menunggu', 'approved', 'ditolak', 'in process', 'lpj'
     * @param string $jurusanFilter Filter jurusan: 'semua' atau nama jurusan spesifik
     * @return array Array dengan key 'data' (list kegiatan) dan 'totalItems' (total records)
     */
    public function getMonitoringData($page, $perPage, $search, $statusFilter, $jurusanFilter)
    {
        $offset = ($page - 1) * $perPage;

        // Base Where Clause & Params Construction
        $whereClause = " WHERE 1=1";
        $types = "";
        $params = [];

        // Filter pencarian
        if (!empty($search)) {
            $whereClause .= " AND (k.namaKegiatan LIKE ? OR k.pemilikKegiatan LIKE ?)";
            $searchTerm = "%{$search}%";
            $types .= "ss";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Filter status
        if ($statusFilter !== 'semua') {
            if ($statusFilter === 'ditolak') {
                $whereClause .= " AND k.statusUtamaId = 4";
            } elseif ($statusFilter === 'approved') {
                $whereClause .= " AND k.posisiId >= 5 AND k.statusUtamaId != 4";
            } elseif ($statusFilter === 'menunggu') {
                $whereClause .= " AND k.statusUtamaId = 1 AND k.posisiId < 5";
            } elseif ($statusFilter === 'in process') {
                $whereClause .= " AND k.statusUtamaId != 4 AND k.posisiId < 5 AND k.statusUtamaId != 1";
            } elseif ($statusFilter === 'lpj') {
                // Filter untuk LPJ: posisi di 1 atau 6, status 5 atau 6, dan dana sudah cair
                $whereClause .= " AND k.posisiId IN (1, 6) AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NOT NULL";
            }
        }

        // Filter jurusan
        if ($jurusanFilter !== 'semua') {
            $whereClause .= " AND k.jurusanPenyelenggara = ?";
            $types .= "s";
            $params[] = $jurusanFilter;
        }

        // 1. Execute Count Query
        $countQuery = "SELECT COUNT(*) as total FROM tbl_kegiatan k" . $whereClause;
        $stmtCount = mysqli_prepare($this->db, $countQuery);

        if ($types !== "") {
            mysqli_stmt_bind_param($stmtCount, $types, ...$params);
        }

        mysqli_stmt_execute($stmtCount);
        $totalResult = mysqli_stmt_get_result($stmtCount);
        $totalItems = ($totalResult) ? mysqli_fetch_assoc($totalResult)['total'] : 0;
        mysqli_stmt_close($stmtCount);

        // 2. Execute Data Query
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as pengusul,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    k.createdAt as tanggal,
                    k.posisiId,
                    k.statusUtamaId,
                    l.statusId as lpj_status_id,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak' 
                        WHEN k.posisiId = 1 AND k.statusUtamaId NOT IN (4, 5, 6) THEN 'Pengajuan'
                        WHEN k.posisiId = 2 AND k.statusUtamaId != 4 THEN 'Verifikasi'
                        WHEN k.posisiId = 4 THEN 'ACC PPK'
                        WHEN k.posisiId = 3 THEN 'ACC WD'
                        WHEN k.posisiId IN (1) AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NOT NULL AND l.statusId = 1 THEN 'LPJ'
                        WHEN k.posisiId = 5 OR (k.posisiId = 1 AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NULL) THEN 'Dana Cair'
                        ELSE 'Unknown'
                    END as tahap_sekarang,
                    CASE 
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN (k.posisiId >= 5 OR (k.posisiId = 1 AND k.statusUtamaId IN (5, 6) AND k.jumlahDicairkan IS NOT NULL)) AND l.statusId = 3 THEN 'Approved'
                        WHEN k.statusUtamaId = 1 OR l.statusId = 1 THEN 'Menunggu'
                        ELSE 'In Process'
                    END as status
                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_lpj l ON k.kegiatanId = l.kegiatanId" . $whereClause;

        // Add ORDER BY and LIMIT
        $query .= " ORDER BY k.createdAt DESC LIMIT ? OFFSET ?";
        
        // Add limit/offset to params
        $typesWithLimit = $types . "ii";
        $paramsWithLimit = $params;
        $paramsWithLimit[] = $perPage;
        $paramsWithLimit[] = $offset;

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, $typesWithLimit, ...$paramsWithLimit);
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        mysqli_stmt_close($stmt);

        return [
            'data' => $data,
            'totalItems' => $totalItems
        ];
    }

    /**
     * Mengambil list jurusan yang distinct dari tabel kegiatan
     * 
     * @return array List nama jurusan
     */
    public function getListJurusanDistinct()
    {
        $query = "SELECT DISTINCT jurusanPenyelenggara as jurusan 
                  FROM tbl_kegiatan 
                  WHERE jurusanPenyelenggara IS NOT NULL AND jurusanPenyelenggara != '' 
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

    /**
     * Get data monitoring proposal dengan filter dan pagination (DEPRECATED - gunakan getMonitoringData)
     */
    public function getMonitoringProposal(int $page = 1, int $perPage = 10, array $filters = []): array
    {
        // Convert to new method format
        $search = $filters['search'] ?? '';
        $statusFilter = strtolower($filters['status'] ?? 'semua');
        $jurusanFilter = $filters['jurusan'] ?? 'semua';
        
        return $this->getMonitoringData($page, $perPage, $search, $statusFilter, $jurusanFilter);
    }

    /**
     * Helper: Generate WHERE clause untuk filter periode
     */
    private function getPeriodeWhereClause(string $periode): string
    {
        switch ($periode) {
            case 'today':
                return "WHERE DATE(k.createdAt) = CURDATE()";
            case 'week':
                return "WHERE k.createdAt >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'month':
                return "WHERE k.createdAt >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            case 'year':
                return "WHERE k.createdAt >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            default:
                return "";
        }
    }
}