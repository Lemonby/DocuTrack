<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Download or View files from public storage.
     * Accessible only by authenticated users.
     */
    public function servePublicFile($folder, $filename)
    {
        $path = $folder . '/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        return Storage::disk('public')->response($path);
    }

    /**
     * Download or View files securely using encrypted paths.
     * Accessible only by authenticated users.
     */
    public function serveSecureFile(Request $request)
    {
        $encryptedPath = $request->query('path');
        if (!$encryptedPath) {
            abort(400, 'Parameter path tidak ditemukan.');
        }

        try {
            // Decrypt the file path using AES-256 (via Laravel Crypt)
            $path = Crypt::decryptString($encryptedPath);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(403, 'Tautan unduhan tidak valid atau telah dirusak.');
        }

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        return Storage::disk('public')->response($path);
    }
}
