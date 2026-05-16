<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LpjController extends Controller
{
    public function index()
    {
        $list_lpj = [
            [
                'id' => 1301,
                'nama' => 'LPJ - Bakti Sosial Mahasiswa 2026',
                'pengusul' => 'Andi Wijaya',
                'nim' => '2407411060',
                'prodi' => 'Teknik Elektro',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => '2026-05-11',
                'deadline' => '2026-05-25',
                'status' => 'Menunggu Verifikasi'
            ],
            [
                'id' => 1302,
                'nama' => 'LPJ - Kunjungan Industri PT. Digital Jaya',
                'pengusul' => 'Santi Kurnia',
                'nim' => '2407411061',
                'prodi' => 'Administrasi Niaga',
                'jurusan' => 'Administrasi Niaga',
                'tanggal_pengajuan' => '2026-05-13',
                'deadline' => '2026-05-27',
                'status' => 'Revisi'
            ],
            [
                'id' => 1303,
                'nama' => 'LPJ - Workshop Mobile Development',
                'pengusul' => 'Rizky Pratama',
                'nim' => '2407411062',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-15',
                'deadline' => '2026-05-29',
                'status' => 'Telah Direvisi'
            ],
            [
                'id' => 1304,
                'nama' => 'LPJ - Pengadaan Alat Lab Komputer',
                'pengusul' => 'Budi Santoso',
                'nim' => '2407411063',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-16',
                'deadline' => '2026-05-30',
                'status' => 'Disetujui'
            ],
            [
                'id' => 1305,
                'nama' => 'LPJ - Seminar Nasional Teknologi 4.0',
                'pengusul' => 'Dewi Lestari',
                'nim' => '2407411064',
                'prodi' => 'Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => '2026-05-10',
                'deadline' => '2026-05-24',
                'status' => 'Telah Direvisi'
            ],
            [
                'id' => 1306,
                'nama' => 'LPJ - Lomba Inovasi Mahasiswa',
                'pengusul' => 'Fajar Ramadhan',
                'nim' => '2407411065',
                'prodi' => 'Teknik Mesin',
                'jurusan' => 'Teknik Mesin',
                'tanggal_pengajuan' => '2026-05-14',
                'deadline' => '2026-05-28',
                'status' => 'Revisi'
            ],
        ];
        return view('bendahara.lpj.index', compact('list_lpj'));
    }

    public function detail(Request $request, $id)
    {
        $id = (int) $id;
        $from = $request->query('from', 'index');
        
        $status_map = [
            1301 => 'Menunggu Verifikasi',
            1302 => 'Revisi',
            1303 => 'Telah Direvisi',
            1304 => 'Disetujui',
            1305 => 'Telah Direvisi',
            1306 => 'Revisi',
        ];
        $status = $status_map[$id] ?? 'Menunggu Verifikasi';

        $kegiatan_data = [
            'id'                    => $id,
            'nama_pengusul'         => 'Andi Wijaya',
            'nim_pengusul'          => '2407411060',
            'nama_pelaksana'        => 'Himpunan Mahasiswa Elektro',
            'nama_penanggung_jawab' => 'Ir. Heru Susanto, M.T.',
            'nip_penanggung_jawab'  => '197005151998031002',
            'jurusan'               => 'Teknik Elektro',
            'prodi'                 => 'D3 Teknik Elektro',
            'nama_kegiatan'         => 'Bakti Sosial Mahasiswa 2026',
            'wadir_tujuan'          => 'Wakil Direktur Bidang Kemahasiswaan',
            'penerima_manfaat'      => 'Warga Desa Sukamaju',
            'tanggal_mulai'         => '2026-05-20',
            'tanggal_selesai'       => '2026-05-22',
        ];

        $kode_mak = 'MAK/2026/1301/ELK';
        
        $rab_items = [
            'Belanja Barang' => [
                [
                    'id' => 1,
                    'uraian' => 'Paket Sembako',
                    'rincian' => 'Beras, Minyak, Gula',
                    'vol1' => 100,
                    'sat1' => 'Paket',
                    'vol2' => 1,
                    'sat2' => 'Kali',
                    'harga' => 150000,
                    'realisasi' => 15000000,
                    'keterangan' => 'Sesuai nota Toko Berkah',
                    'catatan_item' => ''
                ],
                [
                    'id' => 2,
                    'uraian' => 'Konsumsi Panitia',
                    'rincian' => 'Nasi Kotak 3 Hari',
                    'vol1' => 20,
                    'sat1' => 'Orang',
                    'vol2' => 3,
                    'sat2' => 'Hari',
                    'harga' => 25000,
                    'realisasi' => 1450000, // Ada sedikit selisih hemat
                    'keterangan' => 'Nota RM Sederhana',
                    'catatan_item' => 'Lampirkan daftar hadir panitia'
                ]
            ],
            'Belanja Jasa' => [
                [
                    'id' => 3,
                    'uraian' => 'Sewa Tenda',
                    'rincian' => 'Tenda uk 4x6 1 set',
                    'vol1' => 1,
                    'sat1' => 'Set',
                    'vol2' => 3,
                    'sat2' => 'Hari',
                    'harga' => 500000,
                    'realisasi' => 1500000,
                    'keterangan' => 'Kwitansi Sewa Jaya',
                    'catatan_item' => ''
                ]
            ]
        ];

        // Hitung total
        $anggaran_disetujui = 0;
        $anggaran_realisasi = 0;
        foreach ($rab_items as $cat => $items) {
            foreach ($items as $item) {
                $anggaran_disetujui += $item['vol1'] * ($item['vol2'] ?? 1) * $item['harga'];
                $anggaran_realisasi += $item['realisasi'];
            }
        }

        $iku_data = ['IKU 2 - Mahasiswa berkegiatan di luar kampus'];
        $catatan_revisi = ($status == 'Revisi') ? 'Nota pembelian sembako belum dilampirkan.' : null;

        return view('bendahara.lpj.detail', compact(
            'id', 'status', 'rab_items', 'kegiatan_data', 'catatan_revisi', 
            'from', 'kode_mak', 'anggaran_disetujui', 'anggaran_realisasi', 'iku_data'
        ));
    }
}
