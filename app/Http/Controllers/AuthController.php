<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'        => 'required|email',
            'password'     => 'required',
            'captcha_code' => 'required',
        ]);

        // Validate CAPTCHA
        if (strtoupper($request->captcha_code) !== Session::get('captcha_code')) {
            return back()->with('login_error', 'Kode keamanan tidak valid.')->withInput();
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('login_error', 'Email atau password salah.')->withInput();
        }

        // Get role from Spatie (guard: sanctum)
        $role = $user->getRoleNames()->first(); // e.g. "Admin", "Bendahara"

        // Map Spatie role name → session role key (lowercase)
        $roleMap = [
            'Admin'      => 'admin',
            'Bendahara'  => 'bendahara',
            'Verifikator'=> 'verifikator',
            'Wadir'      => 'wadir',
            'PPK'        => 'ppk',
            'Direktur'   => 'direktur',
            'SuperAdmin' => 'superadmin',
        ];

        $sessionRole = $roleMap[$role] ?? 'admin';

        // Store session
        Session::put('user_id',    $user->user_id);
        Session::put('user_name',  $user->nama);
        Session::put('email',      $user->email);
        Session::put('role',       $sessionRole);
        Session::put('spatie_role', $role);
        Session::put('jurusan',    $user->nama_jurusan ?? '');

        // Redirect based on role
        return match($sessionRole) {
            'bendahara'   => redirect()->route('bendahara.dashboard'),
            'verifikator' => redirect()->route('verifikator.dashboard'),
            'ppk'         => redirect()->route('ppk.dashboard'),
            'wadir'       => redirect()->route('wadir.dashboard'),
            'direktur'    => redirect()->route('direktur.dashboard'),
            'superadmin'  => redirect()->route('superadmin.dashboard'),
            default       => redirect()->route('admin.dashboard'),
        };
    }

    public function logout()
    {
        Session::flush();
        return redirect('/');
    }
}
