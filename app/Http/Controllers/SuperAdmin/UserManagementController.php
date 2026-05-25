<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::all()->map(function($user) {
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
            'Pascasarjana'
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
            'list_roles' => $list_roles
        ]);
    }

    public function create()
    {
        return view('superadmin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'jurusan' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:50'],
        ]);

        $password = Str::random(12);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'nama_jurusan' => $validated['jurusan'],
            'password' => $password,
            'status' => 'Aktif',
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil ditambahkan. Password sementara: ' . $password);
    }
}
