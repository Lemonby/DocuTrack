<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\LogStatus;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        // Validate CAPTCHA (with master code 123456)
        $inputCaptcha = strtoupper($request->captcha_code);
        if ($inputCaptcha !== Session::get('captcha_code') && $inputCaptcha !== '123456') {
            return back()->with('login_error', 'Kode keamanan tidak valid.')->withInput();
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->with('login_error', 'Email atau password salah.')->withInput();
        }

        if ($user->status !== 'Aktif') {
            return back()->with('login_error', 'Akun Anda tidak aktif. Silakan hubungi admin.')->withInput();
        }

        // Get role from Spatie (guard: sanctum)
        $role = $user->getRoleNames()->first(); // e.g. "Admin", "Bendahara"

        // Map Spatie role name → session role key (lowercase)
        $roleMap = [
            'Admin' => 'admin',
            'Bendahara' => 'bendahara',
            'Verifikator' => 'verifikator',
            'Wadir' => 'wadir',
            'PPK' => 'ppk',
            'Direktur' => 'direktur',
            'SuperAdmin' => 'superadmin',
        ];

        $sessionRole = $roleMap[$role] ?? 'admin';

        // Store session
        Session::put('user_id', $user->user_id);
        Session::put('user_name', $user->nama);
        Session::put('email', $user->email);
        Session::put('role', $sessionRole);
        Session::put('spatie_role', $role);
        Session::put('jurusan', $user->nama_jurusan ?? '');

        // Set cookie indicating user has logged in (used for session expiration tracking)
        Cookie::queue('was_logged_in', '1', 525600); // 1 year expiry

        // Store login log in log_statuses
        LogStatus::create([
            'user_id' => $user->user_id,
            'tipe_log' => 'LOGIN',
            'status' => 'DIBACA',
            'konten_json' => [
                'judul' => 'Login Berhasil',
                'pesan' => "User {$user->nama} ({$role}) berhasil masuk ke sistem.",
                'link' => '#',
            ],
        ]);

        // Store activity log
        app(ActivityLogService::class)->log(
            userId: $user->user_id,
            action: 'LOGIN',
            category: 'authentication',
            entityType: 'User',
            entityId: $user->user_id,
            description: "User {$user->nama} ({$role}) berhasil masuk ke sistem via Web.",
            request: request()
        );

        // Redirect based on role
        return match ($sessionRole) {
            'bendahara' => redirect()->route('bendahara.dashboard'),
            'verifikator' => redirect()->route('verifikator.dashboard'),
            'ppk' => redirect()->route('ppk.dashboard'),
            'wadir' => redirect()->route('wadir.dashboard'),
            'direktur' => redirect()->route('direktur.dashboard'),
            'superadmin' => redirect()->route('superadmin.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }

    public function logout()
    {
        $userId = Session::get('user_id');
        $userName = Session::get('user_name');
        $role = Session::get('spatie_role');

        if ($userId) {
            app(ActivityLogService::class)->log(
                userId: $userId,
                action: 'LOGOUT',
                category: 'authentication',
                entityType: 'User',
                entityId: $userId,
                description: "User {$userName} ({$role}) berhasil keluar dari sistem via Web.",
                request: request()
            );
        }

        Session::flush();
        Cookie::queue(Cookie::forget('was_logged_in'));

        return redirect('/');
    }
}
