<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AkunController extends Controller
{
    public function index()
    {
        $userName = Session::get('user_name', 'Verifikator');
        $userRole = Session::get('role', 'verifikator');
        return view('verifikator.akun.index', compact('userName', 'userRole'));
    }

    public function update(Request $request)
    {
        Session::put('user_name', $request->nama);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        // Handle password change
        return back()->with('success', 'Password berhasil diubah.');
    }
}
