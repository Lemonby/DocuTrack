@extends('layouts.app')

@section('title', 'Detail Riwayat Verifikasi')

@section('content')
@php
    if (!function_exists('fmtRp')) {
        function fmtRp($n) { return 'Rp ' . number_format($n??0,0,',','.'); }
    }
    function displayValue($value, $placeholder = 'Belum diisi') {
        return !empty($value) ? htmlspecialchars($value) : '<span class="text-gray-400 italic">' . $placeholder . '</span>';
    }
    function fmtDateIndo($date) {
        if (!$date) return '-';
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $d = strtotime($date);
        return date('d', $d) . ' ' . $months[(int)date('m', $d)] . ' ' . date('Y', $d);
    }
    $statusColor = match(strtolower($status??'')) {
        'dana diberikan','disetujui','selesai' => 'emerald',
        'revisi' => 'amber',
        'ditolak' => 'rose',
        default => 'violet'
    };
@endphp

<main class="main-content font-poppins p-4 sm:p-6 lg:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full animate-fade-in">

    {{-- Alert Status --}}
    @if(strtolower($status??'') === 'revisi')
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-xl shadow-sm">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 text-lg mt-0.5"></i>
            <div>
                <h3 class="text-amber-800 font-bold text-sm">Catatan Revisi</h3>
                <p class="text-amber-700 text-xs mt-1">{{ $catatan_revisi ?? 'Dokumen ini memerlukan perbaikan.' }}</p>
            </div>
        </div>
    </div>
    @elseif(in_array(strtolower($status??''), ['dana diberikan','disetujui','selesai']))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded-r-xl shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check text-emerald-600"></i>
            </div>
            <div>
                <h3 class="text-emerald-800 font-bold text-sm">Proses Selesai</h3>
                <p class="text-emerald-700 text-xs">Verifikasi ini telah berhasil diproses oleh Bendahara.</p>
            </div>
        </div>
    </div>
    @endif

    <section class="bg-white p-4 sm:p-6 lg:p-10 rounded-xl lg:rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden mb-6 border border-slate-100">

        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 pb-6 border-b border-slate-100 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-lg bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-[10px] font-black uppercase tracking-wider border border-{{ $statusColor }}-200">
                        {{ $status ?? 'Selesai' }}
                    </span>
                    <span class="text-slate-400 text-xs font-medium">ID USULAN: #USL-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Detail Riwayat Verifikasi</h2>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('bendahara.riwayat.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-bold text-sm border border-slate-200">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-800 text-white rounded-xl transition font-bold text-sm shadow-lg shadow-slate-200 hover:bg-slate-900">
                    <i class="fas fa-print"></i> Cetak Bukti
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Information --}}
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Informasi Utama -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-{{ $statusColor }}-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <i class="fas fa-file-alt text-{{ $statusColor }}-500"></i>
                        Informasi Kegiatan
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pengusul</label>
                            <div class="text-slate-700 font-bold text-base">{{ $kegiatan_data['nama_pengusul'] }}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">NIM / NIP</label>
                            <div class="text-slate-700 font-bold text-base">{{ $kegiatan_data['nim_nip'] }}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Pelaksana</label>
                            <div class="text-slate-700 font-bold text-base">{{ $kegiatan_data['nama_pelaksana'] }}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">PJ Kegiatan</label>
                            <div class="text-slate-700 font-bold text-base">{{ $kegiatan_data['nama_penanggung_jawab'] }}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Kode MAK / Bukti</label>
                            <div class="text-slate-700 font-bold text-base">{{ $kode_mak ?? '-' }}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu</label>
                            <div class="text-slate-700 font-bold text-base">
                                {{ fmtDateIndo($kegiatan_data['tanggal_mulai']) }} - {{ fmtDateIndo($kegiatan_data['tanggal_selesai']) }}
                            </div>
                        </div>
                        <div class="md:col-span-2 lg:col-span-3 space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Kegiatan</label>
                            <div class="text-slate-700 font-bold text-lg leading-tight">{{ $kegiatan_data['nama_kegiatan'] }}</div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Gambaran Umum</label>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-slate-600 text-sm leading-relaxed">{{ $kegiatan_data['gambaran_umum'] }}</div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tahapan Pelaksanaan</label>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ $kegiatan_data['tahapan_kegiatan'] }}</div>
                        </div>
                    </div>
                </div>

                <!-- IKU & Indikator -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-{{ $statusColor }}-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                        <i class="fas fa-bullseye text-{{ $statusColor }}-500"></i>
                        IKU & Indikator Keberhasilan
                    </h3>
                    <div class="space-y-4">
                        @foreach($iku_data as $iku)
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="w-2 h-2 rounded-full bg-{{ $statusColor }}-500"></div>
                            <span class="text-sm font-bold text-slate-700">{{ $iku }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Rincian RAB -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-{{ $statusColor }}-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <i class="fas fa-table text-{{ $statusColor }}-500"></i>
                        Rincian Anggaran
                    </h3>
                    @foreach($rab_data as $kategori => $items)
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            {{ $kategori }}
                        </h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-100 shadow-sm mb-8">
                            <table class="w-full">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Uraian</th>
                                        <th class="px-4 py-3 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Volume</th>
                                        <th class="px-4 py-3 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 bg-white">
                                    @foreach($items as $item)
                                        @php $tot = $item['vol1'] * ($item['vol2'] ?? 1) * $item['harga']; @endphp
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="px-4 py-4">
                                                <div class="text-sm font-bold text-slate-800">{{ $item['uraian'] }}</div>
                                                <div class="text-[10px] text-slate-400 font-bold">{{ $item['rincian'] }}</div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <span class="px-2 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-600">
                                                    {{ $item['vol1'] }} {{ $item['sat1'] }}
                                                    @if(isset($item['vol2'])) x {{ $item['vol2'] }} {{ $item['sat2'] }} @endif
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <span class="text-sm font-black text-slate-800">{{ fmtRp($tot) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right Column: Summary & History --}}
            <div class="space-y-8">
                <!-- Summary Card -->
                <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
                    <div class="p-8 bg-slate-900 text-white">
                        <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Total Disetujui</span>
                        <div class="text-3xl font-black mt-1">{{ fmtRp($anggaran_disetujui) }}</div>
                    </div>
                    <div class="p-8 space-y-4">
                        <div class="flex justify-between items-center p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                            <div>
                                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest block">Dicairkan</span>
                                <span class="text-lg font-black text-emerald-700">{{ fmtRp($jumlah_dicairkan) }}</span>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-emerald-200/50 flex items-center justify-center text-emerald-600">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                        <div class="space-y-3 pt-4 border-t border-slate-100">
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-slate-400 uppercase tracking-widest">Tgl Pencairan</span>
                                <span class="text-slate-700">{{ $tanggal_pencairan ? fmtDateIndo($tanggal_pencairan) : '-' }}</span>
                            </div>
                            <div class="flex justify-between text-xs font-bold">
                                <span class="text-slate-400 uppercase tracking-widest">Metode</span>
                                <span class="text-slate-700">{{ $metode_pencairan }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Table -->
                @if(!empty($riwayat_pencairan))
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="fas fa-history text-blue-600"></i> Riwayat Pencairan
                    </h3>
                    <div class="space-y-4">
                        @foreach($riwayat_pencairan as $item)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ fmtDateIndo($item['tanggal_pencairan']) }}</span>
                                <span class="text-xs font-black text-blue-600">{{ $item['termin'] }}</span>
                            </div>
                            <div class="text-sm font-black text-slate-800">{{ fmtRp($item['nominal']) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
</main>

<style>
    @keyframes fade-in { from{opacity:0; transform: translateY(10px);} to{opacity:1; transform: translateY(0);} }
    .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
</style>
@endsection
