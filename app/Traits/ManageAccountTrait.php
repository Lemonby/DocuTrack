<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

trait ManageAccountTrait
{
    public function index()
    {
        $user = User::findOrFail(Session::get('user_id'));
        $userName = $user->nama;
        $userRole = Session::get('role', 'admin');
        
        $classParts = explode('\\', get_class($this));
        $roleFolder = strtolower($classParts[count($classParts) - 2]);
        
        return view($roleFolder . '.akun.index', compact('userName', 'userRole', 'user'));
    }

    public function update(Request $request)
    {
        $userId = Session::get('user_id');
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'nama' => ['sometimes', 'required', 'string', 'max:100'],
            'email' => ['sometimes', 'required', 'string', 'email:rfc,strict', 'max:255', 'unique:users,email,' . $userId . ',user_id'],
            'foto_profil' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('avatars', 'public');
            $user->foto_profil = $path;
            Session::put('profile_image', asset('storage/' . $path));
        }

        if ($request->has('nama')) {
            $user->nama = $validated['nama'];
            Session::put('user_name', $validated['nama']);
        }

        if ($request->has('email')) {
            $user->email = $validated['email'];
            Session::put('email', $validated['email']);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function changePassword(Request $request)
    {
        $userId = Session::get('user_id');
        $user = User::findOrFail($userId);

        $request->validate([
            'old_password' => ['required', 'string'],
            'new_password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'confirm_password' => ['required', 'string', 'same:new_password'],
        ], [
            'confirm_password.same' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Password lama salah.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah!');
    }
}
