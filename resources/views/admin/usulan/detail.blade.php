@extends('layouts.app')

@section('title', 'Detail Usulan Kegiatan')

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
        'review', 'menunggu' => 'blue',
        default => 'slate'
    };
@endphp

<main class="main-content font-poppins p-4 sm:p-6 lg:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full animate-fade-in">
    
    {{-- Status Header Alert --}}
    @if(strtolower($status) === 'revisi')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-xl shadow-sm animate-slide-up">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    <i class="fas fa-exclamation-triangle text-amber-500 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-amber-800 font-bold text-sm sm:text-base">Perlu Revisi</h3>
                    <p class="text-amber-700 text-xs sm:text-sm mt-1 leading-relaxed">
                        {{ $catatan_revisi ?? 'Terdapat beberapa bagian yang perlu diperbaiki sebelum pengajuan dapat dilanjutkan.' }}
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('admin.usulan.edit', $id) }}" class="inline-flex items-center gap-2 px-4 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition shadow-md shadow-amber-200">
                            <i class="fas fa-edit"></i> Edit Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif(strtolower($status) === 'disetujui')
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded-r-xl shadow-sm animate-slide-up">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-emerald-800 font-bold text-sm sm:text-base">Usulan Disetujui</h3>
                    <p class="text-emerald-700 text-xs sm:text-sm">Kegiatan ini telah melewati tahap verifikasi dan siap untuk dilaksanakan.</p>
                </div>
            </div>
        </div>
    @endif

    <section class="bg-white p-4 sm:p-6 lg:p-10 rounded-xl lg:rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden mb-6 border border-slate-100">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 pb-6 border-b border-slate-100 gap-4">
            <div class="w-full lg:w-auto">
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-lg bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-[10px] font-black uppercase tracking-wider border border-{{ $statusColor }}-200">
                        {{ $status ?? 'Pending' }}
                    </span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-400 text-xs font-medium">ID USULAN: #USL-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Detail Usulan Kegiatan</h2>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('admin.dashboard') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition font-bold text-sm border border-slate-200">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @if(strtolower($status) === 'disetujui')
                <button class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-{{ $statusColor }}-600 hover:bg-{{ $statusColor }}-700 text-white rounded-xl transition font-bold text-sm shadow-lg shadow-{{ $statusColor }}-200">
                    <i class="fas fa-print"></i> Cetak KAK
                </button>
                @endif
            </div>
        </div>

        {{-- Stepper Progress --}}
        <div class="mb-12 px-4">
            <div class="relative flex justify-between items-center max-w-4xl mx-auto">
                <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-100 -translate-y-1/2 z-0"></div>
                <div class="absolute top-1/2 left-0 {{ strtolower($status) === 'disetujui' ? 'w-full' : (strtolower($status) === 'revisi' ? 'w-1/3' : 'w-2/3') }} h-1 bg-{{ $statusColor }}-500 -translate-y-1/2 z-0 transition-all duration-1000"></div>
                
                @foreach(['Pengajuan', 'Verifikasi', 'Selesai'] as $index => $step)
                    @php
                        $isCompleted = (strtolower($status) === 'disetujui') || 
                                      ($index === 0) || 
                                      ($index === 1 && strtolower($status) !== 'revisi' && strtolower($status) !== 'menunggu');
                        $isActive = ($index === 1 && (strtolower($status) === 'review' || strtolower($status) === 'menunggu')) ||
                                    ($index === 0 && strtolower($status) === 'revisi');
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-16">
            
            {{-- Left Column: KAK Data --}}
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Kerangka Acuan Kerja (KAK) -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-{{ $statusColor }}-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <i class="fas fa-file-alt text-{{ $statusColor }}-500"></i>
                        Informasi Kegiatan
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Pengusul</label>
                            <div class="text-slate-700 font-bold text-lg">{!! displayValue($kegiatan_data['nama_pengusul']) !!}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">NIM / NIP</label>
                            <div class="text-slate-700 font-bold text-lg">{!! displayValue($kegiatan_data['nim_nip'] ?? '') !!}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Jurusan & Prodi</label>
                            <div class="text-slate-700 font-bold text-lg">
                                {!! displayValue($kegiatan_data['jurusan'] ?? '') !!} 
                                <span class="text-sm font-medium text-slate-400 block mt-0.5">{!! displayValue($kegiatan_data['prodi'] ?? '') !!}</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Kegiatan</label>
                            <div class="text-slate-700 font-bold text-lg">{!! displayValue($kegiatan_data['nama_kegiatan']) !!}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Wadir Tujuan</label>
                            <div class="text-slate-700 font-bold text-lg">{!! displayValue($kegiatan_data['wadir_tujuan'] ?? '') !!}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Penerima Manfaat</label>
                            <div class="text-slate-700 font-bold text-lg">{!! displayValue($kegiatan_data['penerima_manfaat'] ?? '') !!}</div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Penanggung Jawab</label>
                            <div class="text-slate-700 font-bold text-lg">
                                {!! displayValue($kegiatan_data['penanggung_jawab'] ?? '') !!}
                                <span class="text-xs font-medium text-slate-400 block mt-0.5">NIP: {!! displayValue($kegiatan_data['nip_pj'] ?? '') !!}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Gambaran Umum</label>
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 text-slate-600 leading-relaxed text-sm font-medium">
                                {!! displayValue($kegiatan_data['gambaran_umum']) !!}
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode Pelaksanaan</label>
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 text-slate-600 leading-relaxed text-sm font-medium">
                                {!! displayValue($kegiatan_data['metode_pelaksanaan']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IKU Section -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-{{ $statusColor }}-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                        <i class="fas fa-bullseye text-{{ $statusColor }}-500"></i>
                        Indikator Kinerja Utama (IKU) & Renstra
                    </h3>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach($iku_data as $iku)
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100 group hover:border-{{ $statusColor }}-200 transition-colors">
                                <div class="mt-1 w-5 h-5 rounded-full bg-{{ $statusColor }}-100 flex items-center justify-center flex-shrink-0 text-{{ $statusColor }}-600">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                                <span class="text-slate-700 text-sm font-bold leading-tight">{{ $iku }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tahapan & Indikator Section -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-{{ $statusColor }}-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                        <i class="fas fa-tasks text-{{ $statusColor }}-500"></i>
                        Pelaksanaan & Keberhasilan
                    </h3>
                    
                    <div class="space-y-6">
                        @php
                            $nama_bulan = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                        @endphp
                        @foreach($tahapan_pelaksanaan as $bulan => $tahap)
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                                <h4 class="text-sm font-black text-slate-800 mb-3 flex items-center gap-2">
                                    <span class="px-2 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 rounded text-[10px] uppercase tracking-widest">{{ $nama_bulan[$bulan] ?? 'Bulan ' . $bulan }}</span>
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tahapan Pelaksanaan</label>
                                        <p class="text-sm text-slate-600 font-medium">{{ $tahap }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Indikator Keberhasilan</label>
                                        @php $ind = $indikator_keberhasilan[$bulan] ?? null; @endphp
                                        @if($ind)
                                            <p class="text-sm text-slate-600 font-medium mb-1">{{ is_array($ind) ? $ind['deskripsi'] : $ind }}</p>
                                            @if(is_array($ind) && isset($ind['target_persen']))
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-blue-50 border border-blue-100 text-blue-600 text-[10px] font-bold">
                                                <i class="fas fa-bullseye"></i> Target: {{ $ind['target_persen'] }}%
                                            </span>
                                            @endif
                                        @else
                                            <p class="text-sm text-slate-600 font-medium">-</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Column: RAB Summary & Actions --}}
            <div class="space-y-8">
                
                <!-- RAB Total Card -->
                    </div>
                </div>

                @if(strtolower($status) === 'disetujui')
                <!-- Financial Tracking Card -->
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-blue-600"></i> Financial Tracking
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Payout Status -->
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 relative">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Progress Pencairan</span>
                                <span class="px-2 py-0.5 rounded-lg bg-blue-600 text-white text-[8px] font-black uppercase tracking-widest shadow-sm">{{ $kegiatan_data['payout_status'] }}</span>
                            </div>
                            <div class="flex items-end justify-between">
                                <span class="text-lg font-black text-slate-800 tracking-tighter">{{ formatRupiah($kegiatan_data['total_cair']) }}</span>
                                <span class="text-[9px] font-black text-slate-400 italic">Total Terbayar</span>
                            </div>
                            <!-- Mini Progress Bar -->
                            <div class="mt-4 w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                @php
                                    $payoutWidth = match(strtolower($kegiatan_data['payout_status'])) {
                                        'lunas (100%)' => '100%',
                                        'termin 1 (50%)' => '50%',
                                        default => '0%'
                                    };
                                @endphp
                                <div class="h-full bg-blue-500 transition-all duration-1000 shadow-[0_0_8px_rgba(59,130,246,0.5)]" style="width: {{ $payoutWidth }}"></div>
                            </div>
                        </div>

                        <!-- LPJ Dependency Status -->
                        <div class="p-4 bg-{{ strtolower($kegiatan_data['lpj_status']) === 'disetujui' ? 'emerald' : (strtolower($kegiatan_data['lpj_status']) === 'belum ada' ? 'slate' : 'amber') }}-50 rounded-2xl border border-{{ strtolower($kegiatan_data['lpj_status']) === 'disetujui' ? 'emerald' : (strtolower($kegiatan_data['lpj_status']) === 'belum ada' ? 'slate' : 'amber') }}-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-{{ strtolower($kegiatan_data['lpj_status']) === 'disetujui' ? 'emerald' : (strtolower($kegiatan_data['lpj_status']) === 'belum ada' ? 'slate' : 'amber') }}-100 flex items-center justify-center text-{{ strtolower($kegiatan_data['lpj_status']) === 'disetujui' ? 'emerald' : (strtolower($kegiatan_data['lpj_status']) === 'belum ada' ? 'slate' : 'amber') }}-600">
                                    <i class="fas fa-file-contract text-xs"></i>
                                </div>
                                <div>
                                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest">Status LPJ</span>
                                    <span class="text-xs font-black text-slate-800 uppercase">{{ $kegiatan_data['lpj_status'] }}</span>
                                </div>
                            </div>
                        </div>

                        @if($kegiatan_data['payout_status'] === 'Termin 1 (50%)' && $kegiatan_data['lpj_status'] !== 'Disetujui')
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl">
                            <div class="flex gap-3">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                                <p class="text-[10px] text-blue-700 font-bold leading-relaxed">
                                    Termin 2 (Sisa 50%) akan dicairkan Bendahara segera setelah LPJ Anda <span class="uppercase">Disetujui</span>.
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Action Card based on Status -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Aksi Tersedia</h4>
                    <div class="space-y-3">
                        @if(strtolower($status) === 'revisi')
                            <a href="{{ route('admin.usulan.edit', $id) }}" class="w-full flex items-center justify-center gap-3 bg-amber-500 hover:bg-amber-600 text-white py-3.5 rounded-xl text-sm font-black transition shadow-lg shadow-amber-100">
                                <i class="fas fa-edit"></i> REVISI SEKARANG
                            </a>
                        @elseif(strtolower($status) === 'menunggu' || strtolower($status) === 'review')
                            <div class="p-4 bg-white rounded-xl border border-slate-200 mb-4">
                                <p class="text-[10px] text-slate-500 font-bold text-center leading-relaxed italic">
                                    "Status saat ini: Menunggu Verifikasi."
                                </p>
                            </div>
                        @elseif(strtolower($status) === 'disetujui')
                            <button class="w-full flex items-center justify-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl text-sm font-black transition shadow-lg shadow-emerald-100">
                                <i class="fas fa-download"></i> UNDUH BERKAS KAK
                            </button>
                            <button class="w-full flex items-center justify-center gap-3 bg-white border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-50 py-3.5 rounded-xl text-sm font-black transition">
                                <i class="fas fa-eye"></i> LIHAT TRACKING
                            </button>
                        @elseif(strtolower($status) === 'ditolak')
                            <button disabled class="w-full flex items-center justify-center gap-3 bg-rose-100 text-rose-500 py-3.5 rounded-xl text-sm font-black cursor-not-allowed">
                                <i class="fas fa-times-circle"></i> USULAN DITOLAK
                            </button>
                        @endif
                        
                        <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center justify-center gap-3 bg-white text-slate-600 border border-slate-300 py-3 rounded-xl text-sm font-bold hover:bg-slate-100 transition">
                            KEMBALI KE BERANDA
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Detailed Table for RAB - shown below for better readability --}}
        <div class="mt-12 pt-12 border-t border-slate-100">
            <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                <i class="fas fa-table text-{{ $statusColor }}-500"></i>
                Rincian Anggaran Detail
            </h3>
            
            <div class="space-y-10">
                @foreach($rab_data as $kategori => $items)
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-black text-slate-700 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-{{ $statusColor }}-500"></span>
                                {{ $kategori }}
                            </h4>
                        </div>
                        <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
                            <table class="w-full">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Uraian / Rincian</th>
                                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Volume</th>
                                        <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Harga Satuan</th>
                                        <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($items as $item)
                                        @php $total_item = $item['vol1'] * ($item['vol2'] ?? 1) * $item['harga']; @endphp
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-bold text-slate-800">{{ $item['uraian'] }}</div>
                                                <div class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $item['rincian'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-600">
                                                    {{ $item['vol1'] }} {{ $item['sat1'] }}
                                                    @if(isset($item['vol2']) && isset($item['sat2'])) x {{ $item['vol2'] }} {{ $item['sat2'] }} @endif
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right text-xs font-bold text-slate-500">
                                                {{ number_format($item['harga'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <span class="text-sm font-black text-{{ $statusColor }}-600">{{ formatRupiah($total_item) }}</span>
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

        {{-- Kode MAK Section --}}
        <div class="mt-12 pt-12 border-t border-slate-100">
            <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                <i class="fas fa-fingerprint text-{{ $statusColor }}-500"></i>
                Kode Mata Anggaran Kegiatan (MAK)
            </h3>
            <div class="relative">
                @if(!empty($kegiatan_data['kode_mak']))
                    <div class="flex items-center gap-4 p-6 bg-{{ $statusColor }}-50 rounded-2xl border-2 border-{{ $statusColor }}-200 shadow-sm animate-fade-in">
                        <div class="w-12 h-12 rounded-2xl bg-{{ $statusColor }}-100 flex items-center justify-center text-{{ $statusColor }}-600 shadow-inner">
                            <i class="fas fa-key text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <span class="block text-[10px] font-black text-{{ $statusColor }}-600 uppercase tracking-[0.2em] mb-1">KODE MAK AKTIF</span>
                            <span class="text-xl font-mono font-black text-slate-800 tracking-wider">{{ $kegiatan_data['kode_mak'] }}</span>
                        </div>
                        <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-white rounded-xl text-{{ $statusColor }}-600 text-[10px] font-black uppercase tracking-widest border border-{{ $statusColor }}-200">
                            <i class="fas fa-check-circle"></i> Terverifikasi
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-4 p-6 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 shadow-inner">
                            <i class="fas fa-lock text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kode MAK</span>
                            <span class="text-lg font-bold text-slate-400 italic tracking-tight">Belum tersedia untuk usulan ini</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-4 flex items-center gap-2 italic font-medium">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        Kode MAK akan digenerate otomatis oleh sistem Bendahara setelah usulan disetujui secara final.
                    </p>
                @endif
            </div>
        </div>

    </section>
</main>

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

