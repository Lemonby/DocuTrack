<?php

namespace Tests\Feature;

use App\Services\FileUploadService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    private FileUploadService $fileUploadService;

    private string $tempUploadPath;

    protected function setUp(): void
    {
        parent::setUp();

        // Define DOCUTRACK_ROOT if not defined
        if (! defined('DOCUTRACK_ROOT')) {
            define('DOCUTRACK_ROOT', base_path());
        }

        $this->fileUploadService = new FileUploadService;
        $this->tempUploadPath = $this->fileUploadService->getUploadBasePath();
    }

    protected function tearDown(): void
    {
        // Clean up any uploaded test files
        if (is_dir($this->tempUploadPath)) {
            $this->deleteDirRecursively($this->tempUploadPath);
        }
        parent::tearDown();
    }

    private function deleteDirRecursively(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->deleteDirRecursively($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Memastikan uploadProfileImage berhasil memproses dan memvalidasi file gambar.
     */
    #[Test]
    #[TestDox('Memastikan uploadProfileImage berhasil memproses dan memvalidasi file gambar')]
    public function test_upload_profile_image(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_img_');
        file_put_contents($tempFile, 'fake-image-content');

        $fileArray = [
            'name' => 'profile.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $tempFile,
            'error' => UPLOAD_ERR_OK,
            'size' => 100,
        ];

        $publicPath = $this->fileUploadService->uploadProfileImage($fileArray);

        $this->assertStringContainsString('/docutrack/public/uploads/profiles/', $publicPath);
        $absolutePath = DOCUTRACK_ROOT.str_replace('/docutrack', '', $publicPath);
        $this->assertFileExists($absolutePath);
        $this->assertEquals('fake-image-content', file_get_contents($absolutePath));
    }

    /**
     * Memastikan uploadHeaderBackground berhasil memproses gambar dan mengembalikan url CSS.
     */
    #[Test]
    #[TestDox('Memastikan uploadHeaderBackground berhasil memproses gambar dan mengembalikan url CSS')]
    public function test_upload_header_background(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_img_');
        file_put_contents($tempFile, 'fake-image-content');

        $fileArray = [
            'name' => 'background.png',
            'type' => 'image/png',
            'tmp_name' => $tempFile,
            'error' => UPLOAD_ERR_OK,
            'size' => 200,
        ];

        $cssUrl = $this->fileUploadService->uploadHeaderBackground($fileArray);

        $this->assertStringStartsWith("url('", $cssUrl);
        $this->assertStringContainsString('/docutrack/public/uploads/profiles/', $cssUrl);
    }

    /**
     * Memastikan uploadLpjDocument berhasil memproses berkas bukti LPJ.
     */
    #[Test]
    #[TestDox('Memastikan uploadLpjDocument berhasil memproses berkas bukti LPJ')]
    public function test_upload_lpj_document(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_img_');
        file_put_contents($tempFile, 'fake-image-content');

        $fileArray = [
            'name' => 'bukti.png',
            'type' => 'image/png',
            'tmp_name' => $tempFile,
            'error' => UPLOAD_ERR_OK,
            'size' => 150,
        ];

        $filename = $this->fileUploadService->uploadLpjDocument($fileArray, 45);

        $this->assertStringContainsString('bukti_lpj_45_', $filename);
        $this->assertFileExists($this->tempUploadPath.'/lpj/'.$filename);
    }

    /**
     * Memastikan uploadSuratPengantar berhasil mengupload dokumen surat pengantar.
     */
    #[Test]
    #[TestDox('Memastikan uploadSuratPengantar berhasil mengupload dokumen surat pengantar')]
    public function test_upload_surat_pengantar(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_doc_pdf_');
        file_put_contents($tempFile, 'fake-pdf-content');

        $fileArray = [
            'name' => 'surat.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tempFile,
            'error' => UPLOAD_ERR_OK,
            'size' => 300,
        ];

        $filename = $this->fileUploadService->uploadSuratPengantar($fileArray);

        $this->assertStringContainsString('surat_pengantar_', $filename);
        $this->assertFileExists($this->tempUploadPath.'/surat/'.$filename);
    }

    /**
     * Memastikan uploadDocument generik berhasil mengupload berkas dan menghapusnya.
     */
    #[Test]
    #[TestDox('Memastikan uploadDocument generik berhasil mengupload berkas dan menghapusnya')]
    public function test_upload_document_generic_and_delete(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'test_doc_pdf_');
        file_put_contents($tempFile, 'fake-pdf-content');

        $fileArray = [
            'name' => 'proposal.pdf',
            'type' => 'application/pdf',
            'tmp_name' => $tempFile,
            'error' => UPLOAD_ERR_OK,
            'size' => 16,
        ];

        $info = $this->fileUploadService->uploadDocument($fileArray, 'kak', 'kak_proposal');

        $this->assertEquals('proposal.pdf', $info['original_name']);
        $this->assertStringContainsString('kak_proposal_', $info['filename']);
        $this->assertEquals('16.00 B', $info['size_formatted']);

        $absolutePath = $this->tempUploadPath.'/kak/'.$info['filename'];
        $this->assertFileExists($absolutePath);

        // Test getFileInfo
        $fileInfo = $this->fileUploadService->getFileInfo($absolutePath);
        $this->assertEquals(16, $fileInfo['size']);

        // Test delete
        $deleted = $this->fileUploadService->delete('kak/'.$info['filename']);
        $this->assertTrue($deleted);
        $this->assertFileDoesNotExist($absolutePath);
    }
}

namespace App\Services;

function move_uploaded_file($filename, $destination)
{
    return copy($filename, $destination);
}

function finfo_file($finfo, $filename)
{
    if (str_contains($filename, 'pdf')) {
        return 'application/pdf';
    }

    return 'image/jpeg';
}
