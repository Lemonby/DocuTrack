<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions grouped by domain
        $permissions = [
            // Kegiatan
            'kegiatan.create', 'kegiatan.view', 'kegiatan.edit', 'kegiatan.delete', 'kegiatan.submit',
            // KAK
            'kak.view', 'kak.edit', 'kak.download-pdf',
            // Telaah (review)
            'telaah.view', 'telaah.approve', 'telaah.reject', 'telaah.revise',
            // Pencairan
            'pencairan.view', 'pencairan.proses',
            // LPJ
            'lpj.view', 'lpj.submit', 'lpj.upload-bukti', 'lpj.verify', 'lpj.reject',
            // Monitoring
            'monitoring.view',
            // User management
            'user.view', 'user.create', 'user.edit', 'user.delete',
            // IKU
            'iku.view', 'iku.create', 'iku.edit', 'iku.delete',
            // AI
            'ai.monitoring',
            // Dashboard
            'dashboard.view',
            // Akun
            'akun.view', 'akun.edit',
            // Notifikasi
            'notifikasi.view', 'notifikasi.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Roles with assigned permissions
        $rolePermissions = [
            'Admin' => [
                'dashboard.view', 'akun.view', 'akun.edit', 'notifikasi.view', 'notifikasi.manage',
                'kegiatan.create', 'kegiatan.view', 'kegiatan.edit', 'kegiatan.delete', 'kegiatan.submit',
                'kak.view', 'kak.edit', 'kak.download-pdf',
                'lpj.view', 'lpj.submit', 'lpj.upload-bukti',
            ],
            'Verifikator' => [
                'dashboard.view', 'akun.view', 'akun.edit', 'notifikasi.view', 'notifikasi.manage',
                'telaah.view', 'telaah.approve', 'telaah.reject', 'telaah.revise',
                'kegiatan.view', 'kak.view',
            ],
            'PPK' => [
                'dashboard.view', 'akun.view', 'akun.edit', 'notifikasi.view', 'notifikasi.manage',
                'telaah.view', 'telaah.approve', 'telaah.reject', 'telaah.revise',
                'kegiatan.view', 'monitoring.view',
            ],
            'Wadir' => [
                'dashboard.view', 'akun.view', 'akun.edit', 'notifikasi.view', 'notifikasi.manage',
                'telaah.view', 'telaah.approve', 'telaah.reject', 'telaah.revise',
                'kegiatan.view', 'monitoring.view',
            ],
            'Bendahara' => [
                'dashboard.view', 'akun.view', 'akun.edit', 'notifikasi.view', 'notifikasi.manage',
                'pencairan.view', 'pencairan.proses',
                'lpj.view', 'lpj.verify', 'lpj.reject',
            ],
            'SuperAdmin' => array_values($permissions), // all permissions
            'Direktur' => [
                'dashboard.view', 'akun.view', 'akun.edit', 'notifikasi.view',
                'monitoring.view', 'kegiatan.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'sanctum']);
            $role->syncPermissions($perms);
        }
    }
}
