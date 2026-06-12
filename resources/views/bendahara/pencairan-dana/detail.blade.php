@extends('layouts.app')

@section('title', 'Detail Usulan & Pencairan')

@section('content')
@php
    if (!function_exists('formatRupiah')) {
        function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
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

    $statusColor = match(strtolower($status)) {
        'disetujui', 'selesai', 'dana diberikan' => 'emerald',
        'revisi' => 'amber',
        'ditolak' => 'rose',
        'review', 'menunggu', 'dana belum diberikan semua' => 'blue',
        default => 'slate'
    };
@endphp

<main class="main-content font-poppins p-4 sm:p-6 lg:p-10 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full animate-fade-in">
    
    {{-- Status Header Alert --}}
    @if(strtolower($status) === 'dana diberikan')
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-8 rounded-r-2xl shadow-sm animate-slide-up">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-double text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-emerald-800 font-bold text-sm sm:text-base">Dana Lunas</h3>
                    <p class="text-emerald-700 text-xs sm:text-sm">Seluruh anggaran untuk kegiatan ini telah dicairkan sepenuhnya.</p>
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
                        {{ $status ?? 'Pending' }}
                    </span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-400 text-xs font-medium">ID USULAN: #USL-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Detail Usulan & Pencairan</h2>
                <p class="text-slate-400 text-xs mt-1">Review Detail Usulan & Status Pencairan Dana Keuangan oleh Bendahara</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] bg-blue-50 px-3 py-1 rounded-md border border-blue-100">
                        MAK: {{ $kegiatan_data['mak_code'] ?? '000.00.0.000.000' }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('bendahara.pencairan.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-bold text-sm border border-slate-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('cetak.kak', $id) }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition font-bold text-sm shadow-lg shadow-emerald-200">
                    <i class="fas fa-print"></i> Cetak KAK
                </a>
            </div>
        </div>

        {{-- Stepper Progress --}}
        <div class="mb-14 px-4">
            <div class="relative flex justify-between items-center max-w-4xl mx-auto">
                <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-100 -translate-y-1/2 z-0"></div>
                @php
                    $isSelesai = (isset($kegiatan) && $kegiatan->status_utama_id == 8) || strtolower($status) === 'selesai';
                    $progressColor = $isSelesai ? 'emerald' : $statusColor;
                    $s = strtolower($status);
                    $progressWidth = $isSelesai ? '100%' : (($s === 'menunggu' || $s === 'review') ? '50%' : '75%');
                @endphp
                <div class="absolute top-1/2 left-0 h-1 bg-{{ $progressColor }}-500 -translate-y-1/2 z-0 transition-all duration-1000" style="width: {{ $progressWidth }}"></div>
                
                @foreach(['Pengajuan', 'Verifikasi', 'Selesai'] as $index => $step)
                    @php
                        $isCompleted = $isSelesai || ($index === 0) || ($index === 1 && $s !== 'menunggu' && $s !== 'review');
                        $isActive = ($index === 2 && $isSelesai) ||
                                    ($index === 1 && !$isSelesai && ($s === 'review' || $s === 'menunggu'));
                    @endphp
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full {{ $isCompleted ? 'bg-'.$progressColor.'-500 text-white shadow-md' : ($isActive ? 'bg-white border-4 border-'.$progressColor.'-500 text-'.$progressColor.'-500 shadow-md' : 'bg-white border-4 border-slate-200 text-slate-300') }} flex items-center justify-center transition-all duration-500">
                            @if($isCompleted) <i class="fas fa-check text-sm"></i> @else <span class="text-sm font-bold">{{ $index + 1 }}</span> @endif
                        </div>
                        <span class="absolute -bottom-7 text-[10px] font-black uppercase tracking-widest whitespace-nowrap {{ $isCompleted || $isActive ? 'text-slate-800' : 'text-slate-400' }}">{{ $step }}</span>
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
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIM/NIP Pengusul</span>
                            <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan_data['nim_nip'] ?? '') !!}</div>
                        </div>
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Penanggung Jawab</span>
                            <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan_data['nama_penanggung_jawab'] ?? '') !!}</div>
                        </div>
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIM/NIP Penanggung Jawab</span>
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
        <div class="mt-12 pt-12 border-t border-slate-100 max-w-6xl mx-auto space-y-12">
            
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
                            {{ $kode_mak ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: RAB & Disbursement Status --}}
            <div class="space-y-6 pt-4 border-t border-slate-100">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Status Anggaran & Pencairan</h3>
                <div class="w-full bg-slate-900 rounded-[2.5rem] shadow-2xl shadow-slate-300 overflow-hidden relative group">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="p-10 text-white border-b border-white/5">
                        <span class="text-[10px] font-black uppercase tracking-widest opacity-60 italic font-medium">Anggaran Disetujui</span>
                        <div class="text-4xl font-black mt-2 tracking-tighter">{{ formatRupiah($anggaran_disetujui) }}</div>
                    </div>
                    <div class="p-10 space-y-6 bg-slate-800/50">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest block mb-1">Dicairkan</span>
                                <span class="text-xl font-black text-white">{{ formatRupiah($jumlah_dicairkan) }}</span>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                                <i class="fas fa-check-double text-base"></i>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest block mb-1">Sisa Dana</span>
                                <span class="text-xl font-black text-white">{{ formatRupiah($sisa_dana) }}</span>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-500/20">
                                <i class="fas fa-hand-holding-usd text-base"></i>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-slate-900 border-t border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                                <i class="fas fa-fingerprint text-xs"></i>
                            </div>
                            <div>
                                <span class="block text-[8px] font-black text-slate-500 uppercase tracking-widest">KODE MAK</span>
                                <span class="text-span text-[10px] font-mono font-bold text-blue-200">{{ $kode_mak ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="px-3 py-1 bg-emerald-500/10 rounded-lg text-emerald-400 text-[9px] font-black uppercase tracking-widest border border-emerald-500/20">
                            <i class="fas fa-check-circle"></i> Terverifikasi
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4: LPJ Status --}}
            <div class="space-y-6 pt-4 border-t border-slate-100">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Status Laporan Pertanggungjawaban (LPJ)</h3>
                <div class="w-full bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-slate-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            @php
                                $lpjColor = match(strtolower($lpj_status)) {
                                    'disetujui' => 'emerald',
                                    'revisi', 'menunggu verifikasi' => 'amber',
                                    'belum ada' => 'slate',
                                    default => 'blue'
                                };
                            @endphp
                            <div class="px-4 py-3 rounded-2xl bg-{{ $lpjColor }}-50 border border-{{ $lpjColor }}-100 flex items-center gap-3 transition-all">
                                <div class="w-8 h-8 rounded-xl bg-{{ $lpjColor }}-100 flex items-center justify-center text-{{ $lpjColor }}-600">
                                    <i class="fas fa-{{ strtolower($lpj_status) === 'disetujui' ? 'check-double' : 'clock' }} text-xs"></i>
                                </div>
                                <div>
                                    <span class="block text-[9px] font-black text-{{ $lpjColor }}-600 uppercase tracking-widest">Progress LPJ</span>
                                    <span class="text-xs font-black text-slate-800">{{ $lpj_status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(strtolower($lpj_status) === 'belum ada')
                        <p class="text-[9px] text-slate-400 mt-4 font-bold uppercase tracking-tight opacity-70">
                            <i class="fas fa-exclamation-triangle text-amber-500 mr-1"></i> LPJ belum dibuat oleh pengusul.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Section 5: Riwayat Pencairan (if any) --}}
            @if(!empty($riwayat_pencairan))
                <div class="space-y-6 pt-4 border-t border-slate-100">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Riwayat Pencairan</h3>
                    <div class="w-full bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative">
                        <div class="space-y-6 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">
                            @foreach($riwayat_pencairan as $item)
                            <div class="relative pl-10 group">
                                <div class="absolute left-0 top-1.5 w-6 h-6 rounded-lg bg-white border-4 border-slate-100 flex items-center justify-center z-10 group-hover:border-blue-500 transition-all duration-300"></div>
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ fmtDateIndo($item['tanggal_pencairan']) }}</span>
                                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest">{{ $item['termin'] }}</span>
                                </div>
                                <div class="text-sm font-black text-slate-800">{{ formatRupiah($item['nominal']) }}</div>
                                @if($item['catatan'])
                                <div class="mt-2 text-[10px] text-slate-500 italic p-3 bg-slate-50 rounded-xl border border-slate-100">{{ $item['catatan'] }}</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Section 6: Panel Pencairan --}}
            @if($boleh_cairkan)
                <div class="space-y-6 pt-4 border-t border-slate-100">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Panel Pencairan</h3>
                    <div class="w-full bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden p-8 lg:p-10 relative">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 z-0"></div>
                        <div class="relative z-10">
                            <form id="formPencairan" action="#" method="POST" class="space-y-8 w-full">
                                @csrf
                                <input type="hidden" name="kegiatanId" value="{{ $id }}">
                                <input type="hidden" name="sisa_dana" id="sisa_dana" value="{{ $sisa_dana }}">
                                <input type="hidden" name="lpj_status" id="lpj_status_val" value="{{ strtolower($lpj_status) }}">

                                <div class="space-y-5">
                                    <div class="flex items-center justify-between">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tahapan Pencairan</label>
                                        <button type="button" onclick="addStage()" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                                            <i class="fas fa-plus mr-1"></i> Tambah
                                        </button>
                                    </div>
                                    
                                    <div id="stages-wrapper" class="space-y-4">
                                        <!-- Dynamic inputs -->
                                    </div>

                                    <div class="p-6 bg-slate-900 rounded-2xl text-white shadow-xl shadow-slate-200 relative overflow-hidden">
                                        <div class="absolute right-0 bottom-0 w-20 h-20 bg-white/5 rounded-full -mr-10 -mb-10 blur-xl"></div>
                                        <div class="flex justify-between items-center relative z-10">
                                            <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Total Input Nominal</span>
                                            <span id="total-nominal" class="text-xl font-black">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                @if($jumlah_dicairkan > 0 && strtolower($lpj_status) !== 'disetujui')
                                <!-- Warning LPJ -->
                                <div class="p-5 bg-rose-50 border border-rose-100 rounded-2xl animate-pulse">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0">
                                            <i class="fas fa-lock text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-[11px] font-black text-rose-800 uppercase tracking-widest">Termin 2 Dikunci</h4>
                                            <p class="text-[10px] text-rose-600 font-bold mt-1 leading-relaxed">Sisa dana 50% hanya dapat dicairkan setelah LPJ disetujui. Status saat ini: <span class="uppercase">{{ $lpj_status }}</span></p>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="space-y-3">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan Bendahara</label>
                                    <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                                        <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Catatan / Komentar Bendahara</span>
                                        <textarea name="catatan" rows="3" placeholder="Tambahkan instruksi khusus pencairan..." 
                                            class="w-full mt-2 bg-transparent outline-none text-slate-600 font-semibold text-xs leading-relaxed border-none focus:ring-0 p-0 resize-none"></textarea>
                                    </div>
                                </div>

                                <div class="pt-4">
                                    @php
                                        $isDisabled = ($jumlah_dicairkan > 0 && strtolower($lpj_status) !== 'disetujui');
                                    @endphp
                                    <button type="button" onclick="submitPencairan()" 
                                        class="w-full py-5 {{ $isDisabled ? 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none' : 'bg-blue-600 text-white shadow-2xl shadow-blue-200 hover:bg-blue-700 hover:-translate-y-1' }} rounded-[1.5rem] font-black text-[11px] uppercase tracking-widest transition-all flex items-center justify-center gap-3 group"
                                        {{ $isDisabled ? 'disabled' : '' }}>
                                        <span>{{ $isDisabled ? 'Pencairan Terkunci' : 'Proses Pencairan Dana' }}</span>
                                        <i class="fas fa-{{ $isDisabled ? 'lock' : 'paper-plane' }} {{ $isDisabled ? '' : 'group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform' }}"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="space-y-6 pt-4 border-t border-slate-100">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Panel Pencairan</h3>
                    <div class="w-full bg-emerald-50 border border-emerald-100 rounded-[2.5rem] p-10 text-center relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-emerald-100 rounded-full blur-2xl"></div>
                        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600 shadow-xl shadow-emerald-100">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                        <h4 class="text-base font-black text-emerald-800 uppercase tracking-widest mb-2">Anggaran Lunas</h4>
                        <p class="text-[11px] text-emerald-600 font-bold uppercase tracking-tight opacity-70">Seluruh dana telah dicairkan.</p>
                    </div>
                </div>
            @endif

        </div>

    </section>
</main>

<style>
    @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let stageCount = 0;
    const sisaDana = {{ $sisa_dana }};

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('stages-wrapper')) {
            addStage();
        }
    });

    function addStage() {
        if (stageCount >= 5) return;
        stageCount++;
        
        const todayStr = new Date().toISOString().split('T')[0];
        
        const wrapper = document.getElementById('stages-wrapper');
        const div = document.createElement('div');
        div.className = 'p-5 bg-white border border-slate-100 rounded-[1.5rem] shadow-sm relative animate-fade-in group/stage hover:border-blue-200 transition-all';
        div.id = `stage-${stageCount}`;
        div.innerHTML = `
            <div class="flex justify-between items-center mb-4">
                <span class="px-3 py-1 bg-slate-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">Tahapan Pencairan ${stageCount}</span>
                <button type="button" onclick="removeStage(${stageCount})" class="w-8 h-8 rounded-lg text-slate-300 hover:bg-rose-50 hover:text-rose-500 transition-all flex items-center justify-center">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Tgl Estimasi</label>
                        <input type="date" name="tanggalTahapan[]" value="${todayStr}" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold outline-none focus:border-blue-500 transition-all" required>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Keterangan Termin</label>
                        <input type="text" name="terminTahapan[]" placeholder="Contoh: Termin ${stageCount}" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold outline-none focus:border-blue-500 transition-all" required>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Nominal Pencairan</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-300">Rp</span>
                        <input type="text" name="nominalTahapan[]" placeholder="0" class="nominal-input w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm font-black text-right outline-none focus:border-blue-500 transition-all" oninput="formatRupiahInput(this); updateTotalNominal()" required>
                    </div>
                </div>
            </div>
        `;
        wrapper.appendChild(div);
    }

    function removeStage(id) {
        if (document.querySelectorAll('[id^="stage-"]').length <= 1) return;
        document.getElementById(`stage-${id}`).remove();
        updateTotalNominal();
    }

    function formatRupiahInput(input) {
        let value = input.value.replace(/\D/g, '');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        input.value = value;
    }

    function updateTotalNominal() {
        let total = 0;
        document.querySelectorAll('.nominal-input').forEach(input => {
            total += parseInt(input.value.replace(/\D/g, '')) || 0;
        });
        
        const display = document.getElementById('total-nominal');
        display.innerText = 'Rp ' + total.toLocaleString('id-ID');
        
        if (total > sisaDana) display.className = 'text-xl font-black text-rose-400';
        else if (total === sisaDana) display.className = 'text-xl font-black text-emerald-400';
        else display.className = 'text-xl font-black text-white';
    }

    function submitPencairan() {
        let totalInput = 0;
        document.querySelectorAll('.nominal-input').forEach(input => {
            totalInput += parseInt(input.value.replace(/\D/g, '')) || 0;
        });

        if (totalInput <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Input Tidak Valid',
                text: 'Nominal pencairan harus lebih dari 0.',
                confirmButtonColor: '#3b82f6',
                borderRadius: '24px'
            });
            return;
        }

        if (totalInput > sisaDana) {
            Swal.fire({
                icon: 'error',
                title: 'Melebihi Sisa Dana',
                text: 'Total input nominal tidak boleh melebihi sisa dana yang tersedia!',
                confirmButtonColor: '#ef4444',
                borderRadius: '24px'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Pencairan?',
            text: `Anda akan memproses pencairan sebesar Rp ${totalInput.toLocaleString('id-ID')}. Data ini akan dikunci dan diteruskan ke sistem keuangan.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Proses Sekarang',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#2563eb',
            borderRadius: '24px'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                const form = document.getElementById('formPencairan');
                const formData = new FormData(form);

                fetch("{{ route('bendahara.pencairan.proses') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Instruksi pencairan dana telah berhasil dibuat.',
                            confirmButtonColor: '#10b981',
                            borderRadius: '24px'
                        }).then(() => {
                            window.location.href = "{{ route('bendahara.pencairan.index') }}";
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message || 'Terjadi kesalahan saat memproses pencairan.',
                            confirmButtonColor: '#ef4444',
                            borderRadius: '24px'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan Sistem',
                        text: 'Gagal menghubungi server.',
                        confirmButtonColor: '#ef4444',
                        borderRadius: '24px'
                    });
                });
            }
        });
    }
</script>
@endpush
