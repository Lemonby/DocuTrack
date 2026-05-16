<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LpjController extends Controller
{
    public function index()
    {
        $list_lpj = [
            [
                'id' => 1,
                'nama' => 'Laporan Akhir Workshop AI',
                'nama_mahasiswa' => 'Rizky Pratama',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(10),
                'tenggatLpj' => now()->addDays(5),
                'status' => 'menunggu_upload'
            ],
            [
                'id' => 2,
                'nama' => 'LPJ Pelatihan Cloud Computing Dasar',
                'nama_mahasiswa' => 'Dewi Lestari',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(15),
                'tenggatLpj' => now()->subDays(1),
                'status' => 'menunggu'
            ],
            [
                'id' => 3,
                'nama' => 'Laporan Kegiatan Lomba Coding',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(20),
                'tenggatLpj' => now()->addDays(10),
                'status' => 'disetujui'
            ],
            [
                'id' => 4,
                'nama' => 'LPJ Kunjungan Industri 2026',
                'nama_mahasiswa' => 'Siti Aminah',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(25),
                'tenggatLpj' => now()->addDays(2),
                'status' => 'revisi'
            ],
            [
                'id' => 5,
                'nama' => 'LPJ Pameran Teknologi Tepat Guna',
                'nama_mahasiswa' => 'Budi Santoso',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => now()->subDays(5),
                'tenggatLpj' => now()->addDays(7),
                'status' => 'telah_direvisi'
            ],
            [
                'id' => 6,
                'nama' => 'LPJ Seminar Nasional Cybersecurity',
                'nama_mahasiswa' => 'Andi Wijaya',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => now()->subDays(3),
                'tenggatLpj' => now()->addDays(12),
                'status' => 'siap_submit'
            ],
        ];
        return view('admin.lpj.index', compact('list_lpj'));
    }

    public function detail(Request $request, $id)
    {
        $from = $request->query('from', 'index');
        // Simulate finding the LPJ
        // Sync with DashboardController IDs
        $status_map = [
            1 => 'menunggu_upload',
            2 => 'menunggu',
            3 => 'disetujui',
            4 => 'revisi',
            5 => 'telah_direvisi',
            6 => 'siap_submit',
        ];

        $status = $status_map[$id] ?? 'Draft';

        $kegiatan_nama = "Workshop Pengembangan Web 2026";
        
        // Mock data for RAB with realisasi if not draft
        $rab_items = [
            'Belanja Barang' => [
                [
                    'id' => 'it-1',
                    'uraian' => 'Sertifikat Peserta',
                    'rincian' => 'Cetak sertifikat desain khusus',
                    'vol1' => 50,
                    'sat1' => 'Lembar',
                    'vol2' => 1,
                    'sat2' => 'Kali',
                    'harga' => 5000,
                    'realisasi' => ($status != 'Draft' ? 5000 * 50 : 0),
                    'catatan_item' => ($status === 'Revisi' ? 'Foto sertifikat tidak jelas, mohon upload ulang.' : null)
                ],
                [
                    'id' => 'it-2',
                    'uraian' => 'Konsumsi Snack',
                    'rincian' => 'Snack box untuk peserta dan panitia',
                    'vol1' => 60,
                    'sat1' => 'Box',
                    'vol2' => 1,
                    'sat2' => 'Hari',
                    'harga' => 15000,
                    'realisasi' => ($status != 'Draft' ? 15000 * 60 : 0),
                    'catatan_item' => ($status === 'Revisi' ? 'Nota konsumsi belum distempel basah.' : null)
                ]
            ],
            'Belanja Jasa' => [
                [
                    'id' => 'it-3',
                    'uraian' => 'Honor Narasumber',
                    'rincian' => 'Narasumber eksternal dari industri',
                    'vol1' => 1,
                    'sat1' => 'Orang',
                    'vol2' => 2,
                    'sat2' => 'Jam',
                    'harga' => 500000,
                    'realisasi' => ($status != 'Draft' ? 500000 * 2 : 0),
                    'catatan_item' => null
                ]
            ]
        ];

        $catatan_revisi = ($status === 'Revisi') ? 'Bukti nota untuk konsumsi tidak terbaca dengan jelas. Mohon unggah ulang foto nota yang lebih terang.' : null;
        $prodi = "D4 Teknik Informatika";
        $kode_mak = "521211.001.052.A.5211.001";

        return view('admin.lpj.detail', compact('id', 'status', 'rab_items', 'kegiatan_nama', 'catatan_revisi', 'from', 'prodi', 'kode_mak'));
    }

    public function store(Request $request)
    {
        // Placeholder for store logic
        return redirect()->route('admin.lpj.index')->with('success', 'LPJ berhasil diajukan ke Bendahara.');
    }
}
