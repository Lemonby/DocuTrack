<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total' => 125,
            'disetujui' => 80,
            'revisi' => 15,
            'ditolak' => 5,
            'menunggu' => 25,
        ];

        $system_health = [
            'db_connection' => true,
            'memory_usage' => '42.5 MB',
            'php_version' => PHP_VERSION,
        ];

        $monitoring_kegiatan = [
            [
                'pengusul' => 'Jane Smith',
                'prodi' => 'Teknik Informatika',
                'nama' => 'Lomba Karya Tulis Ilmiah',
                'status' => 'Menunggu',
                'created_at' => now()->subDays(2),
            ],
            [
                'pengusul' => 'John Doe',
                'prodi' => 'Teknik Elektro',
                'nama' => 'Seminar Robotika 2026',
                'status' => 'Disetujui',
                'created_at' => now()->subDays(5),
            ],
            [
                'pengusul' => 'Dewi Lestari',
                'prodi' => 'Akuntansi',
                'nama' => 'Olimpiade Akuntansi',
                'status' => 'Review',
                'created_at' => now()->subDays(3),
            ],
            [
                'pengusul' => 'Ahmad Fauzi',
                'prodi' => 'Teknik Informatika',
                'nama' => 'Pelatihan Dasar Coding',
                'status' => 'Ditolak',
                'created_at' => now()->subDays(1),
            ],
        ];

        $monitoring_lpj = [
            [
                'nama_kegiatan' => 'Workshop UI/UX',
                'pengusul' => 'Alice Wong',
                'total_realisasi' => 5000000,
                'status_lpj' => 'Menunggu Verifikasi',
            ],
            [
                'nama_kegiatan' => 'Seminar Cyber Security',
                'pengusul' => 'Rizky Pratama',
                'total_realisasi' => 7500000,
                'status_lpj' => 'Disetujui',
            ],
            [
                'nama_kegiatan' => 'Kunjungan Industri 2026',
                'pengusul' => 'Siti Aminah',
                'total_realisasi' => 12000000,
                'status_lpj' => 'Revisi',
            ],
        ];

        $server_load = [
            'cpu' => 24,
            'ram' => 45,
            'disk' => 68,
            'traffic' => '1.2 GB/s'
        ];

        $recent_logs = [
            ['time' => '10:45', 'event' => 'New Proposal Uploaded', 'user' => 'John Doe', 'status' => 'info'],
            ['time' => '10:42', 'event' => 'Database Sync Complete', 'user' => 'System', 'status' => 'success'],
            ['time' => '10:38', 'event' => 'Failed Login Attempt', 'user' => 'Admin_PPK', 'status' => 'warning'],
            ['time' => '10:35', 'event' => 'LPJ Verified by Verifikator', 'user' => 'Yovana', 'status' => 'success'],
            ['time' => '10:30', 'event' => 'Cache Purged', 'user' => 'SuperAdmin', 'status' => 'info'],
        ];

        $active_users = [
            ['name' => 'Yovana Ibnu Sina', 'role' => 'Super Admin', 'last_seen' => 'Just now'],
            ['name' => 'Muhammad Syafri', 'role' => 'Verifikator', 'last_seen' => '2m ago'],
            ['name' => 'Jane Smith', 'role' => 'Admin TI', 'last_seen' => '5m ago'],
        ];

        $security_threats = [
            ['type' => 'SQL Injection Attempt', 'ip' => '192.168.1.105', 'status' => 'Blocked', 'risk' => 'High'],
            ['type' => 'Brute Force Detection', 'ip' => '45.122.10.5', 'status' => 'Mitigated', 'risk' => 'Medium'],
            ['type' => 'XSS Payload Filtered', 'ip' => '103.44.12.9', 'status' => 'Isolated', 'risk' => 'Low'],
        ];

        $broadcasts = [
            ['title' => 'Scheduled Maintenance', 'body' => 'System will be offline on Sunday at 02:00 AM.', 'date' => '20 May 2026'],
            ['title' => 'Security Update v4.2', 'body' => 'New encryption protocols have been applied to document storage.', 'date' => '18 May 2026'],
        ];

        return view('superadmin.dashboard', compact(
            'stats', 
            'system_health', 
            'monitoring_kegiatan', 
            'monitoring_lpj',
            'server_load',
            'recent_logs',
            'active_users',
            'security_threats',
            'broadcasts'
        ));
    }

    public function getAiAnalysis()
    {
        return response()->json([
            'status' => 'success',
            'model' => 'GPT-4o / Gemini Pro',
            'data' => "Sistem dalam kondisi optimal.\nStabilitas database: 100%.\nPenggunaan memori efisien.\n\nInsight: Terdapat peningkatan usulan kegiatan sebesar 15% minggu ini.",
        ]);
    }
    public function monitoring()
    {
        return view('superadmin.monitoring', [
            'title' => 'Monitoring Sistem - Super Admin'
        ]);
    }
}
