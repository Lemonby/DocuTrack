<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->user_id,
                'nama' => $user->nama,
                'email' => $user->email,
                'jurusan' => $user->nama_jurusan ?? '-',
                'role' => $user->getRoleNames()->first() ?? 'Pengusul',
                'status' => $user->status ?? 'Aktif',
            ];
        });

        $list_jurusan = [
            'Teknik Informatika dan Komputer',
            'Teknik Elektro',
            'Teknik Sipil',
            'Teknik Mesin',
            'Teknik Grafika dan Penerbitan',
            'Akuntansi',
            'Administrasi Niaga',
            'Pascasarjana',
        ];

        $list_roles = [
            ['roleId' => 1, 'namaRole' => 'Pengusul'],
            ['roleId' => 2, 'namaRole' => 'Verifikator'],
            ['roleId' => 3, 'namaRole' => 'PPK'],
            ['roleId' => 4, 'namaRole' => 'Bendahara'],
            ['roleId' => 5, 'namaRole' => 'Wadir'],
            ['roleId' => 6, 'namaRole' => 'Direktur'],
            ['roleId' => 7, 'namaRole' => 'Admin'],
            ['roleId' => 8, 'namaRole' => 'SuperAdmin'],
        ];

        return view('superadmin.users.index', [
            'list_users' => $users,
            'list_jurusan' => $list_jurusan,
            'list_roles' => $list_roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'jurusan' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:50'],
            'password' => [
                'required',
                'string',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'nama_jurusan' => $validated['jurusan'],
            'password' => bcrypt($validated['password']),
            'status' => 'Aktif',
        ]);

        // Map 'Pengusul' to Spatie's 'Admin' role
        $roleToAssign = $validated['role'] === 'Pengusul' ? 'Admin' : $validated['role'];
        $user->assignRole($roleToAssign);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->user_id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'jurusan' => $user->nama_jurusan,
                    'role' => $roleToAssign,
                    'status' => $user->status,
                ],
                'message' => 'User berhasil ditambahkan.',
            ]);
        }

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:users,user_id'],
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$request->id.',user_id'],
            'jurusan' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:50'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ];
        }

        $validated = $request->validate($rules);

        $user = User::findOrFail($validated['id']);

        $updateData = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'nama_jurusan' => $validated['jurusan'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $user->update($updateData);

        // Map 'Pengusul' to Spatie's 'Admin' role
        $roleToAssign = $validated['role'] === 'Pengusul' ? 'Admin' : $validated['role'];
        $user->syncRoles([$roleToAssign]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->user_id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'jurusan' => $user->nama_jurusan,
                    'role' => $roleToAssign,
                    'status' => $user->status,
                ],
                'message' => 'Profil user berhasil diperbarui.',
            ]);
        }

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Profil user berhasil diperbarui.');
    }

    public function destroy($id, Request $request)
    {
        $user = User::findOrFail($id);
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus permanent.',
            ]);
        }

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatus($id, Request $request)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'Aktif' ? 'Non-Aktif' : 'Aktif';
        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $user->status,
                'message' => 'Status user '.$user->nama.' berhasil diubah menjadi '.$user->status,
            ]);
        }

        return redirect()->route('superadmin.users.index')
            ->with('success', 'Status user berhasil diubah.');
    }
}
