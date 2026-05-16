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

<main class="main-content font-poppins p-4 sm:p-6 lg:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full animate-fade-in">
    
    {{-- Status Header Alert --}}
    @if(strtolower($status) === 'dana diberikan')
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded-r-xl shadow-sm animate-slide-up">
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

    <section class="bg-white p-4 sm:p-6 lg:p-10 rounded-xl lg:rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden mb-6 border border-slate-100">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 pb-6 border-b border-slate-100 gap-4">
            <div class="w-full lg:w-auto">
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-lg bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-[10px] font-black uppercase tracking-wider border border-{{ $statusColor }}-200">
                        {{ $status ?? 'Pending' }}
                    </span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-400 text-xs font-medium">ID USULAN: #USL-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight leading-tight">Detail Usulan & Pencairan</h2>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('bendahara.pencairan.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-white hover:bg-slate-50 text-slate-600 rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest border border-slate-200 shadow-xl shadow-slate-100/50 active:scale-95">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest border border-emerald-500 shadow-xl shadow-emerald-100 active:scale-95">
                    <i class="fas fa-print"></i> Cetak KAK
                </button>
            </div>
        </div>

        {{-- Stepper Progress --}}
        <div class="mb-12 px-4">
            <div class="relative flex justify-between items-center max-w-4xl mx-auto">
                <div class="absolute top-1/2 left-0 w-full h-1.5 bg-slate-100 -translate-y-1/2 z-0 rounded-full"></div>
                @php
                    $s = strtolower($status);
                    $progressWidth = ($s === 'dana diberikan') ? '100%' : (($s === 'menunggu' || $s === 'review') ? '50%' : '66%');
                @endphp
                <div class="absolute top-1/2 left-0 h-1.5 bg-{{ $statusColor }}-500 -translate-y-1/2 z-0 transition-all duration-1000 rounded-full" style="width: {{ $progressWidth }}"></div>
                
                @foreach(['Pengajuan', 'Verifikasi', 'Selesai'] as $index => $step)
                    @php
                        $isCompleted = ($s === 'dana diberikan') || ($index === 0) || ($index === 1 && $s !== 'menunggu' && $s !== 'review');
                        $isActive = ($index === 1 && ($s === 'review' || $s === 'menunggu')) || ($index === 2 && $s === 'dana diberikan');
                    @endphp
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl {{ $isCompleted ? 'bg-'.$statusColor.'-500 text-white shadow-lg shadow-'.$statusColor.'-200' : ($isActive ? 'bg-white border-4 border-'.$statusColor.'-500 text-'.$statusColor.'-500 shadow-lg shadow-'.$statusColor.'-100' : 'bg-white border-4 border-slate-200 text-slate-300') }} flex items-center justify-center transition-all duration-500">
                            @if($isCompleted) <i class="fas fa-check text-sm"></i> @else <span class="text-sm font-black">{{ $index + 1 }}</span> @endif
                        </div>
                        <span class="absolute -bottom-8 text-[10px] font-black uppercase tracking-widest whitespace-nowrap {{ $isCompleted || $isActive ? 'text-slate-800' : 'text-slate-400' }}">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 mt-20">
            
            {{-- Left Column: KAK Data --}}
            <div class="lg:col-span-2 space-y-10">
                
                <!-- Tahapan & Indikator Section -->
                <div class="bg-white p-6 sm:p-10 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-{{ $statusColor }}-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
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
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 group hover:border-{{ $statusColor }}-200 transition-all">
                                <h4 class="text-sm font-black text-slate-800 mb-4 flex items-center gap-2">
                                    <span class="px-3 py-1 bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 rounded-lg text-[10px] uppercase tracking-widest border border-{{ $statusColor }}-200">{{ $nama_bulan[$bulan] ?? 'Bulan ' . $bulan }}</span>
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Tahapan Pelaksanaan</label>
                                        <p class="text-sm text-slate-600 font-bold leading-relaxed">{{ $tahap }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Indikator Keberhasilan</label>
                                        @php $ind = $indikator_keberhasilan[$bulan] ?? null; @endphp
                                        @if($ind)
                                            <p class="text-sm text-slate-600 font-bold leading-relaxed mb-2">{{ is_array($ind) ? $ind['deskripsi'] : $ind }}</p>
                                            @if(is_array($ind) && isset($ind['target_persen']))
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-100 text-blue-600 text-[10px] font-black uppercase tracking-tight">
                                                <i class="fas fa-bullseye"></i> Target: {{ $ind['target_persen'] }}%
                                            </span>
                                            @endif
                                        @else
                                            <p class="text-sm text-slate-400 italic font-medium">Data tidak tersedia</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Kerangka Acuan Kerja (KAK) -->
                <div class="bg-white p-6 sm:p-10 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-{{ $statusColor }}-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-10 flex items-center gap-3">
                        <i class="fas fa-file-alt text-{{ $statusColor }}-500"></i>
                        Informasi Kegiatan
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8 mb-10">
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
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Penanggung Jawab</label>
                            <div class="text-slate-700 font-bold text-lg">
                                {!! displayValue($kegiatan_data['nama_penanggung_jawab'] ?? '') !!}
                                <span class="text-xs font-medium text-slate-400 block mt-0.5">NIP: {!! displayValue($kegiatan_data['nip_penanggung_jawab'] ?? '') !!}</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu Pelaksanaan</label>
                            <div class="text-slate-700 font-bold text-lg">
                                {{ fmtDateIndo($kegiatan_data['tanggal_mulai']) }} - {{ fmtDateIndo($kegiatan_data['tanggal_selesai']) }}
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Kegiatan</label>
                            <div class="text-slate-700 font-bold text-lg">{!! displayValue($kegiatan_data['nama_kegiatan']) !!}</div>
                        </div>
                    </div>

                    <div class="space-y-10">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Gambaran Umum</label>
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-slate-600 leading-relaxed text-sm font-medium shadow-inner">
                                {!! displayValue($kegiatan_data['gambaran_umum']) !!}
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode Pelaksanaan</label>
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-slate-600 leading-relaxed text-sm font-medium shadow-inner">
                                {!! displayValue($kegiatan_data['metode_pelaksanaan']) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IKU Section -->
                <div class="bg-white p-6 sm:p-10 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-{{ $statusColor }}-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <i class="fas fa-bullseye text-{{ $statusColor }}-500"></i>
                        Indikator Kinerja Utama (IKU)
                    </h3>
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($iku_data as $iku)
                            <div class="flex items-start gap-5 p-5 rounded-2xl bg-slate-50 border border-slate-100 group hover:border-{{ $statusColor }}-200 transition-all">
                                <div class="mt-1 w-6 h-6 rounded-xl bg-{{ $statusColor }}-100 flex items-center justify-center flex-shrink-0 text-{{ $statusColor }}-600">
                                    <i class="fas fa-check text-xs"></i>
                                </div>
                                <span class="text-slate-700 text-sm font-bold leading-tight">{{ $iku }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Column: Budget & Action Card --}}
            <div class="space-y-10">
                
                <!-- Budget Summary Card -->
                <div class="bg-slate-900 rounded-[2.5rem] shadow-2xl shadow-slate-300 overflow-hidden relative group">
                    <div class="absolute right-0 top-0 w-40 h-40 bg-white/5 rounded-full -mr-20 -mt-20 blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="p-10 text-white border-b border-white/5">
                        <span class="text-[10px] font-black uppercase tracking-widest opacity-60 italic">Anggaran Disetujui</span>
                        <div class="text-4xl font-black mt-2 tracking-tighter">{{ formatRupiah($anggaran_disetujui) }}</div>
                    </div>
                    <div class="p-10 space-y-6 bg-slate-800/50">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest block mb-1">Dicairkan</span>
                                <span class="text-xl font-black text-white">{{ formatRupiah($jumlah_dicairkan) }}</span>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest block mb-1">Sisa Dana</span>
                                <span class="text-xl font-black text-white">{{ formatRupiah($sisa_dana) }}</span>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/20 flex items-center justify-center text-blue-400 border border-blue-500/20">
                                <i class="fas fa-hand-holding-usd"></i>
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
                                <span class="text-[10px] font-mono font-bold text-blue-200">{{ $kode_mak ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="px-3 py-1 bg-emerald-500/10 rounded-lg text-emerald-400 text-[9px] font-black uppercase tracking-widest border border-emerald-500/20">
                            <i class="fas fa-check-circle"></i> Terverifikasi
                        </div>
                    </div>
                </div>

                <!-- LPJ Status Card -->
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-slate-50 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="fas fa-file-contract text-blue-600"></i> Status Laporan (LPJ)
                    </h3>
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

                @if(!empty($riwayat_pencairan))
                <!-- Riwayat Pencairan -->
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8 flex items-center gap-2">
                        <i class="fas fa-history text-blue-600"></i> Riwayat Pencairan
                    </h3>
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
                @endif

                <!-- Action Card: Pencairan -->
                @if($boleh_cairkan)
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-100 border border-slate-100 overflow-hidden p-8 lg:p-10 relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 z-0"></div>
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-8 flex items-center gap-2">
                            <i class="fas fa-money-check-alt text-blue-600"></i> Panel Pencairan
                        </h3>

                        <form id="formPencairan" action="#" method="POST" class="space-y-8">
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
                                <textarea name="catatan" rows="3" placeholder="Tambahkan instruksi khusus pencairan..." 
                                    class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 outline-none transition-all font-medium text-xs leading-relaxed"></textarea>
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
                @else
                <div class="bg-emerald-50 border border-emerald-100 rounded-[2.5rem] p-10 text-center relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-emerald-100 rounded-full blur-2xl"></div>
                    <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-600 shadow-xl shadow-emerald-100">
                        <i class="fas fa-check-circle text-3xl"></i>
                    </div>
                    <h4 class="text-base font-black text-emerald-800 uppercase tracking-widest mb-2">Anggaran Lunas</h4>
                    <p class="text-[11px] text-emerald-600 font-bold uppercase tracking-tight opacity-70">Seluruh dana telah dicairkan.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Detailed Table for RAB --}}
        <div class="mt-20 pt-16 border-t border-slate-100">
            <h3 class="text-xl font-black text-slate-800 mb-10 flex items-center gap-3">
                <i class="fas fa-table text-{{ $statusColor }}-500"></i>
                Rincian Anggaran Detail
            </h3>
            
            <div class="space-y-12">
                @foreach($rab_data as $kategori => $items)
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-{{ $statusColor }}-500 shadow-lg shadow-{{ $statusColor }}-200"></span>
                            <h4 class="text-sm font-black text-slate-700 uppercase tracking-widest">{{ $kategori }}</h4>
                        </div>
                        <div class="overflow-x-auto rounded-3xl border border-slate-100 shadow-xl shadow-slate-50 bg-white">
                            <table class="w-full">
                                <thead class="bg-slate-50/80">
                                    <tr>
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Uraian / Rincian</th>
                                        <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Volume</th>
                                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Harga Satuan</th>
                                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($items as $item)
                                        @php $total_item = $item['vol1'] * ($item['vol2'] ?? 1) * $item['harga']; @endphp
                                        <tr class="hover:bg-slate-50/30 transition-colors group">
                                            <td class="px-8 py-6">
                                                <div class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $item['uraian'] }}</div>
                                                <div class="text-[10px] text-slate-400 font-bold mt-1.5 px-2 py-0.5 bg-slate-50 rounded-md inline-block border border-slate-100">{{ $item['rincian'] }}</div>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <span class="px-3 py-1.5 bg-slate-100 rounded-xl text-[10px] font-black text-slate-600">
                                                    {{ $item['vol1'] }} {{ $item['sat1'] }}
                                                    @if(isset($item['vol2']) && isset($item['sat2'])) x {{ $item['vol2'] }} {{ $item['sat2'] }} @endif
                                                </span>
                                            </td>
                                            <td class="px-8 py-6 text-right text-xs font-bold text-slate-500">
                                                {{ number_format($item['harga'], 0, ',', '.') }}
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <span class="text-sm font-black text-{{ $statusColor }}-600 tracking-tight">{{ formatRupiah($total_item) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        {{-- Kode MAK Section --}}
        <div class="mt-20 pt-16 border-t border-slate-100">
            <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                <i class="fas fa-fingerprint text-{{ $statusColor }}-500"></i>
                Kode Mata Anggaran Kegiatan (MAK)
            </h3>
            <div class="relative max-w-2xl">
                @if(!empty($kode_mak))
                    <div class="flex items-center gap-6 p-8 bg-{{ $statusColor }}-50 rounded-[2rem] border-2 border-{{ $statusColor }}-200 shadow-sm animate-fade-in group hover:shadow-xl hover:shadow-{{ $statusColor }}-100/50 transition-all duration-500">
                        <div class="w-16 h-16 rounded-[1.5rem] bg-{{ $statusColor }}-100 flex items-center justify-center text-{{ $statusColor }}-600 shadow-inner group-hover:scale-110 transition-transform">
                            <i class="fas fa-key text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <span class="block text-[10px] font-black text-{{ $statusColor }}-600 uppercase tracking-[0.3em] mb-2">KODE ANGGARAN TERVERIFIKASI</span>
                            <span class="text-2xl font-mono font-black text-slate-800 tracking-wider">{{ $kode_mak }}</span>
                        </div>
                        <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-white rounded-xl text-{{ $statusColor }}-600 text-[10px] font-black uppercase tracking-widest border border-{{ $statusColor }}-200 shadow-sm">
                            <i class="fas fa-check-circle"></i> Aktif
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-6 p-8 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                        <div class="w-16 h-16 rounded-[1.5rem] bg-slate-100 flex items-center justify-center text-slate-400 shadow-inner">
                            <i class="fas fa-lock text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">KODE MAK</span>
                            <span class="text-lg font-bold text-slate-400 italic tracking-tight">Belum tersedia untuk usulan ini</span>
                        </div>
                    </div>
                @endif
                <p class="text-[10px] text-slate-400 mt-6 flex items-center gap-2 italic font-medium">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    Kode MAK digunakan sebagai referensi utama dalam setiap transaksi pencairan dana pada sistem keuangan.
                </p>
            </div>
        </div>

    </section>
</main>

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
                        <input type="date" name="tanggalTahapan[]" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold outline-none focus:border-blue-500 transition-all" required>
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
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Instruksi pencairan dana telah berhasil dibuat.',
                        confirmButtonColor: '#10b981',
                        borderRadius: '24px'
                    }).then(() => {
                        window.location.href = "{{ route('bendahara.pencairan.index') }}";
                    });
                }, 1500);
            }
        });
    }
</script>
@endpush
