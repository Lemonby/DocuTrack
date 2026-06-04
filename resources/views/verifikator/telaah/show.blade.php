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

    $statusColor = match(strtolower($status)) {
        'disetujui', 'selesai' => 'emerald',
        'revisi' => 'amber',
        'ditolak' => 'rose',
        'review', 'menunggu', 'menunggu verifikasi' => 'blue',
        default => 'slate'
    };

    $isDisetujui = strtolower($status) === 'disetujui';
    $isDitolak = strtolower($status) === 'ditolak';
    $isRevisi = strtolower($status) === 'revisi';
    $isMenunggu = in_array(strtolower($status), ['menunggu', 'review', 'menunggu verifikasi']);
@endphp

<main class="main-content font-poppins p-4 sm:p-6 lg:p-10 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full animate-fade-in">
    
    {{-- Status Header Alert --}}
    @if(strtolower($status) === 'revisi')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-8 rounded-r-2xl shadow-sm animate-slide-up">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <i class="fas fa-hourglass-half text-amber-500 text-lg animate-pulse"></i>
                </div>
                <div>
                    <h3 class="text-amber-800 font-black text-sm sm:text-base">Menunggu Perbaikan</h3>
                    <p class="text-amber-700 text-xs sm:text-sm mt-1 leading-relaxed">
                        Anda telah mengirimkan catatan revisi. Saat ini usulan sedang diperbaiki oleh pihak Admin/Pengusul.
                    </p>
                </div>
            </div>
        </div>
    @elseif(strtolower($status) === 'disetujui')
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-8 rounded-r-2xl shadow-sm animate-slide-up">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-emerald-800 font-black text-sm sm:text-base">Verifikasi Selesai</h3>
                    <p class="text-emerald-700 text-xs sm:text-sm">Usulan ini telah Anda setujui dan diteruskan ke tahap berikutnya.</p>
                </div>
            </div>
        </div>
    @elseif(strtolower($status) === 'ditolak')
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-8 rounded-r-2xl shadow-sm animate-slide-up">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times text-rose-600"></i>
                </div>
                <div>
                    <h3 class="text-rose-800 font-black text-sm sm:text-base">Usulan Tidak Disetujui</h3>
                    <p class="text-rose-700 text-xs sm:text-sm">Anda telah menolak usulan ini. Proses pengajuan dihentikan.</p>
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
                        @if($isRevisi) Menunggu Perbaikan Admin @else {{ $status ?? 'Pending' }} @endif
                    </span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-400 text-xs font-medium">ID USULAN: #USL-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight">Telaah Usulan Kegiatan</h2>
                <p class="text-slate-400 text-xs mt-1">Review & Telaah Usulan KAK oleh Verifikator</p>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button onclick="window.history.back()" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-bold text-sm border border-slate-200 shadow-sm active:scale-95">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
                @if($kegiatan->posisi_id >= 2 || in_array($kegiatan->status_utama_id, [5, 6, 8]))
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
                @php
                    $isSelesai = (isset($kegiatan) && $kegiatan->status_utama_id == 8) || strtolower($status) === 'selesai';
                    $progressColor = $isSelesai ? 'emerald' : $statusColor;
                    $progressWidth = $isSelesai ? 'w-full' : (strtolower($status) === 'revisi' ? 'w-1/3' : 'w-2/3');
                @endphp
                <div class="absolute top-1/2 left-0 {{ $progressWidth }} h-1 bg-{{ $progressColor }}-500 -translate-y-1/2 z-0 transition-all duration-1000"></div>
                
                @foreach(['Pengajuan', 'Verifikasi', 'Selesai'] as $index => $step)
                    @php
                        $isCompleted = $isSelesai || 
                                      ($index === 0) || 
                                      ($index === 1 && strtolower($status) !== 'revisi' && strtolower($status) !== 'menunggu');
                        $isActive = ($index === 2 && $isSelesai) ||
                                    ($index === 1 && !$isSelesai && (strtolower($status) === 'review' || strtolower($status) === 'menunggu')) ||
                                    ($index === 0 && strtolower($status) === 'revisi');
                    @endphp
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full {{ $isCompleted ? 'bg-'.$progressColor.'-500 text-white shadow-md' : ($isActive ? 'bg-white border-4 border-'.$progressColor.'-500 text-'.$progressColor.'-500 shadow-md' : 'bg-white border-4 border-slate-200 text-slate-300') }} flex items-center justify-center transition-all duration-500">
                            @if($isCompleted) <i class="fas fa-check text-sm"></i> @else <span class="text-sm font-bold">{{ $index + 1 }}</span> @endif
                        </div>
                        <span class="absolute -bottom-7 text-[10px] font-black uppercase tracking-widest {{ $isCompleted || $isActive ? 'text-slate-800' : 'text-slate-400' }}">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Single column premium layout --}}
        <div class="max-w-5xl mx-auto space-y-12">
            
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
                        <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan_data['penanggung_jawab'] ?? '') !!}</div>
                    </div>
                    <div class="relative border border-slate-200 rounded-2xl px-4 py-3 bg-white hover:border-slate-300 transition-all duration-200">
                        <span class="absolute -top-2 left-4 bg-white px-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIM/NIP Penanggung Jawab</span>
                        <div class="text-sm font-semibold text-slate-700 min-h-[1.5rem] mt-1">{!! displayValue($kegiatan_data['nip_pj'] ?? '') !!}</div>
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

                {{-- Comment field for KAK --}}
                <div class="revision-comment-field hidden mt-6 p-5 bg-amber-50/85 border border-amber-200 rounded-2xl animate-slide-up shadow-inner">
                    <label class="block text-[10px] font-black text-amber-800 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                        <i class="fas fa-comment-dots text-amber-600"></i> Catatan Revisi: Kerangka Acuan Kerja (KAK)
                    </label>
                    <textarea name="field_comments[kaks][gambaran_umum]" form="form-review" rows="2" placeholder="Tulis masukan/revisi khusus untuk bagian Kerangka Acuan Kerja (KAK) di sini..."
                        class="w-full px-4 py-3 bg-white border border-amber-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-50/50 outline-none transition-all text-xs font-semibold leading-relaxed text-slate-700"></textarea>
                </div>
            </div>

            {{-- STRATEGI PENCAPAIAN KELUARAN SECTION --}}
            <div class="space-y-6">
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

                {{-- Comment field for Strategi Pencapaian Keluaran --}}
                <div class="revision-comment-field hidden mt-6 p-5 bg-amber-50/85 border border-amber-200 rounded-2xl animate-slide-up shadow-inner">
                    <label class="block text-[10px] font-black text-amber-800 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                        <i class="fas fa-comment-dots text-amber-600"></i> Catatan Revisi: Strategi Pencapaian Keluaran
                    </label>
                    <textarea name="field_comments[kaks][metode_pelaksanaan]" form="form-review" rows="2" placeholder="Tulis masukan/revisi khusus untuk bagian Strategi Pencapaian Keluaran di sini..."
                        class="w-full px-4 py-3 bg-white border border-amber-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-50/50 outline-none transition-all text-xs font-semibold leading-relaxed text-slate-700"></textarea>
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

                {{-- Comment field for Indikator Kinerja --}}
                <div class="revision-comment-field hidden mt-6 p-5 bg-amber-50/85 border border-amber-200 rounded-2xl animate-slide-up shadow-inner">
                    <label class="block text-[10px] font-black text-amber-800 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                        <i class="fas fa-comment-dots text-amber-600"></i> Catatan Revisi: Indikator Kinerja
                    </label>
                    <textarea name="field_comments[indikator_kaks][indikator_keberhasilan]" form="form-review" rows="2" placeholder="Tulis masukan/revisi khusus untuk bagian Indikator Kinerja di sini..."
                        class="w-full px-4 py-3 bg-white border border-amber-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-50/50 outline-none transition-all text-xs font-semibold leading-relaxed text-slate-700"></textarea>
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

                {{-- Comment field for IKU --}}
                <div class="revision-comment-field hidden mt-6 p-5 bg-amber-50/85 border border-amber-200 rounded-2xl animate-slide-up shadow-inner">
                    <label class="block text-[10px] font-black text-amber-800 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                        <i class="fas fa-comment-dots text-amber-600"></i> Catatan Revisi: Indikator Kerja Utama (IKU)
                    </label>
                    <textarea name="field_comments[kaks][iku]" form="form-review" rows="2" placeholder="Tulis masukan/revisi khusus untuk bagian Indikator Kerja Utama (IKU) di sini..."
                        class="w-full px-4 py-3 bg-white border border-amber-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-50/50 outline-none transition-all text-xs font-semibold leading-relaxed text-slate-700"></textarea>
                </div>
            </div>

            {{-- RINCIAN ANGGARAN BIAYA (RAB) SECTION --}}
            <div class="space-y-8 pt-6">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Rincian Anggaran Biaya (RAB)</h3>
                
                @php 
                    $grand_total = 0;
                @endphp
                @if(!empty($rab_data))
                    <div class="space-y-12">
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
                    </div>
                @else
                    <div class="p-8 text-center text-slate-400 italic bg-white border border-slate-200 rounded-2xl shadow-sm">
                        Belum ada data anggaran biaya (RAB)
                    </div>
                @endif

                {{-- Grand Total & Estimasi Anggaran Card --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                    {{-- Estimasi Anggaran Panel --}}
                    <div class="bg-gradient-to-br from-blue-600 to-cyan-500 p-8 rounded-[2rem] text-white shadow-xl shadow-blue-100/50 relative overflow-hidden group">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-md">
                                    <i class="fas fa-wallet text-white text-sm"></i>
                                </div>
                                <span class="text-xs font-black uppercase tracking-widest opacity-80 italic">Estimasi Anggaran</span>
                            </div>
                            <div class="text-3xl font-black mb-2 tracking-tighter">
                                {{ formatRupiah($grand_total) }}
                            </div>
                            <p class="text-[10px] font-bold opacity-60">Total Rincian Anggaran Biaya (RAB)</p>
                        </div>
                    </div>

                    {{-- Simple summary info block --}}
                    <div class="flex justify-between items-center bg-white p-8 rounded-[2rem] border border-slate-200 shadow-sm ml-auto w-full">
                        <span class="text-sm font-black text-slate-500 uppercase tracking-wider">Grand Total Anggaran:</span>
                        <span class="text-3xl font-black text-blue-600 tracking-tight">{{ formatRupiah($grand_total) }}</span>
                    </div>
                </div>

                {{-- Comment field for RAB --}}
                <div class="revision-comment-field hidden mt-6 p-5 bg-amber-50/85 border border-amber-200 rounded-2xl animate-slide-up shadow-inner">
                    <label class="block text-[10px] font-black text-amber-800 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                        <i class="fas fa-comment-dots text-amber-600"></i> Catatan Revisi: Rincian Anggaran Biaya (RAB)
                    </label>
                    <textarea name="field_comments[rabs][uraian]" form="form-review" rows="2" placeholder="Tulis masukan/revisi khusus untuk bagian Rincian Anggaran Biaya (RAB) di sini..."
                        class="w-full px-4 py-3 bg-white border border-amber-200 rounded-xl focus:border-amber-500 focus:ring-4 focus:ring-amber-50/50 outline-none transition-all text-xs font-semibold leading-relaxed text-slate-700"></textarea>
                </div>
            </div>

            {{-- Mata Anggaran Section - Finalized Info --}}
            @if(!empty($kegiatan_data['kode_mak']) && $kegiatan_data['kode_mak'] !== '-')
                <div class="p-8 bg-blue-50/50 rounded-3xl border border-blue-100 flex flex-col md:flex-row items-center justify-between gap-6 animate-reveal">
                    <div class="flex items-center gap-4 text-center md:text-left">
                        <div class="w-16 h-16 rounded-2xl bg-white border border-blue-100 flex items-center justify-center shadow-sm">
                            <i class="fas fa-barcode text-2xl text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-1">Kode Mata Anggaran Kegiatan (MAK)</h4>
                            <p class="text-blue-600 font-mono text-2xl font-black tracking-widest">
                                {{ $kegiatan_data['kode_mak'] }}
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col items-center md:items-end">
                        <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Status Anggaran</span>
                        <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-200">
                            DIALOKASIKAN
                        </span>
                    </div>
                </div>
            @endif

            {{-- Catatan Verifikator Textarea (Only if waiting for action) --}}
            @if($isMenunggu)
                <div class="bg-white rounded-[1.5rem] border border-slate-200 shadow-sm p-8">
                    <h3 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-comment-dots text-blue-500"></i> Catatan Verifikator
                    </h3>
                    <textarea id="general_notes" name="catatan_revisi" form="form-review" rows="4" placeholder="Tulis catatan, masukan, atau rekomendasi umum di sini (opsional)..." 
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 outline-none transition-all font-medium text-sm leading-relaxed"></textarea>
                </div>
            @endif

            {{-- Hidden review form --}}
            <form id="form-review" action="{{ route('verifikator.telaah.store', $id) }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="action" id="action-field" value="">
                <input type="hidden" name="kode_mak" id="kode_mak_hidden" value="">
                <input type="hidden" name="alasan_penolakan" id="alasan-penolakan-field" value="">
                <input type="hidden" name="umpan_balik_verifikator" id="umpan-balik-field" value="">
                <input type="hidden" name="dana_disetujui" value="{{ $grand_total }}">
            </form>

            {{-- Bottom Action Row matching picture 2 exactly --}}
            <div class="flex flex-col sm:flex-row justify-between items-center pt-8 border-t border-slate-100 gap-4">
                <div>
                    <button type="button" onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#187CFC] hover:bg-blue-700 text-white rounded-2xl transition font-black text-sm shadow-lg shadow-blue-200 active:scale-95">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                </div>
                
                @if($isMenunggu)
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-end">
                        <button type="button" onclick="handleReject()" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#FF3B30] hover:bg-rose-700 text-white rounded-2xl transition font-black text-sm shadow-lg shadow-rose-200 active:scale-95">
                            <i class="fas fa-times-circle"></i> Ditolak
                        </button>
                        
                        <button type="button" onclick="handleRevise()" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#FF9500] hover:bg-amber-600 text-white rounded-2xl transition font-black text-sm shadow-lg shadow-amber-200 active:scale-95">
                            <i class="fas fa-pencil-alt"></i> Revisi
                        </button>
                        
                        <button type="button" onclick="handleApprove()" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#187CFC] hover:bg-blue-700 text-white rounded-2xl transition font-black text-sm shadow-lg shadow-blue-200 active:scale-95">
                            <span>Lanjut</span> <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                @else
                    <div class="text-xs text-slate-400 font-bold uppercase tracking-wider italic">
                        Telaah Selesai (Status: {{ $status }})
                    </div>
                @endif
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
    let isRevisionMode = false;

    function handleApprove() {
        const generalNotes = document.getElementById('general_notes')?.value || '';

        Swal.fire({
            title: 'Setujui Usulan?',
            text: 'Masukkan Kode Mata Anggaran Kegiatan (MAK) untuk melanjutkan persetujuan.',
            input: 'text',
            inputPlaceholder: 'Contoh: 521111...',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Setujui & Lanjutkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#10b981',
            borderRadius: '24px',
            inputValidator: (value) => {
                if (!value) {
                    return 'Kode MAK wajib diisi untuk menyetujui usulan!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('action-field').value = 'approve';
                document.getElementById('kode_mak_hidden').value = result.value;
                document.getElementById('umpan-balik-field').value = generalNotes;

                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('form-review').submit();
            }
        });
    }

    function handleRevise() {
        const commentFields = document.querySelectorAll('.revision-comment-field');
        const generalNotes = document.getElementById('general_notes')?.value || '';

        if (!isRevisionMode) {
            // Activate revision mode
            isRevisionMode = true;
            commentFields.forEach(field => {
                field.classList.remove('hidden');
            });

            // Smooth scroll to the first comment field
            const firstField = document.querySelector('.revision-comment-field');
            if (firstField) {
                firstField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            // Customize the Revision Button styling to look active/pulsing
            const reviseBtn = document.querySelector('button[onclick="handleRevise()"]');
            if (reviseBtn) {
                reviseBtn.className = "flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-[#FF9500] hover:bg-amber-600 text-white rounded-2xl transition font-black text-sm shadow-lg shadow-amber-300 active:scale-95 animate-pulse";
                reviseBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Revisi';
            }

            Swal.fire({
                title: 'Mode Revisi Aktif!',
                text: 'Silakan isi kolom komentar revisi pada setiap seksi dokumen yang ingin diperbaiki, lalu klik tombol "Kirim Revisi" lagi untuk mengirim.',
                icon: 'info',
                confirmButtonColor: '#ff9500',
                borderRadius: '24px'
            });
            return;
        }

        // We are in revision mode and clicked "Kirim Revisi" again.
        // Let's check if they filled at least one revision box OR the Catatan Verifikator box.
        let hasComment = false;
        document.querySelectorAll('.revision-comment-field textarea').forEach(textarea => {
            if (textarea.value.trim() !== '') {
                hasComment = true;
            }
        });
        if (generalNotes.trim() !== '') {
            hasComment = true;
        }

        if (!hasComment) {
            Swal.fire({
                title: 'Komentar Revisi Kosong!',
                text: 'Harap isi setidaknya satu kolom masukan/catatan revisi sebelum mengirim.',
                icon: 'warning',
                confirmButtonColor: '#ff9500',
                borderRadius: '24px'
            });
            return;
        }

        // Show confirmation popup
        Swal.fire({
            title: 'Kirim Catatan Revisi?',
            text: 'Usulan akan dikembalikan ke Admin/Pengusul dengan semua detail catatan revisi Anda.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Revisi',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#FF9500',
            borderRadius: '24px'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('action-field').value = 'revise';
                // Handled automatically via form="form-review" attribute on the textareas

                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('form-review').submit();
            }
        });
    }

    function handleReject() {
        Swal.fire({
            title: 'Tolak Usulan?',
            text: 'Harap berikan alasan penolakan secara jelas untuk pengusul.',
            input: 'textarea',
            inputPlaceholder: 'Tulis alasan penolakan di sini...',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tolak Usulan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#FF3B30',
            borderRadius: '24px',
            inputValidator: (value) => {
                if (!value) {
                    return 'Alasan penolakan wajib diisi!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('action-field').value = 'reject';
                document.getElementById('alasan-penolakan-field').value = result.value;

                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('form-review').submit();
            }
        });
    }
</script>
@endpush
