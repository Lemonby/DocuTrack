@extends('layouts.app')

@section('title', 'Review & Telaah Usulan (Wadir)')

@section('content')
@php
    if (!function_exists('formatRupiah')) {
        function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
    }
    function displayValue($value, $placeholder = 'Belum diisi') {
        return !empty($value) ? htmlspecialchars($value) : '<span class="text-gray-400 italic">' . $placeholder . '</span>';
    }

    $statusColor = $kegiatan->posisi_id == 4 ? 'blue' : match(strtolower($status)) {
        'disetujui', 'selesai' => 'emerald',
        'revisi' => 'amber',
        'ditolak' => 'rose',
        'review', 'menunggu' => 'blue',
        default => 'slate'
    };
@endphp

<main class="main-content font-poppins p-4 sm:p-6 lg:p-10 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full animate-fade-in bg-slate-50/50">
    
    {{-- Status Header Alert --}}
    @if(strtolower($status) === 'revisi')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-8 rounded-r-2xl shadow-sm animate-slide-up">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-amber-800 font-bold text-sm sm:text-base">Perlu Revisi</h3>
                    <p class="text-amber-700 text-xs sm:text-sm mt-1 leading-relaxed">
                        {{ $catatan_revisi ?? 'Terdapat beberapa bagian yang perlu diperbaiki sebelum pengajuan dapat dilanjutkan.' }}
                    </p>
                </div>
            </div>
        </div>
    @elseif($kegiatan->posisi_id == 4)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8 rounded-r-2xl shadow-sm animate-slide-up">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-hourglass-half text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-blue-800 font-bold text-sm sm:text-base">Menunggu Persetujuan Wadir</h3>
                    <p class="text-blue-700 text-xs sm:text-sm">Usulan ini telah diverifikasi oleh Verifikator & PPK, dan saat ini menunggu persetujuan Anda.</p>
                </div>
            </div>
        </div>
    @elseif($kegiatan->posisi_id >= 5)
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-8 rounded-r-2xl shadow-sm animate-slide-up">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-emerald-800 font-bold text-sm sm:text-base">Usulan Disetujui Wadir</h3>
                    <p class="text-emerald-700 text-xs sm:text-sm">Anda telah menyetujui usulan ini dan diteruskan ke Bendahara untuk pencairan dana.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Container --}}
    <section class="bg-white p-6 sm:p-10 lg:p-12 rounded-[2rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden mb-8">
        
        {{-- Header Title Section --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-10 pb-6 border-b border-slate-100 gap-4">
            <div class="w-full lg:w-auto">
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-lg bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-[10px] font-black uppercase tracking-wider border border-{{ $statusColor }}-200">
                        {{ $kegiatan->posisi_id == 4 ? 'Menunggu' : ($status ?? 'Pending') }}
                    </span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-400 text-xs font-medium">ID USULAN: #USL-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Detail Usulan Kegiatan</h2>
                <p class="text-slate-400 text-xs mt-1">Review & Persetujuan Usulan KAK oleh Wadir</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] bg-blue-50 px-3 py-1 rounded-md border border-blue-100">
                        MAK: {{ $kegiatan_data['mak_code'] ?? '000.00.0.000.000' }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('wadir.kegiatan.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-bold text-sm border border-slate-200">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @if($kegiatan->posisi_id >= 4 || in_array($kegiatan->status_utama_id, [5, 6, 8]))
                <a href="{{ route('cetak.kak', $id) }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition font-bold text-sm shadow-lg shadow-emerald-200 active:scale-95">
                    <i class="fas fa-print"></i> Cetak KAK
                </a>
                @endif
            </div>
        </div>

        {{-- Stepper Progress --}}
        <div class="mb-14 px-4">
            <div class="relative flex justify-between items-center max-w-4xl mx-auto">
                <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-100 -translate-y-1/2 z-0"></div>
                <div class="absolute top-1/2 left-0 {{ $kegiatan->posisi_id >= 5 ? 'w-full' : ($kegiatan->posisi_id == 1 ? 'w-0' : 'w-1/2') }} h-1 bg-{{ $statusColor }}-500 -translate-y-1/2 z-0 transition-all duration-1000"></div>
                
                @foreach(['Pengajuan', 'Verifikasi', 'Selesai'] as $index => $step)
                    @php
                        $isCompleted = ($index === 0 && $kegiatan->posisi_id > 1) ||
                                       ($index === 1 && $kegiatan->posisi_id > 4) ||
                                       ($index === 2 && $kegiatan->posisi_id >= 5);
                        $isActive = ($index === 0 && $kegiatan->posisi_id == 1) ||
                                    ($index === 1 && in_array($kegiatan->posisi_id, [2, 3, 4])) ||
                                    ($index === 2 && $kegiatan->posisi_id >= 5);
                    @endphp
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full {{ $isCompleted ? 'bg-'.$statusColor.'-500 text-white' : ($isActive ? 'bg-white border-4 border-'.$statusColor.'-500 text-'.$statusColor.'-500' : 'bg-white border-4 border-slate-200 text-slate-300') }} flex items-center justify-center shadow-md transition-all duration-500">
                            @if($isCompleted) <i class="fas fa-check text-sm"></i> @else <span class="text-sm font-bold">{{ $index + 1 }}</span> @endif
                        </div>
                        <span class="absolute -bottom-7 text-[10px] font-black uppercase tracking-widest {{ $isCompleted || $isActive ? 'text-slate-800' : 'text-slate-400' }}">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- main content layout --}}
        <div class="mt-16 max-w-6xl mx-auto space-y-12">
            
            {{-- KAK Details --}}
            <div class="space-y-12">
                
                {{-- KERANGKA ACUAN KERJA (KAK) SECTION --}}
                <div class="space-y-6">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Kerangka Acuan Kerja (KAK)</h3>
                    
                    {{-- Row 1: Four compact fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Pengusul</span>
                            <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan_data['nama_pengusul']) !!}</div>
                        </div>
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIMNama Pengusul</span>
                            <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan_data['nim_nip'] ?? '') !!}</div>
                        </div>
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Penanggung Jawab</span>
                            <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan_data['nama_penanggung_jawab'] ?? '') !!}</div>
                        </div>
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIM/NIP Nama Penanggung Jawab</span>
                            <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan_data['nip_penanggung_jawab'] ?? '') !!}</div>
                        </div>
                    </div>

                    {{-- Row 2: Full-width Nama Kegiatan --}}
                    <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                        <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Kegiatan</span>
                        <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan_data['nama_kegiatan']) !!}</div>
                    </div>

                    {{-- Row 3: Full-width Gambaran Umum --}}
                    <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                        <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Gambaran Umum</span>
                        <div class="text-sm font-semibold text-slate-600 leading-relaxed min-h-[4rem] mt-1 whitespace-pre-line">{!! displayValue($kegiatan_data['gambaran_umum']) !!}</div>
                    </div>

                    {{-- Row 4: Full-width Penerima Manfaat --}}
                    <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                        <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Penerima Manfaat</span>
                        <div class="text-sm font-semibold text-slate-600 leading-relaxed min-h-[3rem] mt-1">{!! displayValue($kegiatan_data['penerima_manfaat']) !!}</div>
                    </div>

                    {{-- Subheader: Strategi Pencapaian Keluaran --}}
                    <div class="pt-4 space-y-6">
                        <h4 class="text-lg font-black text-slate-800 tracking-tight">Strategi Pencapaian Keluaran</h4>
                        
                        {{-- Metode Pelaksanaan --}}
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Metode Pelaksanaan</span>
                            <div class="text-sm font-semibold text-slate-600 leading-relaxed min-h-[4rem] mt-1">{!! displayValue($kegiatan_data['metode_pelaksanaan']) !!}</div>
                        </div>

                        {{-- Tahapan Kegiatan --}}
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tahapan Kegiatan</span>
                            <div class="text-sm font-semibold text-slate-600 leading-relaxed min-h-[4rem] mt-1 whitespace-pre-line">
                                @if($kegiatan->kak && $kegiatan->kak->tahapans->isNotEmpty())
                                    @php $tahapNo = 1; @endphp
                                    @foreach($kegiatan->kak->tahapans as $tahap)
                                        Tahap {{ $tahapNo++ }}: {{ $tahap->nama_tahapan }}
                                    @endforeach
                                @else
                                    <span class="text-gray-400 italic">Belum diisi</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INDIKATOR KINERJA SECTION --}}
                <div class="space-y-6 pt-6">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Indikator Kinerja</h3>
                    
                    <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#E0EEFF]">
                                    <th class="px-6 py-4 text-center text-xs font-black text-slate-800 uppercase tracking-wider w-16">NO</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-800 uppercase tracking-wider w-32">Bulan</th>
                                    <th class="px-6 py-4 text-left text-xs font-black text-slate-800 uppercase tracking-wider">Indikator Keberhasilan</th>
                                    <th class="px-6 py-4 text-center text-xs font-black text-slate-800 uppercase tracking-wider w-28">Target</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @php
                                    $nama_bulan = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    $no = 1;
                                @endphp
                                @if($kegiatan->kak && $kegiatan->kak->indikators->isNotEmpty())
                                    @foreach($kegiatan->kak->indikators as $ind)
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4 text-center text-sm font-semibold text-slate-500">{{ $no++ }}.</td>
                                            <td class="px-6 py-4 text-sm font-black text-slate-800">{{ $nama_bulan[$ind->bulan] ?? 'Bulan ' . $ind->bulan }}</td>
                                            <td class="px-6 py-4 text-sm text-slate-600 leading-relaxed font-semibold">
                                                <ul class="list-disc pl-4 space-y-1">
                                                    @php
                                                        $bullets = explode(',', $ind->indikator_keberhasilan);
                                                    @endphp
                                                    @foreach($bullets as $bullet)
                                                        <li>{{ trim($bullet) }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-600 text-xs font-black rounded-lg">
                                                    {{ $ind->target_persen }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-slate-400 italic">Belum ada indikator kinerja</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- INDIKATOR KERJA UTAMA SECTION --}}
                <div class="space-y-6 pt-6">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Indikator Kerja Utama</h3>
                    
                    <div class="relative border border-slate-200 rounded-2xl px-4 py-4 bg-white flex items-center justify-between hover:border-slate-300 transition-all duration-200">
                        <div class="flex-1 flex flex-wrap gap-2">
                            @if(!empty($iku_data) && count(array_filter($iku_data)) > 0)
                                @foreach($iku_data as $iku)
                                    <span class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1.5 rounded-xl border border-blue-100 flex items-center gap-1.5">
                                        <i class="fas fa-check-circle text-blue-500 text-[10px]"></i>
                                        {{ trim($iku) }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-slate-400 text-sm font-semibold">Pilih</span>
                            @endif
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-sm ml-3"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- RINCIAN ANGGARAN BIAYA (RAB) SECTION --}}
        <div class="mt-12 pt-12 border-t border-slate-100 max-w-6xl mx-auto">
            <h3 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-3">
                <i class="fas fa-calculator text-blue-500"></i> Rincian Anggaran (RAB)
            </h3>
            
            <div class="space-y-10">
                @php 
                    $grand_total = 0;
                @endphp
                @if(!empty($rab_data))
                    @foreach($rab_data as $kategori => $items)
                        @php 
                            $subtotal = 0;
                            foreach($items as $it) {
                                $subtotal += $it['vol1'] * ($it['vol2'] ?? 1) * $it['harga'];
                            }
                            $grand_total += $subtotal;
                        @endphp
                        
                        {{-- Category Card --}}
                        <div class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                            
                            {{-- Category Header --}}
                            <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner">
                                        <i class="fas fa-shopping-bag text-base"></i>
                                    </div>
                                    <h4 class="text-base font-black text-slate-800 tracking-tight">{{ $kategori }}</h4>
                                    <i class="fas fa-comment-dots text-violet-500 text-base cursor-pointer hover:scale-110 transition-transform" title="Lihat catatan"></i>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Subtotal</span>
                                    <span class="text-sm font-black text-emerald-600">{{ formatRupiah($subtotal) }}</span>
                                </div>
                            </div>

                            {{-- Items Table --}}
                            <div class="overflow-x-auto">
                                <table class="min-w-[1000px] w-full text-left table-auto border-separate border-spacing-y-2 px-6 pb-4">
                                    <thead>
                                        <tr class="text-slate-400 text-[11px] font-black uppercase tracking-wider">
                                            <th class="pb-2 pl-2">Uraian</th>
                                            <th class="pb-2">Rincian</th>
                                            <th class="pb-2 text-center w-20">Vol 1</th>
                                            <th class="pb-2 text-center w-20">Sat 1</th>
                                            <th class="pb-2 text-center w-20">Vol 2</th>
                                            <th class="pb-2 text-center w-20">Sat 2</th>
                                            <th class="pb-2 text-center w-36">Harga (RP)</th>
                                            <th class="pb-2 text-right pr-4 w-40">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        @foreach($items as $item)
                                            @php 
                                                $total_item = $item['vol1'] * ($item['vol2'] ?? 1) * $item['harga'];
                                            @endphp
                                            <tr class="hover:bg-slate-50/30 transition-colors">
                                                <td class="py-1 pr-3 pl-2">
                                                    <div class="border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 bg-white w-full truncate" title="{{ $item['uraian'] }}">{{ $item['uraian'] }}</div>
                                                </td>
                                                <td class="py-1 pr-3">
                                                    <div class="border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 bg-white w-full truncate" title="{{ $item['rincian'] }}">{{ $item['rincian'] }}</div>
                                                </td>
                                                <td class="py-1 pr-3 text-center">
                                                    <div class="border border-slate-200 rounded-xl py-2 text-sm font-semibold text-slate-700 bg-white text-center w-full">{{ number_format($item['vol1'], 0) }}</div>
                                                </td>
                                                <td class="py-1 pr-3 text-center">
                                                    <div class="border border-slate-200 rounded-xl py-2 text-sm font-semibold text-slate-700 bg-white text-center w-full uppercase text-xs">{{ $item['sat1'] }}</div>
                                                </td>
                                                <td class="py-1 pr-3 text-center">
                                                    <div class="border border-slate-200 rounded-xl py-2 text-sm font-semibold text-slate-700 bg-white text-center w-full">{{ number_format($item['vol2'] ?? 1, 0) }}</div>
                                                </td>
                                                <td class="py-1 pr-3 text-center">
                                                    <div class="border border-slate-200 rounded-xl py-2 text-sm font-semibold text-slate-700 bg-white text-center w-full uppercase text-xs">{{ $item['sat2'] ?? '-' }}</div>
                                                </td>
                                                <td class="py-1 pr-3 text-center">
                                                    <div class="border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 bg-white text-center w-full">{{ number_format($item['harga'], 0, ',', '.') }}</div>
                                                </td>
                                                <td class="py-1 text-right pr-4">
                                                    <span class="text-sm font-black text-slate-800 leading-tight block">{{ formatRupiah($total_item) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-8 text-center text-slate-400 italic bg-white border border-slate-200 rounded-2xl shadow-sm">
                        Belum ada data anggaran biaya (RAB)
                    </div>
                @endif

                {{-- Grand Total --}}
                <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-slate-200 shadow-sm max-w-md ml-auto mt-6">
                    <span class="text-sm font-black text-slate-500 uppercase tracking-wider">Grand Total:</span>
                    <span class="text-2xl font-black text-blue-600 tracking-tight">{{ formatRupiah($grand_total) }}</span>
                </div>
            </div>
        </div>

        {{-- Rincian Rancangan, MAK & Persetujuan Section --}}
        <div class="mt-12 pt-12 border-t border-slate-100 max-w-6xl mx-auto space-y-10">
            
            {{-- Section 1: Rincian Rancangan Kegiatan --}}
            <div class="space-y-6">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Rincian Rancangan Kegiatan</h3>
                
                <div class="space-y-6">
                    {{-- Surat Pengantar --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">Surat Pengantar</label>
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200 flex items-center justify-between">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Upload Surat</span>
                            <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1 flex items-center gap-2">
                                @if(!empty($kegiatan_data['surat_pengantar']))
                                    <a href="{{ asset('storage/' . $kegiatan_data['surat_pengantar']) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1.5">
                                        <i class="fas fa-file-pdf text-red-500 text-base"></i>
                                        Lihat Surat Pengantar
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Belum ada berkas</span>
                                @endif
                            </div>
                            @if(!empty($kegiatan_data['surat_pengantar']))
                                <a href="{{ asset('storage/' . $kegiatan_data['surat_pengantar']) }}" download class="text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-upload text-sm"></i>
                                </a>
                            @else
                                <i class="fas fa-upload text-slate-300 text-sm"></i>
                            @endif
                        </div>
                    </div>

                    {{-- Kurun Waktu Pelaksanaan --}}
                    <div class="space-y-3">
                        <span class="text-xs font-bold text-slate-700 block">Kurun Waktu Pelaksanaan</span>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200 flex items-center justify-between">
                                <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Mulai</span>
                                <div class="text-xs font-semibold text-slate-700 min-h-[1.5rem] mt-1">
                                    {{ $kegiatan_data['tanggal_mulai'] ? \Carbon\Carbon::parse($kegiatan_data['tanggal_mulai'])->translatedFormat('d M Y') : '-' }}
                                </div>
                                <i class="far fa-calendar-alt text-slate-400 text-sm"></i>
                            </div>
                            <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200 flex items-center justify-between">
                                <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Selesai</span>
                                <div class="text-xs font-semibold text-slate-700 min-h-[1.5rem] mt-1">
                                    {{ $kegiatan_data['tanggal_selesai'] ? \Carbon\Carbon::parse($kegiatan_data['tanggal_selesai'])->translatedFormat('d M Y') : '-' }}
                                </div>
                                <i class="far fa-calendar-alt text-slate-400 text-sm"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Kode MAK --}}
            <div class="space-y-6 pt-4 border-t border-slate-100">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Kode Mata Anggaran Kegiatan (MAK)</h3>
                <div class="w-full">
                    <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                        <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kode MAK</span>
                        <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">
                            {{ $kegiatan_data['mak_code'] ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Panel Persetujuan --}}
            <div class="space-y-6 pt-4 border-t border-slate-100">
                @if($kegiatan->posisi_id == 4)
                    <form action="{{ route('wadir.kegiatan.store', $id) }}" method="POST" class="space-y-6 w-full">
                        @csrf
                        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Panel Persetujuan</h3>
                        
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Catatan / Komentar Wadir</span>
                            <textarea name="notes" rows="3" placeholder="Berikan catatan jika diperlukan..." 
                                class="w-full mt-2 bg-transparent outline-none text-slate-600 font-semibold text-xs leading-relaxed border-none focus:ring-0 p-0 resize-none"></textarea>
                        </div>

                        <div class="flex items-center justify-between gap-4 pt-4">
                            <a href="{{ route('wadir.kegiatan.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#007BFF] hover:bg-blue-700 text-white rounded-xl font-bold text-xs transition duration-200 shadow-md">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#198754] hover:bg-green-700 text-white rounded-xl font-bold text-xs transition duration-200 shadow-md">
                                <i class="fas fa-check-circle"></i> Setujui Usulan
                            </button>
                        </div>
                    </form>
                @else
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Panel Persetujuan</h3>
                    <div class="flex items-center justify-between gap-4 pt-4 w-full">
                        <a href="{{ route('wadir.kegiatan.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#007BFF] hover:bg-blue-700 text-white rounded-xl font-bold text-xs transition duration-200 shadow-md">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <div class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#A8E6CF] text-[#1D7A46] rounded-xl font-bold text-xs border border-[#8FE0C0] shadow-sm">
                            <i class="fas fa-check-circle"></i> Telah Disetujui
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </section>
</main>

<style>
    @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection
