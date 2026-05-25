<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AkunController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Session::get('user_id'));
        $userName = $user->nama;
        $userRole = Session::get('role', 'verifikator');
        
        // Update session just in case
        Session::put('user_name', $user->nama);
        Session::put('email', $user->email);

        return view('verifikator.akun.index', compact('userName', 'userRole'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . Session::get('user_id') . ',user_id',
        ]);

        $user = User::findOrFail(Session::get('user_id'));
        $user->update([
            'nama' => $request->nama,
            'email' => $request->email,
        ]);

        Session::put('user_name', $request->nama);
        Session::put('email', $request->email);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ]);

        $user = User::findOrFail(Session::get('user_id'));

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->with('error_message', 'Password lama tidak sesuai!');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}

