@extends('layouts.app')

@section('title', 'Review & Telaah Usulan')

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
                <div class="mt-2">
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] bg-blue-50 px-3 py-1 rounded-md border border-blue-100">
                        MAK: {{ $kegiatan_data['mak_code'] ?? '000.00.0.000.000' }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('wadir.kegiatan.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-bold text-sm border border-slate-200">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @if($kegiatan->posisi_id >= 5)
                <button class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition font-bold text-sm shadow-lg shadow-emerald-200">
                    <i class="fas fa-print"></i> Cetak KAK
                </button>
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

        {{-- main content layout with two columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-16 max-w-6xl mx-auto">
            
            {{-- Left column: detail forms --}}
            <div class="lg:col-span-2 space-y-12">
                
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
                            <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan->nama_pj ?? '-') !!}</div>
                        </div>
                        <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                            <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIM/NIP Nama Penanggung Jawab</span>
                            <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan->nip ?? '-') !!}</div>
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

            {{-- Right column: Panel Persetujuan Wadir --}}
            <div class="space-y-8">
                
                {{-- RAB Total Card --}}
                <div class="bg-gradient-to-br from-blue-600 to-cyan-500 p-8 rounded-[2rem] text-white shadow-xl shadow-blue-100 relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-md">
                                <i class="fas fa-wallet text-white text-sm"></i>
                            </div>
                            <span class="text-xs font-black uppercase tracking-widest opacity-80 italic">Estimasi Anggaran</span>
                        </div>
                        <div class="text-3xl font-black mb-2 tracking-tighter">
                            @php 
                                $grand_total = 0;
                                if (!empty($rab_data)) {
                                    foreach($rab_data as $cat => $items) {
                                        foreach($items as $it) {
                                            $grand_total += $it['vol1'] * ($it['vol2'] ?? 1) * $it['harga'];
                                        }
                                    }
                                }
                            @endphp
                            {{ formatRupiah($grand_total) }}
                        </div>
                        <p class="text-[10px] font-bold opacity-60">Total Rincian Anggaran Biaya (RAB)</p>
                    </div>
                </div>

                {{-- Action Panel / Persetujuan Wadir --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden p-8 animate-reveal">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="fas fa-stamp text-blue-600"></i> Panel Telaah
                    </h3>

                    @if($kegiatan->posisi_id == 4)
                        <form id="form-review" action="{{ route('wadir.kegiatan.store', $id) }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" id="action-input" name="action" value="approve">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Catatan / Feedback</label>
                                <textarea name="notes" rows="4" placeholder="Tuliskan catatan persetujuan, revisi, atau penolakan..." 
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 outline-none transition-all font-medium text-xs leading-relaxed"></textarea>
                            </div>

                            <div class="space-y-3 pt-4">
                                <button type="button" onclick="handleAction('approve')" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-emerald-100 hover:bg-emerald-700 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i> Setujui Usulan
                                </button>
                                
                                <button type="button" onclick="handleAction('revisi')" class="w-full py-4 bg-amber-500 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-amber-100 hover:bg-amber-600 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-exclamation-circle"></i> Minta Revisi
                                </button>

                                <button type="button" onclick="handleAction('reject')" class="w-full py-4 bg-rose-500 text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-lg shadow-rose-100 hover:bg-rose-600 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-times-circle"></i> Tolak Usulan
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-10">
                            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600 shadow-xl shadow-emerald-50">
                                    <i class="fas fa-check-double text-3xl"></i>
                            </div>
                            <h4 class="text-base font-black text-slate-800 uppercase tracking-widest">Telah Diproses</h4>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-2 italic">Keputusan akhir telah dikirim.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- RINCIAN ANGGARAN BIAYA (RAB) SECTION --}}
        <div class="mt-12 pt-12 border-t border-slate-100">
            <h3 class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-3">
                <i class="fas fa-calculator text-blue-500"></i> Rincian Anggaran (RAB)
            </h3>
            
            <div class="space-y-10">
                @if(!empty($rab_data))
                    @foreach($rab_data as $kategori => $items)
                        @php 
                            $subtotal = 0;
                            foreach($items as $it) {
                                $subtotal += $it['vol1'] * ($it['vol2'] ?? 1) * $it['harga'];
                            }
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

    </section>
</main>

<style>
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-slide-up { animation: slide-up 0.6s ease-out forwards; }
    .animate-fade-in { animation: fade-in 0.3s ease-out forwards; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function handleAction(type) {
        const config = {
            approve: { title: 'Setujui Usulan?', text: 'Usulan akan dilanjutkan ke tahap berikutnya.', icon: 'success', color: '#10b981', label: 'Ya, Setujui' },
            revisi:  { title: 'Minta Revisi?', text: 'Usulan akan dikembalikan ke Admin untuk diperbaiki.', icon: 'warning', color: '#f59e0b', label: 'Ya, Minta Revisi' },
            reject:  { title: 'Tolak Usulan?', text: 'Usulan akan dihentikan dan ditolak permanen.', icon: 'error', color: '#f43f5e', label: 'Ya, Tolak' }
        };

        const c = config[type];
        Swal.fire({
            title: c.title,
            text: c.text,
            icon: c.icon,
            showCancelButton: true,
            confirmButtonText: c.label,
            cancelButtonText: 'Batal',
            confirmButtonColor: c.color,
            borderRadius: '24px'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('action-input').value = type;
                document.getElementById('form-review').submit();
            }
        });
    }
</script>
@endpush
