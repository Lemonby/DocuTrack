<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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
                'status' => 'Aktif', // Default mock status
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
        // Placeholder for user creation
        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil ditambahkan.');
    }
}
