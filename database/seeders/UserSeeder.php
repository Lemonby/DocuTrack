<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('1Password321!#');

        $users = [
            ['nama' => 'Admin TI', 'email' => 'adminti@gmail.com', 'nama_jurusan' => 'Teknik Informatika dan Komputer', 'role' => 'Admin'],
            ['nama' => 'Admin Teknik Elektro', 'email' => 'adminelektro@gmail.com', 'nama_jurusan' => 'Teknik Elektro', 'role' => 'Admin'],
            ['nama' => 'Admin Teknik Sipil', 'email' => 'adminsipil@gmail.com', 'nama_jurusan' => 'Teknik Sipil', 'role' => 'Admin'],
            ['nama' => 'Admin Teknik Mesin', 'email' => 'adminmesin@gmail.com', 'nama_jurusan' => 'Teknik Mesin', 'role' => 'Admin'],
            ['nama' => 'Admin Grafika dan Penerbitan', 'email' => 'admintgp@gmail.com', 'nama_jurusan' => 'Teknik Grafika dan Penerbitan', 'role' => 'Admin'],
            ['nama' => 'Admin Akuntansi', 'email' => 'adminakt@gmail.com', 'nama_jurusan' => 'Akuntansi', 'role' => 'Admin'],
            ['nama' => 'Admin Administrasi Niaga', 'email' => 'adminan@gmail.com', 'nama_jurusan' => 'Administrasi Niaga', 'role' => 'Admin'],
            ['nama' => 'Admin Pascasarjana', 'email' => 'adminpasca@gmail.com', 'nama_jurusan' => 'Pascasarjana', 'role' => 'Admin'],
            ['nama' => 'Verifikator', 'email' => 'verifikator@gmail.com', 'nama_jurusan' => null, 'role' => 'Verifikator'],
            ['nama' => 'Wakil Direktur', 'email' => 'wadir@gmail.com', 'nama_jurusan' => null, 'role' => 'Wadir'],
            ['nama' => 'PPK', 'email' => 'ppk@gmail.com', 'nama_jurusan' => null, 'role' => 'PPK'],
            ['nama' => 'Bendahara', 'email' => 'bendahara@gmail.com', 'nama_jurusan' => null, 'role' => 'Bendahara'],
            ['nama' => 'Direktur', 'email' => 'direktur@gmail.com', 'nama_jurusan' => null, 'role' => 'Direktur'],
            ['nama' => 'Super Admin', 'email' => 'superadmin@gmail.com', 'nama_jurusan' => null, 'role' => 'SuperAdmin'],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            $userData['password'] = $password;

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if (! $user->hasRole($role)) {
                $user->assignRole($role);
            }
        }
    }
}
