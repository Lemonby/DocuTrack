<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsulanController extends Controller
{
    public function index()
    {
        // Mock data
        $list_usulan = [
            [
                'id' => 101,
                'nama' => 'Pengadaan Server Lab Komputer',
                'nama_mahasiswa' => 'Yovana Ibnu Sina',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(1),
                'status' => 'Menunggu'
            ],
            [
                'id' => 102,
                'nama' => 'Penyelenggaraan Lomba Coding',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(3),
                'status' => 'Revisi'
            ],
            [
                'id' => 103,
                'nama' => 'Seminar Nasional Cybersecurity',
                'nama_mahasiswa' => 'Dewi Lestari',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(5),
                'status' => 'Disetujui'
            ],
            [
                'id' => 104,
                'nama' => 'Workshop UI/UX Design Modern',
                'nama_mahasiswa' => 'Rizky Pratama',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(7),
                'status' => 'Ditolak'
            ],
            [
                'id' => 105,
                'nama' => 'Pengadaan Alat Lab Robotik',
                'nama_mahasiswa' => 'Siti Aminah',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => now()->subDays(2),
                'status' => 'Menunggu'
            ],
            [
                'id' => 106,
                'nama' => 'Pelatihan Jurnalistik Kampus',
                'nama_mahasiswa' => 'Budi Santoso',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tanggal_pengajuan' => now()->subDays(4),
                'status' => 'Disetujui'
            ],
        ];
        return view('admin.usulan.index', compact('list_usulan'));
    }

    public function show($id)
    {
        // Simulate finding the item from the dashboard list
        $status_map = [
            1 => 'Menunggu',
            2 => 'Disetujui',
            3 => 'Menunggu',
            4 => 'Revisi',
            5 => 'Ditolak',
            101 => 'Menunggu',
            102 => 'Revisi',
            103 => 'Disetujui',
            104 => 'Ditolak'
        ];

        $status = $status_map[$id] ?? 'Menunggu';
        
        $iku_data = [
            'IKU 2 - Mahasiswa mendapat pengalaman di luar kampus',
            'IKU 4 - Praktisi mengajar di kampus'
        ];

        $rab_data = [
            'Belanja Barang' => [
                ['uraian' => 'Konsumsi', 'rincian' => 'Snack & Lunch Box', 'vol1' => 50, 'sat1' => 'Paket', 'vol2' => 1, 'sat2' => 'Kali', 'harga' => 35000],
                ['uraian' => 'ATK', 'rincian' => 'Kertas & Tinta Print', 'vol1' => 2, 'sat1' => 'Rim', 'vol2' => 1, 'sat2' => 'Kali', 'harga' => 55000]
            ],
            'Belanja Jasa' => [
                ['uraian' => 'Honor Pemateri', 'rincian' => 'Narasumber Ahli', 'vol1' => 1, 'sat1' => 'Orang', 'vol2' => 4, 'sat2' => 'Jam', 'harga' => 500000]
            ]
        ];

        $payout_data = [
            101 => ['payout_status' => 'Belum Ada', 'lpj_status' => 'Belum Ada', 'total_cair' => 0],
            103 => ['payout_status' => 'Termin 1 (50%)', 'lpj_status' => 'Menunggu Verifikasi', 'total_cair' => 2500000],
            106 => ['payout_status' => 'Lunas (100%)', 'lpj_status' => 'Disetujui', 'total_cair' => 4500000],
        ];
        $payout = $payout_data[$id] ?? ['payout_status' => 'Belum Ada', 'lpj_status' => 'Belum Ada', 'total_cair' => 0];

        $kegiatan_data = [
            'nama_pengusul' => 'Siti Aminah',
            'nim_nip' => '2407411059',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'D4 Teknik Informatika',
            'nama_kegiatan' => 'Workshop UI/UX Design 2026',
            'penanggung_jawab' => 'Andi Wijaya, S.T., M.Kom.',
            'nip_pj' => '198501012010011001',
            'wadir_tujuan' => 'Wakil Direktur Bidang Kemahasiswaan',
            'penerima_manfaat' => 'Mahasiswa TIK Semester 4 & 6',
            'gambaran_umum' => 'Workshop ini dirancang untuk memberikan pemahaman mendalam tentang prinsip desain UI/UX kepada mahasiswa. Fokus pada User Research, Wireframing, dan Prototyping menggunakan Figma.',
            'metode_pelaksanaan' => 'Sesi teori di pagi hari diikuti dengan workshop praktik di siang hari. Peserta akan bekerja dalam kelompok kecil untuk menyelesaikan proyek mini.',
            'kode_mak' => ($status === 'Disetujui') ? '521211.001.052.A.5211.001' : null,
            'payout_status' => $payout['payout_status'],
            'lpj_status' => $payout['lpj_status'],
            'total_cair' => $payout['total_cair']
        ];

        $tahapan_pelaksanaan = [
            '1' => 'Persiapan materi dan koordinasi pemateri.',
            '2' => 'Publikasi acara dan pendaftaran peserta.',
            '3' => 'Pelaksanaan workshop dan evaluasi awal.',
            '4' => 'Penyusunan laporan pertanggungjawaban.'
        ];

        $indikator_keberhasilan = [
            '1' => ['target_persen' => 100, 'deskripsi' => 'Materi telah disetujui dan pemateri konfirmasi kehadiran.'],
            '2' => ['target_persen' => 100, 'deskripsi' => 'Target 50 peserta terdaftar terpenuhi.'],
            '3' => ['target_persen' => 80, 'deskripsi' => 'Acara berjalan lancar dengan tingkat kepuasan peserta minimal 80%.'],
            '4' => ['target_persen' => 100, 'deskripsi' => 'LPJ diserahkan tepat waktu dan disetujui tanpa revisi major.']
        ];

        $catatan_revisi = ($status === 'Revisi') ? 'Rincian RAB untuk Honor Pemateri terlalu tinggi, mohon sesuaikan dengan standar SBM terbaru. Gambaran umum juga perlu diperjelas terkait output kegiatannya.' : null;

        return view('admin.usulan.detail', compact('id', 'status', 'iku_data', 'rab_data', 'kegiatan_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'catatan_revisi'));
    }

    public function edit($id)
    {
        $iku_data = [
            'Mendapat Pekerjaan', 
            'Kegiatan luar prodi'
        ];

        $rab_data = [
            'Belanja Barang' => [
                ['uraian' => 'Konsumsi', 'rincian' => 'Snack & Lunch Box', 'vol1' => 50, 'sat1' => 'Paket', 'vol2' => 1, 'sat2' => 'Kali', 'harga' => 35000],
                ['uraian' => 'ATK', 'rincian' => 'Kertas & Tinta Print', 'vol1' => 2, 'sat1' => 'Rim', 'vol2' => 1, 'sat2' => 'Kali', 'harga' => 55000]
            ],
            'Belanja Jasa' => [
                ['uraian' => 'Honor Pemateri', 'rincian' => 'Narasumber Ahli', 'vol1' => 1, 'sat1' => 'Orang', 'vol2' => 4, 'sat2' => 'Jam', 'harga' => 500000]
            ]
        ];

        $kegiatan_data = [
            'nama_pengusul' => 'Siti Aminah',
            'nim_nip' => '2407411059',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'D4 Teknik Informatika',
            'nama_kegiatan' => 'Workshop UI/UX Design 2026',
            'penanggung_jawab' => 'Andi Wijaya, S.T., M.Kom.',
            'nip_pj' => '198501012010011001',
            'wadir_tujuan' => 'Wakil Direktur Bidang Kemahasiswaan',
            'penerima_manfaat' => 'Mahasiswa TIK Semester 4 & 6',
            'gambaran_umum' => 'Workshop ini dirancang untuk memberikan pemahaman mendalam tentang prinsip desain UI/UX kepada mahasiswa. Fokus pada User Research, Wireframing, dan Prototyping menggunakan Figma.',
            'metode_pelaksanaan' => 'Sesi teori di pagi hari diikuti dengan workshop praktik di siang hari. Peserta akan bekerja dalam kelompok kecil untuk menyelesaikan proyek mini.',
            'kode_mak' => null
        ];

        $tahapan_pelaksanaan = [
            '1' => 'Persiapan materi dan koordinasi pemateri.',
            '2' => 'Publikasi acara dan pendaftaran peserta.',
            '3' => 'Pelaksanaan workshop dan evaluasi awal.',
            '4' => 'Penyusunan laporan pertanggungjawaban.'
        ];

        $indikator_keberhasilan = [
            '1' => ['target_persen' => 100, 'deskripsi' => 'Materi telah disetujui dan pemateri konfirmasi kehadiran.'],
            '2' => ['target_persen' => 100, 'deskripsi' => 'Target 50 peserta terdaftar terpenuhi.'],
            '3' => ['target_persen' => 80, 'deskripsi' => 'Acara berjalan lancar dengan tingkat kepuasan peserta minimal 80%.'],
            '4' => ['target_persen' => 100, 'deskripsi' => 'LPJ diserahkan tepat waktu dan disetujui tanpa revisi major.']
        ];

        $status = 'Revisi';
        $catatan_revisi = 'Rincian RAB untuk Honor Pemateri terlalu tinggi, mohon sesuaikan dengan standar SBM terbaru. Gambaran umum juga perlu diperjelas terkait output kegiatannya.';

        return view('admin.usulan.edit', compact('id', 'status', 'iku_data', 'rab_data', 'kegiatan_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'catatan_revisi'));
    }

    public function store(Request $request)
    {
        // Will be implemented with DB integration
        return redirect()->route('admin.usulan.index')->with('success_message', 'Usulan berhasil diajukan!');
    }

    public function update(Request $request, $id)
    {
        // Will be implemented with DB integration
        return redirect()->route('admin.usulan.index')->with('success_message', 'Revisi usulan berhasil disimpan dan diajukan ulang!');
    }
}
