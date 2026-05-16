<?php

namespace App\Http\Controllers\Verifikator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TelaahController extends Controller
{
    public function index()
    {
        $list_usulan = [
            [
                'id' => 1001,
                'nama' => 'Peningkatan Kompetensi AI Mahasiswa TI',
                'pengusul' => 'Yovana Ibnu Sina',
                'nim' => '2407411059',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-14',
                'status' => 'Menunggu Verifikasi'
            ],
            [
                'id' => 1002,
                'nama' => 'Workshop UI/UX Design Modern',
                'pengusul' => 'Ahmad Fauzi',
                'nim' => '2407411050',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Review'
            ],
            [
                'id' => 1003,
                'nama' => 'Seminar Internasional Digital Transformation',
                'pengusul' => 'Budi Santoso',
                'nim' => '2407411003',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2026-05-15',
                'status' => 'Menunggu Verifikasi'
            ],
            [
                'id' => 1004,
                'nama' => 'Lomba Karya Tulis Ilmiah 2026',
                'pengusul' => 'Siti Aminah',
                'nim' => '2407411080',
                'prodi' => 'Teknik Grafika',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tanggal_pengajuan' => '2026-05-16',
                'status' => 'Menunggu Verifikasi'
            ],
        ];
        $jurusan_list = [
            'Teknik Informatika dan Komputer',
            'Teknik Grafika dan Penerbitan',
            'Teknik Elektro',
            'Teknik Mesin',
            'Teknik Sipil',
            'Administrasi Niaga',
            'Akuntansi',
        ];
        return view('verifikator.telaah.index', compact('list_usulan', 'jurusan_list'));
    }

    public function show($id)
    {
        // Status map based on dummy IDs from Dashboard and Riwayat
        $status_map = [
            // From Dashboard (Pending/Process)
            1001 => 'Menunggu Verifikasi',
            1002 => 'Review',
            1003 => 'Menunggu Verifikasi',
            1004 => 'Menunggu Verifikasi',
            
            // Sync exactly with RiwayatController
            601 => 'Disetujui',
            602 => 'Disetujui',
            603 => 'Revisi',
            604 => 'Disetujui',
            605 => 'Ditolak',
            606 => 'Revisi',
            607 => 'Disetujui',
            608 => 'Disetujui',
            609 => 'Ditolak',
            610 => 'Revisi',
        ];
        $status = $status_map[$id] ?? 'Menunggu';

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
            'nama_pengusul' => 'Yovana Ibnu Sina',
            'nim_nip' => '2407411059',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'D4 Teknik Informatika',
            'nama_kegiatan' => 'Peningkatan Kompetensi AI Mahasiswa TI',
            'wadir_tujuan' => 'Wakil Direktur Bidang Kemahasiswaan',
            'penerima_manfaat' => 'Mahasiswa TIK Semester 4 & 6',
            'gambaran_umum' => 'Workshop ini dirancang untuk memberikan pemahaman mendalam tentang prinsip desain UI/UX kepada mahasiswa. Fokus pada User Research, Wireframing, dan Prototyping menggunakan Figma.',
            'metode_pelaksanaan' => 'Sesi teori di pagi hari diikuti dengan workshop praktik di siang hari. Peserta akan bekerja dalam kelompok kecil untuk menyelesaikan proyek mini.'
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

        $catatan_revisi = null;

        return view('verifikator.telaah.show', compact('id', 'status', 'iku_data', 'rab_data', 'kegiatan_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'catatan_revisi'));
    }

    public function store(Request $request, $id)
    {
        // Handle review submission
        return redirect()->route('verifikator.telaah.index')->with('success', 'Telaah berhasil disimpan.');
    }
}
