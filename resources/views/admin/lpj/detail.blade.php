@extends('layouts.app')

@section('title', 'Pusat Pertanggungjawaban (LPJ)')

@section('content')
@php
    if (!function_exists('formatRupiah')) {
        function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
    }
    $isEditable = (in_array(strtolower($status), ['draft', 'revisi', 'menunggu_upload', 'menunggu upload', 'siap_submit', 'siap submit']));
    $statusColor = match(strtolower(str_replace(' ', '_', $status))) {
        'selesai', 'disetujui', 'setuju', 'lpj_disetujui' => 'emerald',
        'revisi' => 'amber',
        'menunggu', 'review', 'siap_submit' => 'blue',
        'ditolak' => 'rose',
        'menunggu_upload' => 'orange',
        default => 'slate'
    };
@endphp

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full animate-fade-in">

    @if(strtolower($status) === 'revisi')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-6 mb-8 rounded-r-3xl shadow-sm animate-slide-up">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-600 border border-amber-200">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <h3 class="text-amber-800 font-black text-xs uppercase tracking-widest mb-1">Perlu Perbaikan Laporan</h3>
                    <p class="text-amber-700 text-sm leading-relaxed font-medium italic">
                        "{{ $catatan_revisi ?? 'Terdapat bukti yang kurang jelas atau nominal yang tidak sesuai. Mohon periksa kembali rincian di bawah.' }}"
                    </p>
                </div>
            </div>
        </div>
    @endif

    <section class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden mb-10">
        
        <!-- Premium Header -->
        <div class="px-8 md:px-12 py-10 border-b border-slate-50 bg-gradient-to-br from-white via-white to-slate-50/50 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-64 h-64 bg-blue-50/30 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 rounded-xl bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-[10px] font-black uppercase tracking-widest border border-{{ $statusColor }}-200 shadow-sm">
                            {{ $status }}
                        </span>
                        <div class="h-4 w-px bg-slate-200 mx-1"></div>
                        <span class="text-slate-400 text-[10px] font-black uppercase tracking-widest">KODE LPJ: #{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tighter leading-tight truncate">Laporan Pertanggung jawaban</h2>
                    <p class="text-sm text-slate-500 mt-2 font-medium flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-calendar-check text-blue-500"></i>
                            <span>Kegiatan:</span>
                        </span>
                        <span class="text-blue-600 font-black tracking-tight underline decoration-blue-200 underline-offset-4 break-words">{{ $kegiatan_nama }}</span>
                        <span class="text-slate-300 hidden sm:inline">|</span>
                        <span class="text-slate-500">{{ $prodi ?? 'Program Studi' }}</span>
                    </p>
                </div>
                
                @php
                    $backUrl = ($from === 'dashboard') ? route('admin.dashboard') : route('admin.lpj.index');
                    $backText = ($from === 'dashboard') ? 'Kembali ke Dashboard' : 'Kembali ke Antrian';
                @endphp
                <div class="flex-shrink-0 flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ $backUrl }}" class="flex-1 md:flex-none inline-flex items-center justify-center gap-3 px-8 py-4 bg-white hover:bg-slate-50 text-slate-600 rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest border border-slate-200 shadow-xl shadow-slate-100/50 active:scale-95">
                        <i class="fas fa-arrow-left"></i> {{ $backText }}
                    </a>
                    @if(in_array(strtolower($status), ['setuju', 'disetujui', 'selesai']))
                    <a href="{{ route('cetak.lpj', $id) }}" target="_blank" class="flex-1 md:flex-none inline-flex items-center justify-center gap-3 px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest border border-emerald-500 shadow-xl shadow-emerald-100 active:scale-95">
                        <i class="fas fa-print"></i> Cetak LPJ
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stepper Progress --}}
        <div class="px-8 md:px-12 py-8 bg-slate-50/30 border-b border-slate-50">
            <div class="relative flex justify-between items-center max-w-4xl mx-auto">
                <div class="absolute top-1/2 left-0 w-full h-1.5 bg-slate-100 -translate-y-1/2 z-0 rounded-full"></div>
                @php
                    $s = strtolower(str_replace(' ', '_', $status));
                    $progressWidth = '0%';
                    if (in_array($s, ['menunggu_upload', 'siap_submit', 'draft'])) $progressWidth = '0%';
                    elseif (in_array($s, ['menunggu', 'review', 'revisi', 'telah_direvisi'])) $progressWidth = '50%';
                    elseif (in_array($s, ['selesai', 'disetujui', 'setuju', 'lpj_disetujui'])) $progressWidth = '100%';
                @endphp
                <div class="absolute top-1/2 left-0 h-1.5 bg-{{ $statusColor }}-500 -translate-y-1/2 z-0 transition-all duration-1000 rounded-full" style="width: {{ $progressWidth }}"></div>
                
                @foreach(['Penyusunan', 'Verifikasi', 'Selesai'] as $index => $step)
                    @php
                        $stepActive = false;
                        $stepDone = false;
                        if ($index == 0) {
                            $stepDone = in_array($s, ['menunggu', 'review', 'revisi', 'telah_direvisi', 'selesai', 'disetujui', 'setuju', 'lpj_disetujui']);
                            $stepActive = in_array($s, ['menunggu_upload', 'siap_submit', 'draft']);
                        } elseif ($index == 1) {
                            $stepDone = in_array($s, ['selesai', 'disetujui', 'setuju', 'lpj_disetujui']);
                            $stepActive = in_array($s, ['menunggu', 'review', 'revisi', 'telah_direvisi']);
                        } elseif ($index == 2) {
                            $stepDone = in_array($s, ['selesai', 'disetujui', 'setuju', 'lpj_disetujui']);
                            $stepActive = false;
                        }
                    @endphp
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl {{ $stepDone ? 'bg-'.$statusColor.'-500 text-white' : ($stepActive ? 'bg-white border-4 border-'.$statusColor.'-500 text-'.$statusColor.'-500 shadow-lg shadow-'.$statusColor.'-100' : 'bg-white border-4 border-slate-200 text-slate-300') }} flex items-center justify-center transition-all duration-500">
                            @if($stepDone) <i class="fas fa-check"></i> @else <span class="text-sm font-black">{{ $index + 1 }}</span> @endif
                        </div>
                        <span class="absolute -bottom-8 text-[10px] font-black uppercase tracking-widest whitespace-nowrap {{ $stepDone || $stepActive ? 'text-slate-800' : 'text-slate-400' }}">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-8 md:p-12">
            @if($isEditable)
            <!-- Interactive Guide -->
            <div class="mb-12 p-6 bg-blue-600 rounded-3xl text-white relative overflow-hidden shadow-2xl shadow-blue-200">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="flex items-start gap-5 relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0 backdrop-blur-md border border-white/20">
                        <i class="fas fa-lightbulb text-xl text-blue-100"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-black uppercase tracking-widest mb-1">Panduan Digital Realisasi</h4>
                        <p class="text-xs text-blue-50 leading-relaxed font-medium max-w-2xl">
                            Pastikan nominal yang Anda masukkan sesuai dengan bukti fisik. Sistem akan melakukan validasi otomatis antara total anggaran dan total realisasi sebelum pengiriman.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <form id="form-lpj-submit" action="{{ route('admin.lpj.store') }}" method="POST" enctype="multipart/form-data" class="space-y-16">
                @csrf
                <input type="hidden" name="kegiatan_id" value="{{ $id }}">

                {{-- Accordion 1: RAB KEGIATAN --}}
                <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm overflow-hidden mb-8 transition-all duration-300">
                    <button type="button" onclick="document.getElementById('rab-kegiatan-content').classList.toggle('hidden'); document.getElementById('rab-chevron').classList.toggle('rotate-180')" class="w-full px-8 sm:px-10 py-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-left group transition-all hover:bg-slate-100/50 outline-none">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                                <i class="fas fa-shopping-bag text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-slate-800 uppercase tracking-tight">RAB KEGIATAN</h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Rencana Anggaran Biaya awal yang disetujui</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center shadow-sm text-slate-400 group-hover:text-slate-600 group-hover:border-slate-300 transition-all">
                            <i id="rab-chevron" class="fas fa-chevron-down transition-transform duration-300"></i>
                        </div>
                    </button>

                    <div id="rab-kegiatan-content" class="hidden p-8 sm:p-10 space-y-12 bg-white">
                        @foreach($rab_items as $kategori => $items)
                            @php $subtotal_anggaran_rab = 0; @endphp
                            <div class="relative">
                                <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-blue-600 border border-slate-200 shadow-sm">
                                            <i class="fas fa-folder-open text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest leading-none">{{ $kategori }}</h4>
                                            <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Kategori Belanja Operasional</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @foreach($items as $item)
                                            @php $subtotal_anggaran_rab += $item['total'] ?? ($item['vol1'] * ($item['vol2'] ?? 1) * $item['harga']); @endphp
                                        @endforeach
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">Subtotal</span>
                                        <span class="text-xs font-black text-emerald-600">{{ formatRupiah($subtotal_anggaran_rab) }}</span>
                                    </div>
                                </div>

                                <div class="overflow-x-auto border border-slate-100 rounded-2xl shadow-sm bg-white">
                                    <table class="min-w-[1000px] w-full text-left table-auto border-separate border-spacing-y-2 px-6 pb-4">
                                        <thead>
                                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-wider">
                                                <th class="pb-2 pt-4 pl-2">Uraian</th>
                                                <th class="pb-2 pt-4">Rincian</th>
                                                <th class="pb-2 pt-4 text-center w-20">Vol 1</th>
                                                <th class="pb-2 pt-4 text-center w-20">Sat 1</th>
                                                <th class="pb-2 pt-4 text-center w-20">Vol 2</th>
                                                <th class="pb-2 pt-4 text-center w-20">Sat 2</th>
                                                <th class="pb-2 pt-4 text-center w-36">Harga (RP)</th>
                                                <th class="pb-2 pt-4 text-right pr-4 w-40">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white">
                                            @foreach($items as $item)
                                                @php 
                                                    $anggaran_item = $item['total'] ?? ($item['vol1'] * ($item['vol2'] ?? 1) * $item['harga']);
                                                @endphp
                                                <tr class="hover:bg-slate-50/30 transition-colors">
                                                    <td class="py-1 pr-3 pl-2">
                                                        <div class="border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-600 bg-white w-full truncate" title="{{ $item['uraian'] }}">{{ $item['uraian'] }}</div>
                                                    </td>
                                                    <td class="py-1 pr-3">
                                                        <div class="border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-600 bg-white w-full truncate" title="{{ $item['rincian'] }}">{{ $item['rincian'] }}</div>
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        <div class="border border-slate-200 rounded-xl py-2 text-xs font-semibold text-slate-600 bg-white text-center w-full">{{ number_format($item['vol1'], 0) }}</div>
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        <div class="border border-slate-200 rounded-xl py-2 text-xs font-semibold text-slate-600 bg-white text-center w-full uppercase text-[10px]">{{ $item['sat1'] }}</div>
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        <div class="border border-slate-200 rounded-xl py-2 text-xs font-semibold text-slate-600 bg-white text-center w-full">{{ number_format($item['vol2'] ?? 1, 0) }}</div>
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        <div class="border border-slate-200 rounded-xl py-2 text-xs font-semibold text-slate-600 bg-white text-center w-full uppercase text-[10px]">{{ $item['sat2'] ?? '-' }}</div>
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        <div class="border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-600 bg-white text-center w-full">{{ number_format($item['harga'], 0, ',', '.') }}</div>
                                                    </td>
                                                    <td class="py-1 text-right pr-4">
                                                        <span class="text-xs font-black text-slate-700 leading-tight block">{{ formatRupiah($anggaran_item) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Accordion: REALISASI TANGGAL PELAKSANAAN --}}
                <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm overflow-hidden mb-8 transition-all duration-300">
                    <button type="button" onclick="document.getElementById('realisasi-tanggal-content').classList.toggle('hidden'); document.getElementById('tanggal-chevron').classList.toggle('rotate-180')" class="w-full px-8 sm:px-10 py-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-left group transition-all hover:bg-slate-100/50 outline-none">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                                <i class="fas fa-calendar-alt text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-slate-800 uppercase tracking-tight">REALISASI TANGGAL PELAKSANAAN</h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Penyesuaian tanggal mulai dan tanggal selesai riil kegiatan</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center shadow-sm text-slate-400 group-hover:text-slate-600 group-hover:border-slate-300 transition-all">
                            <i id="tanggal-chevron" class="fas fa-chevron-down transition-transform duration-300"></i>
                        </div>
                    </button>

                    <div id="realisasi-tanggal-content" class="p-8 sm:p-10 bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Planned/Rencana Dates Info -->
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 flex flex-col justify-between">
                                <div>
                                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Waktu Pelaksanaan Asli (Rencana)</h4>
                                    <p class="text-xs font-bold text-slate-600 leading-relaxed mb-2">
                                        Tanggal pelaksanaan yang diajukan
                                    </p>
                                </div>
                                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-200">
                                    <div>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Tanggal Mulai</span>
                                        <span class="text-xs font-black text-slate-700">{{ \Carbon\Carbon::parse($tanggal_mulai)->format('d F Y') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Tanggal Selesai</span>
                                        <span class="text-xs font-black text-slate-700">{{ \Carbon\Carbon::parse($tanggal_selesai)->format('d F Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Realization/Realisasi Date Inputs -->
                            <div class="p-6 bg-blue-50/20 rounded-3xl border border-blue-100/50 flex flex-col justify-between">
                                <div>
                                    <h4 class="text-xs font-black text-blue-500 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                                        <i class="fas fa-calendar-check"></i> Waktu Pelaksanaan Riil (Realisasi)
                                    </h4>
                                    <p class="text-xs font-bold text-slate-500 leading-relaxed mb-6">
                                        Pilih tanggal mulai dan tanggal selesai realisasi kegiatan.
                                    </p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="realisasi_tanggal_mulai" class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Tanggal Mulai Riil</label>
                                        @if($isEditable)
                                            <input type="date" 
                                                   name="realisasi_tanggal_mulai" 
                                                   id="realisasi_tanggal_mulai" 
                                                   value="{{ $realisasi_tanggal_mulai ?? \Carbon\Carbon::parse($tanggal_mulai)->format('Y-m-d') }}"
                                                   class="w-full px-4 py-3 text-xs font-bold border-2 border-slate-100 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all bg-white text-slate-700" 
                                                   required>
                                        @else
                                            <div class="px-4 py-3 text-xs font-black border border-slate-200 bg-slate-50 rounded-2xl text-slate-700">
                                                {{ $realisasi_tanggal_mulai ? \Carbon\Carbon::parse($realisasi_tanggal_mulai)->format('d F Y') : '-' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <label for="realisasi_tanggal_selesai" class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Tanggal Selesai Riil</label>
                                        @if($isEditable)
                                            <input type="date" 
                                                   name="realisasi_tanggal_selesai" 
                                                   id="realisasi_tanggal_selesai" 
                                                   value="{{ $realisasi_tanggal_selesai ?? \Carbon\Carbon::parse($tanggal_selesai)->format('Y-m-d') }}"
                                                   class="w-full px-4 py-3 text-xs font-bold border-2 border-slate-100 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all bg-white text-slate-700" 
                                                   required>
                                        @else
                                            <div class="px-4 py-3 text-xs font-black border border-slate-200 bg-slate-50 rounded-2xl text-slate-700">
                                                {{ $realisasi_tanggal_selesai ? \Carbon\Carbon::parse($realisasi_tanggal_selesai)->format('d F Y') : '-' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @php
                                    $tglMulaiPlanAdmin = \Carbon\Carbon::parse($tanggal_mulai);
                                    $tglSelesaiPlanAdmin = \Carbon\Carbon::parse($tanggal_selesai);
                                    $tglMulaiRealAdmin = \Carbon\Carbon::parse($realisasi_tanggal_mulai ?? $tanggal_mulai);
                                    $tglSelesaiRealAdmin = \Carbon\Carbon::parse($realisasi_tanggal_selesai ?? $tanggal_selesai);

                                    $isSameAdmin = false;
                                    $statusTextAdmin = '';
                                    
                                    if ($tglMulaiRealAdmin->equalTo($tglMulaiPlanAdmin) && $tglSelesaiRealAdmin->equalTo($tglSelesaiPlanAdmin)) {
                                        $isSameAdmin = true;
                                        $statusTextAdmin = 'JADWAL SESUAI RENCANA';
                                    } else {
                                        $durasiPlanAdmin = $tglMulaiPlanAdmin->diffInDays($tglSelesaiPlanAdmin) + 1;
                                        $durasiRealAdmin = $tglMulaiRealAdmin->diffInDays($tglSelesaiRealAdmin) + 1;
                                        
                                        $selisihHariAdmin = $durasiRealAdmin - $durasiPlanAdmin;
                                        $shiftMulaiAdmin = $tglMulaiPlanAdmin->diffInDays($tglMulaiRealAdmin, false);

                                        $reasonsAdmin = [];
                                        if ($shiftMulaiAdmin < 0) {
                                            $reasonsAdmin[] = abs($shiftMulaiAdmin) . ' HARI LEBIH AWAL DIMULAI';
                                        } elseif ($shiftMulaiAdmin > 0) {
                                            $reasonsAdmin[] = $shiftMulaiAdmin . ' HARI LEBIH LAMBAT DIMULAI';
                                        }

                                        if ($selisihHariAdmin > 0) {
                                            $reasonsAdmin[] = abs($selisihHariAdmin) . ' HARI LEBIH LAMA';
                                        } elseif ($selisihHariAdmin < 0) {
                                            $reasonsAdmin[] = abs($selisihHariAdmin) . ' HARI LEBIH SINGKAT';
                                        }

                                        if (empty($reasonsAdmin)) {
                                            $statusTextAdmin = 'JADWAL BERGESER ' . abs($shiftMulaiAdmin) . ' HARI';
                                        } else {
                                            $statusTextAdmin = implode(' & ', array_map('strtoupper', $reasonsAdmin));
                                        }
                                    }
                                @endphp

                                <div id="realisasi-date-status-container" class="mt-5 pt-3 border-t border-blue-100/50 flex items-center justify-between w-full">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status Pelaksanaan</span>
                                    <div id="realisasi-date-badge">
                                        @if($isSameAdmin)
                                            <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-xl flex items-center gap-1">
                                                <i class="fas fa-check-circle text-[10px]"></i> {{ $statusTextAdmin }}
                                            </span>
                                        @else
                                            <span class="text-[9px] font-black text-amber-600 bg-amber-50 border border-amber-200/60 px-2.5 py-1 rounded-xl flex items-center gap-1 animate-pulse">
                                                <i class="fas fa-exclamation-triangle text-[10px]"></i> {{ $statusTextAdmin }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Accordion 2: REALISASI KEGIATAN --}}
                <div class="bg-white border border-slate-200 rounded-[2rem] shadow-sm overflow-hidden mb-8 transition-all duration-300">
                    <button type="button" onclick="document.getElementById('realisasi-kegiatan-content').classList.toggle('hidden'); document.getElementById('realisasi-chevron').classList.toggle('rotate-180')" class="w-full px-8 sm:px-10 py-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-left group transition-all hover:bg-slate-100/50 outline-none">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform">
                                <i class="fas fa-file-invoice-dollar text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-black text-slate-800 uppercase tracking-tight">REALISASI KEGIATAN</h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Pelaporan pengeluaran riil beserta bukti transaksi</p>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center shadow-sm text-slate-400 group-hover:text-slate-600 group-hover:border-slate-300 transition-all">
                            <i id="realisasi-chevron" class="fas fa-chevron-down transition-transform duration-300 rotate-180"></i>
                        </div>
                    </button>

                    <div id="realisasi-kegiatan-content" class="p-8 sm:p-10 space-y-12 bg-white">
                        @php $grand_total_realisasi = 0; @endphp
                        @foreach($rab_items as $kategori => $items)
                            @php $subtotal_anggaran = 0; $subtotal_realisasi = 0; @endphp
                            <div class="relative">
                                <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-blue-600 border border-slate-200 shadow-sm">
                                            <i class="fas fa-folder-open text-xs"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest leading-none">{{ $kategori }}</h4>
                                            <p class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Kategori Belanja Operasional</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @foreach($items as $index => $item)
                                            @php 
                                                $subtotal_anggaran += $item['anggaran_original'];
                                                $subtotal_realisasi += $item['realisasi'] ?? 0;
                                            @endphp
                                        @endforeach
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block">Subtotal Realisasi</span>
                                        <span class="text-xs font-black text-blue-600">{{ formatRupiah($subtotal_realisasi) }}</span>
                                    </div>
                                </div>

                                <div class="overflow-x-auto border border-slate-100 rounded-2xl shadow-sm bg-white">
                                    <table class="min-w-[1200px] w-full text-left table-auto border-separate border-spacing-y-2 px-6 pb-4">
                                        <thead>
                                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-wider">
                                                <th class="pb-2 pt-4 pl-2">Uraian</th>
                                                <th class="pb-2 pt-4">Rincian</th>
                                                <th class="pb-2 pt-4 text-center w-20">Vol 1</th>
                                                <th class="pb-2 pt-4 text-center w-20">Sat 1</th>
                                                <th class="pb-2 pt-4 text-center w-20">Vol 2</th>
                                                <th class="pb-2 pt-4 text-center w-20">Sat 2</th>
                                                <th class="pb-2 pt-4 text-center w-36">Harga (RP)</th>
                                                <th class="pb-2 pt-4 text-right pr-4 w-40">Anggaran</th>
                                                <th class="pb-2 pt-4 text-right pr-4 w-48 text-blue-500">Realisasi (Input)</th>
                                                <th class="pb-2 pt-4 text-center w-60">Lampiran / Feedback</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white">
                                            @foreach($items as $index => $item)
                                                @php 
                                                    $anggaran = $item['anggaran_original'];
                                                    $realisasi = $item['realisasi'] ?? 0;
                                                @endphp
                                                <tr class="hover:bg-slate-50/30 transition-colors">
                                                    <td class="py-1 pr-3 pl-2">
                                                        @if($isEditable)
                                                            <input type="hidden" name="lpj_item_id[{{ $kategori }}][{{ $index }}]" value="{{ $item['lpj_item_id'] }}">
                                                            <input type="hidden" name="rab_item_id[{{ $kategori }}][{{ $index }}]" value="{{ $item['rab_item_id'] }}">
                                                            <input type="text" 
                                                                   name="uraian[{{ $kategori }}][{{ $index }}]" 
                                                                   value="{{ $item['uraian'] }}" 
                                                                   class="border-2 border-slate-100 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 bg-white w-full focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                                                   required>
                                                        @else
                                                            <div class="border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 bg-white w-full truncate" title="{{ $item['uraian'] }}">{{ $item['uraian'] }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 pr-3">
                                                        @if($isEditable)
                                                            <input type="text" 
                                                                   name="rincian[{{ $kategori }}][{{ $index }}]" 
                                                                   value="{{ $item['rincian'] }}" 
                                                                   class="border-2 border-slate-100 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 bg-white w-full focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                                                   required>
                                                        @else
                                                            <div class="border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 bg-white w-full truncate" title="{{ $item['rincian'] }}">{{ $item['rincian'] }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        @if($isEditable)
                                                            <input type="number" 
                                                                   step="any" 
                                                                   name="vol1[{{ $kategori }}][{{ $index }}]" 
                                                                   value="{{ (float) $item['vol1'] }}" 
                                                                   class="vol1-input border-2 border-slate-100 rounded-xl py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                                                   required>
                                                        @else
                                                            <div class="border border-slate-200 rounded-xl py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full">{{ number_format($item['vol1'], 0) }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        @if($isEditable)
                                                            <input type="text" 
                                                                   name="sat1[{{ $kategori }}][{{ $index }}]" 
                                                                   value="{{ $item['sat1'] }}" 
                                                                   class="border-2 border-slate-100 rounded-xl py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full uppercase focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                                                   required>
                                                        @else
                                                            <div class="border border-slate-200 rounded-xl py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full uppercase text-[10px]">{{ $item['sat1'] }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        @if($isEditable)
                                                            <input type="number" 
                                                                   step="any" 
                                                                   name="vol2[{{ $kategori }}][{{ $index }}]" 
                                                                   value="{{ (float) ($item['vol2'] ?? 1) }}" 
                                                                   class="vol2-input border-2 border-slate-100 rounded-xl py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                                                   required>
                                                        @else
                                                            <div class="border border-slate-200 rounded-xl py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full">{{ number_format($item['vol2'] ?? 1, 0) }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        @if($isEditable)
                                                            <input type="text" 
                                                                   name="sat2[{{ $kategori }}][{{ $index }}]" 
                                                                   value="{{ $item['sat2'] ?? '' }}" 
                                                                   class="border-2 border-slate-100 rounded-xl py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full uppercase focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all">
                                                        @else
                                                            <div class="border border-slate-200 rounded-xl py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full uppercase text-[10px]">{{ $item['sat2'] ?? '-' }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 pr-3 text-center">
                                                        @if($isEditable)
                                                            <input type="number" 
                                                                   step="any" 
                                                                   name="harga[{{ $kategori }}][{{ $index }}]" 
                                                                   value="{{ (int) $item['harga'] }}" 
                                                                   class="harga-input border-2 border-slate-100 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full focus:border-blue-500 focus:ring-4 focus:ring-blue-50 outline-none transition-all"
                                                                   required>
                                                        @else
                                                            <div class="border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 bg-white text-center w-full">{{ number_format($item['harga'], 0, ',', '.') }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="py-1 text-right pr-4">
                                                        <span class="text-xs font-black text-slate-700 leading-tight block">{{ formatRupiah($anggaran) }}</span>
                                                        <input type="hidden" class="anggaran-disetujui-input" value="{{ $anggaran }}">
                                                    </td>
                                                    <td class="py-1 pr-4">
                                                        @if($isEditable)
                                                            <div class="relative group/input max-w-[200px] ml-auto">
                                                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-[10px] font-black group-focus-within/input:text-blue-500 transition-colors">Rp</span>
                                                                <input type="number" 
                                                                       name="realisasi[{{ $kategori }}][{{ $index }}]"
                                                                       class="realisasi-input w-full pl-10 pr-4 py-3 text-xs text-right font-black border-2 border-slate-100 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all outline-none bg-white"
                                                                       value="{{ (int) $realisasi }}" 
                                                                       data-anggaran="{{ $anggaran }}"
                                                                       required>
                                                            </div>
                                                        @else
                                                            <span class="text-xs font-black text-{{ $statusColor }}-600 tracking-tighter block text-right">{{ formatRupiah($realisasi) }}</span>
                                                        @endif
                                                        
                                                        @php
                                                            $selisih = $realisasi - $anggaran;
                                                        @endphp
                                                        <div class="selisih-badge-container text-[9px] font-black uppercase text-right mt-1.5 transition-all">
                                                            @if($selisih > 0)
                                                                <span class="text-rose-500 bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-100"><i class="fas fa-arrow-up mr-0.5"></i> +{{ formatRupiah($selisih) }} (Lebih)</span>
                                                            @elseif($selisih < 0)
                                                                <span class="text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100"><i class="fas fa-arrow-down mr-0.5"></i> -{{ formatRupiah(abs($selisih)) }} (Hemat)</span>
                                                            @else
                                                                <span class="text-slate-400 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-100"><i class="fas fa-check-circle mr-0.5"></i> Sesuai</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="py-1 text-center">
                                                        <div class="flex items-center justify-center gap-2">
                                                            @if($isEditable)
                                                                <label class="cursor-pointer group/upload relative inline-block">
                                                                    <input type="file" name="bukti[{{ $kategori }}][{{ $index }}]" class="hidden bukti-input">
                                                                    <div class="w-12 h-12 rounded-2xl bg-white border-2 border-slate-100 text-slate-400 flex items-center justify-center group-hover/upload:bg-blue-600 group-hover/upload:text-white group-hover/upload:border-blue-600 transition-all shadow-sm active:scale-90">
                                                                        <i class="fas fa-camera text-xs"></i>
                                                                    </div>
                                                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white {{ !empty($item['file_bukti']) ? '' : 'hidden' }} file-badge"></div>
                                                                </label>
                                                            @else
                                                                @if(!empty($item['file_bukti']))
                                                                    <a href="{{ asset('storage/' . $item['file_bukti']) }}" target="_blank" class="w-12 h-12 rounded-2xl bg-{{ $statusColor }}-50 text-{{ $statusColor }}-600 border border-{{ $statusColor }}-100 hover:bg-{{ $statusColor }}-600 hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center">
                                                                        <i class="fas fa-file-invoice text-xs"></i>
                                                                    </a>
                                                                @else
                                                                    <span class="text-[10px] font-bold text-slate-400 italic">No File</span>
                                                                @endif
                                                            @endif

                                                            <button type="button" onclick="toggleFeedback('{{ $item['id'] ?? $index }}')" class="w-12 h-12 rounded-2xl {{ !empty($item['catatan_item']) ? 'bg-amber-100 text-amber-600 border-amber-200' : 'bg-slate-50 text-slate-400 border-slate-100' }} hover:bg-amber-500 hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center relative">
                                                                <i class="fas fa-comment-dots text-xs"></i>
                                                                @if(!empty($item['catatan_item']))
                                                                    <span class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 rounded-full border-2 border-white animate-bounce"></span>
                                                                @endif
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr id="tr-feedback-{{ $item['id'] ?? $index }}" class="hidden bg-slate-50/50">
                                                    <td colspan="10" class="px-6 py-4">
                                                        <div id="feedback-{{ $item['id'] ?? $index }}" class="p-5 bg-slate-50 rounded-2xl border border-slate-200 shadow-inner animate-fade-in text-left">
                                                            <div class="flex items-center gap-2 mb-3">
                                                                <i class="fas fa-pen-nib text-[10px] text-blue-600"></i>
                                                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Catatan Perbaikan dari Bendahara</label>
                                                            </div>
                                                            <div class="w-full p-4 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 shadow-sm leading-relaxed">
                                                                {{ $item['catatan_item'] ?? 'Tidak ada catatan perbaikan untuk item ini.' }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @php $grand_total_realisasi += $subtotal_realisasi; @endphp
                        @endforeach
                    </div>
                </div>

                <!-- Dynamic Summary Dashboard -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2 bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl shadow-slate-300">
                        <div class="absolute right-0 bottom-0 w-64 h-64 bg-blue-500/20 rounded-full -mr-32 -mb-32 blur-3xl"></div>
                        <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-10">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Anggaran Disetujui</span>
                                <div class="text-4xl font-black tracking-tighter">{{ formatRupiah($grand_total_anggaran) }}</div>
                                <div class="mt-4 flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dana Tersedia</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3">Realisasi Dilaporkan</span>
                                <div id="grand-total-realisasi" class="text-4xl font-black tracking-tighter text-blue-400 transition-all duration-500" data-anggaran="{{ $grand_total_anggaran }}">
                                    {{ formatRupiah($grand_total_realisasi) }}
                                </div>
                                <div id="diff-indicator" class="mt-4 text-[10px] font-black uppercase tracking-widest">
                                    <span class="text-slate-500">Menunggu Input Data...</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 pt-6 border-t border-white/10 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400">
                                    <i class="fas fa-fingerprint text-xs"></i>
                                </div>
                                <div>
                                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">Kode MAK Aktif</span>
                                    <span class="text-xs font-mono font-bold text-blue-100 tracking-wider">{{ $kode_mak ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="px-3 py-1 bg-emerald-500/10 rounded-lg text-emerald-400 text-[9px] font-black uppercase tracking-widest border border-emerald-500/20">
                                <i class="fas fa-check-circle"></i> Terverifikasi
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] border border-slate-100 p-10 shadow-xl shadow-slate-100 flex flex-col justify-center items-center text-center">
                        <div id="status-circle" class="w-24 h-24 rounded-full border-8 border-slate-50 flex items-center justify-center mb-6 transition-all duration-500">
                            <i id="status-icon" class="fas fa-hourglass-half text-slate-200 text-2xl"></i>
                        </div>
                        <h4 id="status-title" class="text-sm font-black text-slate-800 uppercase tracking-widest mb-2">Pengecekan Data</h4>
                        <p id="status-desc" class="text-[10px] font-bold text-slate-400 uppercase leading-relaxed">Pastikan seluruh input realisasi telah terisi dan bukti telah diunggah.</p>
                    </div>
                </div>

                @if($isEditable)
                    <div class="flex flex-col md:flex-row gap-6 justify-between items-center mt-12 pt-12 border-t border-slate-50">
                        <button type="button" class="text-xs font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Draft
                        </button>
                        
                        <button type="submit" id="btn-submit-lpj" class="w-full md:w-auto px-16 py-5 bg-blue-600 hover:bg-blue-700 text-white rounded-[1.5rem] transition-all font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-blue-200 hover:-translate-y-1 active:scale-95 group flex items-center justify-center gap-4">
                            <span>Kirim LPJ Digital</span>
                            <i class="fas fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                        </button>
                    </div>
                @else
                    <div class="mt-12 pt-12 border-t border-slate-50 text-center">
                        <div class="inline-flex items-center gap-3 px-6 py-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <i class="fas fa-shield-check text-{{ $statusColor }}-500"></i>
                            @if(in_array(strtolower($status), ['setuju', 'disetujui', 'selesai']))
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Dokumen ini telah disetujui dan diverifikasi.</span>
                            @else
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Dokumen ini telah dikunci dan sedang dalam tahap verifikasi final.</span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleFeedback(id) {
    const el = document.getElementById(`tr-feedback-${id}`);
    if (el) {
        el.classList.toggle('hidden');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const totalAnggaran = parseFloat(document.getElementById('grand-total-realisasi').dataset.anggaran);
    const display = document.getElementById('grand-total-realisasi');
    const diffIndicator = document.getElementById('diff-indicator');
    const statusCircle = document.getElementById('status-circle');
    const statusIcon = document.getElementById('status-icon');
    const statusTitle = document.getElementById('status-title');
    const statusDesc = document.getElementById('status-desc');

    function formatRupiahJs(angka) {
        return 'Rp ' + Math.round(Math.abs(angka)).toLocaleString('id-ID');
    }
    
    function updateVisuals() {
        let total = 0;
        const inputs = document.querySelectorAll('.realisasi-input');
        if (inputs.length > 0) {
            inputs.forEach(input => {
                const val = parseFloat(input.value) || 0;
                total += val;

                // Update individual item-level selisih badge
                const cell = input.closest('td');
                if (cell) {
                    const badgeContainer = cell.querySelector('.selisih-badge-container');
                    const anggaranItem = parseFloat(input.dataset.anggaran) || 0;
                    const diffItem = val - anggaranItem;

                    if (badgeContainer) {
                        if (diffItem > 0) {
                            badgeContainer.innerHTML = `<span class="text-rose-500 bg-rose-50 px-2 py-0.5 rounded-lg border border-rose-100"><i class="fas fa-arrow-up mr-0.5"></i> +${formatRupiahJs(diffItem)} (Lebih)</span>`;
                        } else if (diffItem < 0) {
                            badgeContainer.innerHTML = `<span class="text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100"><i class="fas fa-arrow-down mr-0.5"></i> -${formatRupiahJs(diffItem)} (Hemat)</span>`;
                        } else {
                            badgeContainer.innerHTML = `<span class="text-slate-400 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-100"><i class="fas fa-check-circle mr-0.5"></i> Sesuai</span>`;
                        }
                    }
                }
            });
        } else {
            total = parseFloat("{{ $grand_total_realisasi }}") || 0;
        }
        
        display.textContent = 'Rp ' + total.toLocaleString('id-ID');
        const diff = total - totalAnggaran;
        const currentStatus = "{{ strtolower($status) }}";
        
        if (Math.abs(diff) < 0.1) {
            display.className = 'text-4xl font-black tracking-tighter text-emerald-400';
            diffIndicator.innerHTML = '<span class="text-emerald-400"><i class="fas fa-check-circle mr-1"></i> NOMINAL SESUAI ANGGARAN</span>';
            statusCircle.className = 'w-24 h-24 rounded-full border-8 border-emerald-500 flex items-center justify-center mb-6 shadow-lg shadow-emerald-100';
            statusIcon.className = 'fas fa-check text-emerald-500 text-2xl';
            
            if (['setuju', 'disetujui', 'selesai'].includes(currentStatus)) {
                statusTitle.textContent = 'LPJ Disetujui';
                statusDesc.textContent = 'Laporan telah disetujui tanpa revisi.';
            } else if (currentStatus === 'menunggu_upload' || currentStatus === 'menunggu upload') {
                statusTitle.textContent = 'Perlu Unggah Bukti';
                statusDesc.textContent = 'Total realisasi sesuai anggaran, namun Anda belum mengunggah seluruh bukti.';
                statusCircle.className = 'w-24 h-24 rounded-full border-8 border-orange-500 flex items-center justify-center mb-6 shadow-lg shadow-orange-100';
                statusIcon.className = 'fas fa-upload text-orange-500 text-2xl';
            } else if (currentStatus === 'revisi') {
                statusTitle.textContent = 'Revisi Diperlukan';
                statusDesc.textContent = 'Mohon perbaiki bukti atau nominal yang belum sesuai catatan.';
                statusCircle.className = 'w-24 h-24 rounded-full border-8 border-amber-500 flex items-center justify-center mb-6 shadow-lg shadow-amber-100';
                statusIcon.className = 'fas fa-exclamation-triangle text-amber-500 text-2xl';
            } else if (['menunggu', 'review'].includes(currentStatus)) {
                statusTitle.textContent = 'Menunggu Verifikasi';
                statusDesc.textContent = 'Laporan Anda sedang ditinjau oleh pihak terkait.';
                statusCircle.className = 'w-24 h-24 rounded-full border-8 border-blue-500 flex items-center justify-center mb-6 shadow-lg shadow-blue-100';
                statusIcon.className = 'fas fa-hourglass-half text-blue-500 text-2xl';
            } else {
                statusTitle.textContent = 'Siap Dikirim';
                statusDesc.textContent = 'Total realisasi telah sesuai dengan pagu anggaran.';
            }
        } else if (diff > 0) {
            display.className = 'text-4xl font-black tracking-tighter text-rose-500';
            diffIndicator.innerHTML = `<span class="text-rose-500"><i class="fas fa-times-circle mr-1"></i> REALISASI MELEBIHI ANGGARAN (${formatRupiahJs(diff)} LEBIH)</span>`;
            statusCircle.className = 'w-24 h-24 rounded-full border-8 border-rose-500 flex items-center justify-center mb-6 shadow-lg shadow-rose-100';
            statusIcon.className = 'fas fa-exclamation-circle text-rose-500 text-2xl';
            statusTitle.textContent = 'Dana Berlebih';
            statusDesc.textContent = 'Total realisasi melebihi pagu anggaran yang disetujui.';
        } else {
            display.className = 'text-4xl font-black tracking-tighter text-blue-400';
            diffIndicator.innerHTML = `<span class="text-blue-400"><i class="fas fa-info-circle mr-1"></i> REALISASI KURANG DARI ANGGARAN (${formatRupiahJs(diff)} HEMAT)</span>`;
            statusCircle.className = 'w-24 h-24 rounded-full border-8 border-blue-500 flex items-center justify-center mb-6 shadow-lg shadow-blue-100';
            statusIcon.className = 'fas fa-hourglass-half text-blue-500 text-2xl animate-spin-slow';
            statusTitle.textContent = 'Belum Lengkap';
            statusDesc.textContent = 'Total realisasi belum mencapai pagu anggaran.';
        }
    }

    document.querySelectorAll('.realisasi-input').forEach(input => {
        input.addEventListener('input', updateVisuals);
    });

    // Auto-recalculate realisasi nominal when Vol 1, Vol 2, or Harga Satuan is changed
    document.querySelectorAll('.vol1-input, .vol2-input, .harga-input').forEach(input => {
        input.addEventListener('input', () => {
            const row = input.closest('tr');
            if (row) {
                const vol1Input = row.querySelector('.vol1-input');
                const vol2Input = row.querySelector('.vol2-input');
                const hargaInput = row.querySelector('.harga-input');
                
                const vol1 = vol1Input ? parseFloat(vol1Input.value) || 0 : 0;
                const vol2 = vol2Input ? parseFloat(vol2Input.value) || 0 : 1;
                const harga = hargaInput ? parseFloat(hargaInput.value) || 0 : 0;
                
                const calculatedRealisasi = vol1 * vol2 * harga;
                
                const realisasiInput = row.querySelector('.realisasi-input');
                if (realisasiInput) {
                    realisasiInput.value = Math.round(calculatedRealisasi);
                    // Trigger input event to update badges and grand total
                    realisasiInput.dispatchEvent(new Event('input'));
                }
            }
        });
    });

    document.querySelectorAll('.bukti-input').forEach(input => {
        input.addEventListener('change', () => {
            const badge = input.closest('label').querySelector('.file-badge');
            if (input.files.length > 0) {
                badge.classList.remove('hidden');
                input.closest('div.w-12').classList.add('bg-emerald-50', 'border-emerald-500', 'text-emerald-500');
            }
        });
    });

    const form = document.getElementById('form-lpj-submit');
    if (form) {
        form.addEventListener('submit', (e) => {
            if (typeof Swal === 'undefined') {
                return; // Let the browser submit the form natively if SweetAlert fails to load
            }
            
            e.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi Pengiriman?',
                text: "Laporan akan dikirim ke Bendahara dan tidak dapat diubah kembali.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#2563eb',
                borderRadius: '24px'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengirim...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    form.submit();
                }
            });
        });
    }

    // Real-time Date validation & dynamic schedule shift badge
    const tglMulai = document.getElementById('realisasi_tanggal_mulai');
    const tglSelesai = document.getElementById('realisasi_tanggal_selesai');
    
    function updateDateBadge() {
        const tglMulaiPlanStr = '{{ $tanggal_mulai }}';
        const tglSelesaiPlanStr = '{{ $tanggal_selesai }}';
        
        if (!tglMulai || !tglSelesai) return;
        
        const startRealVal = tglMulai.value;
        const endRealVal = tglSelesai.value;
        
        if (!startRealVal || !endRealVal) return;
        
        const planStart = new Date(tglMulaiPlanStr);
        const planEnd = new Date(tglSelesaiPlanStr);
        const realStart = new Date(startRealVal);
        const realEnd = new Date(endRealVal);
        
        // Remove time component to compare strictly by calendar dates
        planStart.setHours(0,0,0,0);
        planEnd.setHours(0,0,0,0);
        realStart.setHours(0,0,0,0);
        realEnd.setHours(0,0,0,0);
        
        const isSame = planStart.getTime() === realStart.getTime() && planEnd.getTime() === realEnd.getTime();
        const badgeDiv = document.getElementById('realisasi-date-badge');
        if (!badgeDiv) return;
        
        if (isSame) {
            badgeDiv.innerHTML = `
                <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 border border-emerald-200/60 px-2.5 py-1 rounded-xl flex items-center gap-1">
                    <i class="fas fa-check-circle text-[10px]"></i> JADWAL SESUAI RENCANA
                </span>
            `;
        } else {
            const MS_PER_DAY = 1000 * 60 * 60 * 24;
            const durPlan = Math.round((planEnd - planStart) / MS_PER_DAY) + 1;
            const durReal = Math.round((realEnd - realStart) / MS_PER_DAY) + 1;
            
            const diffDays = durReal - durPlan;
            const shiftStart = Math.round((realStart - planStart) / MS_PER_DAY);
            
            let reasons = [];
            if (shiftStart < 0) {
                reasons.push(`${Math.abs(shiftStart)} HARI LEBIH AWAL DIMULAI`);
            } else if (shiftStart > 0) {
                reasons.push(`${shiftStart} HARI LEBIH LAMBAT DIMULAI`);
            }
            
            if (diffDays > 0) {
                reasons.push(`${Math.abs(diffDays)} HARI LEBIH LAMA`);
            } else if (diffDays < 0) {
                reasons.push(`${Math.abs(diffDays)} HARI LEBIH SINGKAT`);
            }
            
            let statusText = '';
            if (reasons.length === 0) {
                statusText = `JADWAL BERGESER ${Math.abs(shiftStart)} HARI`;
            } else {
                statusText = reasons.join(' & ');
            }
            
            badgeDiv.innerHTML = `
                <span class="text-[9px] font-black text-amber-600 bg-amber-50 border border-amber-200/60 px-2.5 py-1 rounded-xl flex items-center gap-1 animate-pulse">
                    <i class="fas fa-exclamation-triangle text-[10px]"></i> ${statusText}
                </span>
            `;
        }
    }

    if (tglMulai && tglSelesai) {
        tglSelesai.min = tglMulai.value;
        tglMulai.addEventListener('change', () => {
            tglSelesai.min = tglMulai.value;
            if (tglSelesai.value && tglSelesai.value < tglMulai.value) {
                tglSelesai.value = tglMulai.value;
            }
            updateDateBadge();
        });
        
        tglSelesai.addEventListener('change', () => {
            updateDateBadge();
        });
        
        // Initial invocation in case values pre-load differently
        updateDateBadge();
    }

    updateVisuals();
});
</script>
<style>
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-slide-up { animation: slide-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-fade-in { animation: fade-in 1s ease-out forwards; }
    .animate-spin-slow { animation: spin 3s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endpush
