<?php

namespace App\Services;

use App\Exceptions\UploadException;
use Exception;

class FileUploadService
{
    private $uploadBasePath;

    private $allowedMimes = [
        'image' => ['image/jpeg', 'image/png', 'image/gif'],
        'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    ];

    private $maxFileSize = 5 * 1024 * 1024; // 5 MB

    public function __construct()
    {
        $this->uploadBasePath = DOCUTRACK_ROOT . '/public/uploads';
        if (!is_dir($this->uploadBasePath)) {
            mkdir($this->uploadBasePath, 0777, true);
        }
    }

    /**
     * Handles profile image uploads.
     * @param array $file The file array from $_FILES.
     * @return string The public path to the uploaded file.
     * @throws UploadException
     */
    public function uploadProfileImage(array $file): string
    {
        return $this->handleUpload($file, 'profiles', 'image', 2 * 1024 * 1024); // 2MB limit for profiles
    }

    /**
     * Handles header background uploads.
     * @param array $file The file array from $_FILES.
     * @return string The public URL path for CSS.
     * @throws UploadException
     */
    public function uploadHeaderBackground(array $file): string
    {
        $publicPath = $this->handleUpload($file, 'profiles', 'image', 2 * 1024 * 1024);
        return "url('$publicPath')";
    }

    /**
     * Handles LPJ document uploads.
     * @param array $file The file array from $_FILES.
     * @param int $itemId The ID of the LPJ item.
     * @return string The filename of the uploaded file.
     * @throws UploadException
     */
    public function uploadLpjDocument(array $file, int $itemId): string
    {
        $relativePath = $this->handleUpload($file, 'lpj', 'image', 5 * 1024 * 1024, "bukti_lpj_{$itemId}");
        return basename($relativePath);
    }

    /**
     * Handles 'surat pengantar' uploads.
     * @param array $file The file array from $_FILES.
     * @return string The filename of the uploaded file.
     * @throws UploadException
     */
    public function uploadSuratPengantar(array $file): string
    {
        $relativePath = $this->handleUpload($file, 'surat', 'document', 2 * 1024 * 1024, "surat_pengantar");
        return basename($relativePath);
    }

    /**
     * Generic document upload handler.
     * @param array $file The file array from $_FILES.
     * @param string $category The target folder (e.g., 'kak', 'rab').
     * @param string $description Optional description/prefix for filename.
     * @return array File info including path, filename, etc.
     * @throws UploadException
     */
    public function uploadDocument(array $file, string $category, string $description = ''): array
    {
        // Default to 'document' type (PDF, DOC) and 5MB limit
        $publicPath = $this->handleUpload($file, $category, 'document', 5 * 1024 * 1024, $description);
        
        // Return detailed info as expected by UploadController
        return [
            'filename' => basename($publicPath),
            'original_name' => $file['name'],
            'relative_path' => str_replace('/docutrack/public/uploads/', '', $publicPath), // Store relative path in DB
            'size' => $file['size'],
            'size_formatted' => $this->formatFileSize($file['size']),
            'mime_type' => $file['type']
        ];
    }

    /**
     * Format file size.
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $exp = floor(log($bytes) / log(1024));
        return sprintf('%.2f %s', $bytes / pow(1024, $exp), $units[$exp]);
    }

    /**
     * Get the base upload path.
     * @return string
     */
    public function getUploadBasePath(): string
    {
        return $this->uploadBasePath;
    }

    /**
     * Get file information.
     * @param string $filePath Absolute path to file.
     * @return array
     */
    public function getFileInfo(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new UploadException('File not found: ' . $filePath);
        }

        return [
            'size' => filesize($filePath),
            'mime_type' => mime_content_type($filePath),
            'extension' => pathinfo($filePath, PATHINFO_EXTENSION)
        ];
    }

    /**
     * Delete a file.
     * @param string $relativePath Relative path stored in DB (e.g. 'kak/file.pdf').
     * @return bool
     */
    public function delete(string $relativePath): bool
    {
        // Handle both relative path from DB or full path
        if (strpos($relativePath, $this->uploadBasePath) === 0) {
            $fullPath = $relativePath;
        } else {
            // Remove leading slashes or /docutrack/public/uploads/ prefix if present
            $cleanPath = str_replace('/docutrack/public/uploads/', '', $relativePath);
            $fullPath = $this->uploadBasePath . '/' . ltrim($cleanPath, '/');
        }

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Core upload handling logic.
     */
    private function handleUpload(array $file, string $category, string $fileType, int $maxSize, string $fileNamePrefix = ''): string
    {
        $this->validateFile($file, $this->allowedMimes[$fileType] ?? [], $maxSize);

        $uploadDir = $this->uploadBasePath . '/' . $category;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = ($fileNamePrefix ? $fileNamePrefix . '_' : '') . time() . '_' . uniqid() . '.' . $ext;
        $destinationPath = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            throw new UploadException('Gagal menyimpan file.');
        }

        return '/docutrack/public/uploads/' . $category . '/' . $filename;
    }

    /**
     * Validates a file.
     */
    private function validateFile(array $file, array $allowedMimes, int $maxSize): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new UploadException('Error unggah file (kode: ' . $file['error'] . ')');
        }
        if ($file['size'] > $maxSize) {
            throw new UploadException('Ukuran file melebihi batas maksimal.');
        }
        if (!in_array(finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['tmp_name']), $allowedMimes)) {
            throw new UploadException('Tipe file tidak diizinkan.');
        }
    }
}
