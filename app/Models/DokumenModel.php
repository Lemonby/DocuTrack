<?php

namespace App\Model;

use mysqli;
use Exception;

/**
 * DokumenModel - Database operations untuk file metadata
 *
 * Handle CRUD operations untuk tbl_dokumen yang menyimpan
 * metadata file upload (KAK, RAB, LPJ).
 *
 * @category Model
 * @package  DocuTrack\Model
 * @version  1.0.0
 */
class DokumenModel
{
    /**
     * @var mysqli Database connection
     */
    private $db;

    /**
     * Constructor
     *
     * @param mysqli $db Database connection
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Insert new document record
     *
     * @param array $data Document data
     * @return int Inserted document ID
     * @throws Exception
     */
    public function insert($data)
    {
        $sql = "INSERT INTO tbl_dokumen 
                (reference_type, reference_id, original_name, filename, file_path, 
                 file_size, mime_type, uploaded_by, uploaded_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }

        $stmt->bind_param(
            'sisssiis',
            $data['reference_type'],
            $data['reference_id'],
            $data['original_name'],
            $data['filename'],
            $data['file_path'],
            $data['file_size'],
            $data['mime_type'],
            $data['uploaded_by']
        );

        if (!$stmt->execute()) {
            throw new Exception('Failed to insert document: ' . $stmt->error);
        }

        $insertId = $stmt->insert_id;
        $stmt->close();

        return $insertId;
    }

    /**
     * Find document by ID
     *
     * @param int $id Document ID
     * @return array|null Document data
     */
    public function findById($id)
    {
        $sql = "SELECT d.*, u.nama as uploader_name
                FROM tbl_dokumen d
                LEFT JOIN tbl_user u ON d.uploaded_by = u.id
                WHERE d.id = ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $document = $result->fetch_assoc();

        $stmt->close();

        return $document ?: null;
    }

    /**
     * Find documents by reference (KAK/RAB/LPJ)
     *
     * @param string $type Reference type ('kak', 'rab', 'lpj')
     * @param int $refId Reference ID (kegiatan_id, rab_id, lpj_id)
     * @return array Array of documents
     */
    public function findByReference($type, $refId)
    {
        $sql = "SELECT d.*, u.nama as uploader_name
                FROM tbl_dokumen d
                LEFT JOIN tbl_user u ON d.uploaded_by = u.id
                WHERE d.reference_type = ? AND d.reference_id = ?
                ORDER BY d.uploaded_at DESC";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }

        $stmt->bind_param('si', $type, $refId);
        $stmt->execute();

        $result = $stmt->get_result();
        $documents = [];

        while ($row = $result->fetch_assoc()) {
            $documents[] = $row;
        }

        $stmt->close();

        return $documents;
    }

    /**
     * Find all documents by type
     *
     * @param string $type Reference type ('kak', 'rab', 'lpj')
     * @param int $limit Limit results (default: 100)
     * @param int $offset Offset (default: 0)
     * @return array Array of documents
     */
    public function findByType($type, $limit = 100, $offset = 0)
    {
        $sql = "SELECT d.*, u.nama as uploader_name
                FROM tbl_dokumen d
                LEFT JOIN tbl_user u ON d.uploaded_by = u.id
                WHERE d.reference_type = ?
                ORDER BY d.uploaded_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }

        $stmt->bind_param('sii', $type, $limit, $offset);
        $stmt->execute();

        $result = $stmt->get_result();
        $documents = [];

        while ($row = $result->fetch_assoc()) {
            $documents[] = $row;
        }

        $stmt->close();

        return $documents;
    }

    /**
     * Delete document record
     *
     * @param int $id Document ID
     * @return bool Success status
     * @throws Exception
     */
    public function delete($id)
    {
        $sql = "DELETE FROM tbl_dokumen WHERE id = ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }

        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            throw new Exception('Failed to delete document: ' . $stmt->error);
        }

        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows > 0;
    }

    /**
     * Update file size
     *
     * @param int $id Document ID
     * @param int $size New file size
     * @return bool Success status
     */
    public function updateFileSize($id, $size)
    {
        $sql = "UPDATE tbl_dokumen SET file_size = ? WHERE id = ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }

        $stmt->bind_param('ii', $size, $id);
        $success = $stmt->execute();

        $stmt->close();

        return $success;
    }

    /**
     * Count documents by reference
     *
     * @param string $type Reference type
     * @param int $refId Reference ID
     * @return int Document count
     */
    public function countByReference($type, $refId)
    {
        $sql = "SELECT COUNT(*) as total
                FROM tbl_dokumen
                WHERE reference_type = ? AND reference_id = ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }

        $stmt->bind_param('si', $type, $refId);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return (int) $row['total'];
    }

    /**
     * Get total storage used by type
     *
     * @param string $type Reference type
     * @return int Total size in bytes
     */
    public function getTotalStorageByType($type)
    {
        $sql = "SELECT SUM(file_size) as total_size
                FROM tbl_dokumen
                WHERE reference_type = ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }

        $stmt->bind_param('s', $type);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return (int) ($row['total_size'] ?? 0);
    }

    /**
     * Get recent uploads
     *
     * @param int $limit Limit results
     * @param int|null $userId Filter by user ID (optional)
     * @return array Recent documents
     */
    public function getRecentUploads($limit = 10, $userId = null)
    {
        $sql = "SELECT d.*, u.nama as uploader_name
                FROM tbl_dokumen d
                LEFT JOIN tbl_user u ON d.uploaded_by = u.id";

        if ($userId !== null) {
            $sql .= " WHERE d.uploaded_by = ?";
        }

        $sql .= " ORDER BY d.uploaded_at DESC LIMIT ?";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            throw new Exception('Failed to prepare statement: ' . $this->db->error);
        }

        if ($userId !== null) {
            $stmt->bind_param('ii', $userId, $limit);
        } else {
            $stmt->bind_param('i', $limit);
        }

        $stmt->execute();

        $result = $stmt->get_result();
        $documents = [];

        while ($row = $result->fetch_assoc()) {
            $documents[] = $row;
        }

        $stmt->close();

        return $documents;
    }

    /**
     * Check if document exists
     *
     * @param int $id Document ID
     * @return bool
     */
    public function exists($id)
    {
        $sql = "SELECT 1 FROM tbl_dokumen WHERE id = ? LIMIT 1";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;

        $stmt->close();

        return $exists;
    }
}
