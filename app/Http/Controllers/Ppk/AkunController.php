<?php

namespace App\Http\Controllers\Ppk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AkunController extends Controller
{
    public function index()
    {
        $userName = Session::get('user_name', 'PPK');
        $userRole = Session::get('role', 'ppk');
        return view('ppk.akun.index', compact('userName', 'userRole'));
    }

    public function update(Request $request)
    {
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        return back()->with('success', 'Password berhasil diubah.');
    }
}
