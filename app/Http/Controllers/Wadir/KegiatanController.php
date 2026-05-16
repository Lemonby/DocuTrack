<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $list_kegiatan = [
            ['id' => 101, 'nama' => 'Sertifikasi IT Internasional', 'pengusul' => 'Ahmad Fauzi', 'nim' => '2407411001', 'prodi' => 'TI', 'jurusan' => 'Teknik Informatika dan Komputer', 'tanggal_pengajuan' => '2026-05-10', 'status' => 'Menunggu'],
            ['id' => 102, 'nama' => 'Seminar Digital Transformation', 'pengusul' => 'Budi Santoso', 'nim' => '2407411003', 'prodi' => 'Elektro', 'jurusan' => 'Teknik Elektro', 'tanggal_pengajuan' => '2026-05-14', 'status' => 'Menunggu'],
        ];
        $jurusan_list = ['Teknik Informatika dan Komputer', 'Teknik Grafika dan Penerbitan', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil', 'Administrasi Niaga', 'Akuntansi'];
        return view('wadir.kegiatan.index', compact('list_kegiatan', 'jurusan_list'));
    }

    public function show($id)
    {
        $status = 'Menunggu';
        $iku_data = ['Mendapat Pekerjaan', 'Kegiatan luar prodi'];
        $rab_data = [
            'Belanja Barang' => [
                ['uraian' => 'Konsumsi', 'rincian' => 'Snack & Lunch Box', 'vol1' => 50, 'sat1' => 'Paket', 'vol2' => 1, 'sat2' => 'Kali', 'harga' => 35000],
            ],
        ];

        $kegiatan_data = [
            'nama_pengusul' => 'Yovana Ibnu Sina',
            'nim_nip' => '2407411059',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'D4 Teknik Informatika',
            'nama_kegiatan' => 'Peningkatan Kompetensi AI Mahasiswa TI',
            'mak_code' => '521211.001.052.A.524111',
            'wadir_tujuan' => 'Wakil Direktur Bidang Kemahasiswaan',
            'penerima_manfaat' => 'Mahasiswa TIK Semester 4 & 6',
            'gambaran_umum' => 'Workshop ini dirancang untuk memberikan pemahaman mendalam tentang prinsip desain UI/UX kepada mahasiswa.',
            'metode_pelaksanaan' => 'Sesi teori di pagi hari diikuti dengan workshop praktik di siang hari.'
        ];

        $tahapan_pelaksanaan = ['1' => 'Persiapan materi dan koordinasi pemateri.'];
        $indikator_keberhasilan = ['1' => ['target_persen' => 100, 'deskripsi' => 'Materi telah disetujui.']];
        $catatan_revisi = null;

        return view('wadir.kegiatan.show', compact('id', 'status', 'iku_data', 'rab_data', 'kegiatan_data', 'tahapan_pelaksanaan', 'indikator_keberhasilan', 'catatan_revisi'));
    }

    public function store(Request $request, $id)
    {
        return redirect()->route('wadir.dashboard')->with('success', 'Persetujuan Wadir berhasil disimpan.');
    }
}
