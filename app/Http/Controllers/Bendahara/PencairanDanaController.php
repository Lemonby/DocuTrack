<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;

class PencairanDanaController extends Controller
{
    public function index()
    {
        $list_kak = [
            [
                'id' => 1101,
                'nama' => 'Workshop UI/UX Design Modern',
                'pengusul' => 'Rizki Pratama',
                'nim' => '2407411050',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-12',
                'status' => 'Belum Dicairkan'
            ],
            [
                'id' => 1102,
                'nama' => 'Seminar Internasional Blockchain',
                'pengusul' => 'Ahmad Fauzi',
                'nim' => '2407411052',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-13',
                'status' => 'Sudah Dicairkan'
            ],
            [
                'id' => 1103,
                'nama' => 'Pengadaan Alat Praktikum Elektro',
                'pengusul' => 'Bambang Sudarsono',
                'nim' => '2407411055',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Belum Dicairkan'
            ],
            [
                'id' => 1104,
                'nama' => 'Lomba Karya Tulis Ilmiah Nasional',
                'pengusul' => 'Dewi Lestari',
                'nim' => '2407411051',
                'prodi' => 'Akuntansi',
                'jurusan' => 'Akuntansi',
                'tanggal_pengajuan' => '2026-05-16',
                'status' => 'Belum Dicairkan'
            ],
        ];
        return view('bendahara.pencairan-dana.index', compact('list_kak'));
    }

    public function detail($id)
    {
        $id = (int) $id;

        // Status & pencairan per ID — menyerupai kondisi nyata dari DB
        $pencairan_map = [
            1101 => [
                'status'           => 'Menunggu',
                'jumlah_dicairkan' => 0,
                'boleh_cairkan'    => true,
                'lpj_status'       => 'Belum Ada',
                'riwayat'          => [],
            ],
            1102 => [
                'status'           => 'Dana Diberikan',
                'jumlah_dicairkan' => 2150000,
                'boleh_cairkan'    => false,
                'lpj_status'       => 'Disetujui',
                'riwayat'          => [
                    ['tanggal_pencairan' => '2026-05-17', 'termin' => 'Termin 1', 'nominal' => 1500000, 'catatan' => 'Pencairan tahap pertama'],
                    ['tanggal_pencairan' => '2026-05-20', 'termin' => 'Termin 2', 'nominal' => 650000,  'catatan' => 'Pencairan tahap akhir'],
                ],
            ],
            1103 => [
                'status'           => 'Dana Belum Diberikan Semua',
                'jumlah_dicairkan' => 1750000,
                'boleh_cairkan'    => true,
                'lpj_status'       => 'Menunggu Verifikasi',
                'riwayat'          => [
                    ['tanggal_pencairan' => '2026-05-18', 'termin' => 'Termin 1', 'nominal' => 1750000, 'catatan' => 'Pencairan uang muka'],
                ],
            ],
            1104 => [
                'status'           => 'Menunggu',
                'jumlah_dicairkan' => 0,
                'boleh_cairkan'    => true,
                'lpj_status'       => 'Belum Ada',
                'riwayat'          => [],
            ],
        ];

        $p = $pencairan_map[$id] ?? $pencairan_map[1101];
        $status           = $p['status'];
        $jumlah_dicairkan = $p['jumlah_dicairkan'];
        $boleh_cairkan    = $p['boleh_cairkan'];
        $riwayat_pencairan = $p['riwayat'];

        $rab_data = [
            'Belanja Barang' => [
                ['uraian' => 'Konsumsi',      'rincian' => 'Snack & Lunch Box',      'vol1' => 50, 'sat1' => 'Paket',  'vol2' => 1, 'sat2' => 'Kali', 'harga' => 35000],
                ['uraian' => 'ATK',           'rincian' => 'Kertas & Tinta Print',   'vol1' => 2,  'sat1' => 'Rim',    'vol2' => 1, 'sat2' => 'Kali', 'harga' => 55000],
                ['uraian' => 'Sertifikat',    'rincian' => 'Cetak Sertifikat Desain','vol1' => 50, 'sat1' => 'Lembar', 'vol2' => 1, 'sat2' => 'Kali', 'harga' => 5000],
            ],
            'Belanja Jasa' => [
                ['uraian' => 'Honor Pemateri','rincian' => 'Narasumber Ahli Eksternal','vol1' => 1, 'sat1' => 'Orang', 'vol2' => 4, 'sat2' => 'Jam', 'harga' => 500000],
            ],
        ];

        // Hitung total anggaran dari RAB
        $anggaran_disetujui = 0;
        foreach ($rab_data as $items) {
            foreach ($items as $item) {
                $anggaran_disetujui += $item['vol1'] * ($item['vol2'] ?? 1) * $item['harga'];
            }
        }
        $sisa_dana = $anggaran_disetujui - $jumlah_dicairkan;

        $iku_data = [
            'IKU 2 - Mahasiswa mendapat pengalaman di luar kampus',
            'IKU 4 - Praktisi mengajar di kampus',
            'IKU 7 - Kelas kolaborasi dengan mitra industri',
        ];

        // Tahapan Pelaksanaan per bulan
        $tahapan_pelaksanaan = [
            5 => 'Persiapan materi dan koordinasi pemateri.',
            6 => 'Pelaksanaan workshop dan evaluasi awal.',
            7 => 'Penyusunan laporan pertanggungjawaban.'
        ];

        // Indikator Keberhasilan per bulan
        $indikator_keberhasilan = [
            5 => ['target_persen' => 100, 'deskripsi' => 'Materi telah disetujui dan pemateri konfirmasi kehadiran.'],
            6 => ['target_persen' => 80,  'deskripsi' => 'Acara berjalan lancar dengan tingkat kepuasan peserta minimal 80%.'],
            7 => ['target_persen' => 100, 'deskripsi' => 'LPJ diserahkan tepat waktu dan disetujui tanpa revisi major.']
        ];

        $kegiatan_data = [
            'id'                    => $id,
            'nama_pengusul'         => 'Siti Aminah',
            'nim_nip'               => '2407411059',
            'nim_pengusul'          => '2407411059',
            'nama_pelaksana'        => 'Tim Mahasiswa TIK Semester 4 & 6',
            'nama_penanggung_jawab' => 'Andi Wijaya, S.T., M.Kom.',
            'nip_penanggung_jawab'  => '198501012010011001',
            'penanggung_jawab'      => 'Andi Wijaya, S.T., M.Kom.',
            'nip_pj'                => '198501012010011001',
            'jurusan'               => 'Teknik Informatika dan Komputer',
            'prodi'                 => 'D4 Teknik Informatika',
            'nama_kegiatan'         => 'Workshop UI/UX Design 2026',
            'wadir_tujuan'          => 'Wakil Direktur Bidang Kemahasiswaan',
            'penerima_manfaat'      => 'Mahasiswa TIK Semester 4 & 6',
            'gambaran_umum'         => 'Workshop ini dirancang untuk memberikan pemahaman mendalam tentang prinsip desain UI/UX kepada mahasiswa. Fokus pada User Research, Wireframing, dan Prototyping menggunakan Figma.',
            'metode_pelaksanaan'    => 'Sesi teori di pagi hari diikuti dengan workshop praktik di siang hari. Peserta akan bekerja dalam kelompok kecil untuk menyelesaikan proyek mini.',
            'tahapan_kegiatan'      => "1. Persiapan materi dan koordinasi pemateri.\n2. Publikasi acara dan pendaftaran peserta.\n3. Pelaksanaan workshop dan evaluasi awal.\n4. Penyusunan laporan pertanggungjawaban.",
            'tanggal_mulai'         => '2026-05-20',
            'tanggal_selesai'       => '2026-05-21',
        ];

        $lpj_status        = $p['lpj_status'];
        $riwayat_pencairan = $p['riwayat'];
        $kode_mak          = '521211.001.052.A.5211.001';
        $catatan_bendahara = '';

        return view('bendahara.pencairan-dana.detail', compact(
            'id', 'status', 'lpj_status', 'iku_data', 'rab_data', 'kegiatan_data',
            'tahapan_pelaksanaan', 'indikator_keberhasilan', 'anggaran_disetujui', 'jumlah_dicairkan',
            'sisa_dana', 'boleh_cairkan', 'riwayat_pencairan',
            'kode_mak', 'catatan_bendahara'
        ));
    }
}
