<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KriteriaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kriterias')->insert([
            [
                'kode_kriteria' => 'C1',
                'nama_kriteria' => 'Ketepatan Waktu Pelaksanaan',
                'bobot' => 0.2500,
                'tipe' => 'benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_kriteria' => 'C2',
                'nama_kriteria' => 'Ketepatan Penggunaan Anggaran',
                'bobot' => 0.2500,
                'tipe' => 'benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_kriteria' => 'C3',
                'nama_kriteria' => 'Mendukung IKU',
                'bobot' => 0.2500,
                'tipe' => 'benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_kriteria' => 'C4',
                'nama_kriteria' => 'Ketepatan Waktu Pengajuan LPJ',
                'bobot' => 0.2500,
                'tipe' => 'benefit',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Sync all existing activities scores into the database
        resolve(\App\Services\SpkMautService::class)->syncAllKegiatanScores();
    }
}
