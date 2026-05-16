@extends('layouts.app')

@section('title', 'Review & Telaah Usulan (PPK)')

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
        'disetujui', 'selesai' => 'emerald',
        'revisi' => 'amber',
        'ditolak' => 'rose',
        'review', 'menunggu' => 'blue',
        default => 'slate'
    };
@endphp

<main class="main-content font-poppins p-4 sm:p-6 lg:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full animate-fade-in">
    
    <section class="bg-white p-4 sm:p-6 lg:p-10 rounded-xl lg:rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden mb-6 border border-slate-100">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 pb-6 border-b border-slate-100 gap-4">
            <div class="w-full lg:w-auto">
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-lg bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-[10px] font-black uppercase tracking-wider border border-{{ $statusColor }}-200">
                        {{ $status ?? 'Pending' }}
                    </span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-400 text-xs font-medium">ID USULAN: #KAK-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight leading-tight uppercase">Telaah Usulan Kegiatan</h2>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] bg-blue-50 px-3 py-1 rounded-md border border-blue-100">
                        MAK: {{ $kegiatan_data['mak_code'] ?? '000.00.0.000.000' }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('ppk.kegiatan.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-white hover:bg-slate-50 text-slate-600 rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest border border-slate-200 shadow-xl shadow-slate-100/50 active:scale-95">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            {{-- Left Column: Content --}}
            <div class="lg:col-span-2 space-y-10">
                
                {{-- 1. Info Kegiatan --}}
                <div class="bg-white p-6 sm:p-10 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-blue-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-10 flex items-center gap-3">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        1. Informasi Kegiatan
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
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
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Kegiatan</label>
                            <div class="text-slate-800 font-black text-xl leading-tight">{!! displayValue($kegiatan_data['nama_kegiatan']) !!}</div>
                        </div>
                    </div>

                    <div class="mt-12 space-y-8">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Gambaran Umum</label>
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-slate-600 leading-relaxed text-sm font-medium shadow-inner">
                                {!! nl2br(displayValue($kegiatan_data['gambaran_umum'])) !!}
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Penerima Manfaat</label>
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-slate-600 leading-relaxed text-sm font-medium shadow-inner">
                                {!! nl2br(displayValue($kegiatan_data['penerima_manfaat'])) !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Strategi Pencapaian --}}
                <div class="bg-white p-6 sm:p-10 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-emerald-500"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-10 flex items-center gap-3">
                        <i class="fas fa-chess-king text-emerald-500"></i>
                        2. Strategi Pencapaian Keluaran
                    </h3>
                    
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Metode Pelaksanaan</label>
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-slate-600 leading-relaxed text-sm font-medium">
                                {!! nl2br(displayValue($kegiatan_data['metode_pelaksanaan'])) !!}
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Tahapan Kegiatan</label>
                            <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 text-slate-600 leading-relaxed text-sm font-medium">
                                {!! nl2br(displayValue($kegiatan_data['tahapan_kegiatan'])) !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. IKU & Indikator --}}
                <div class="bg-white p-6 sm:p-10 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-blue-600"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <i class="fas fa-bullseye text-blue-600"></i>
                        3. Indikator Kinerja (IKU & KAK)
                    </h3>
                    
                    <div class="space-y-10">
                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Indikator Kinerja Utama (IKU)</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($iku_data as $iku)
                                    <span class="px-4 py-2 bg-blue-50 text-blue-700 rounded-xl text-xs font-black border border-blue-100 flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i> {{ $iku }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Pelaksanaan & Keberhasilan per Bulan</label>
                            <div class="space-y-4">
                                @php
                                    $nama_bulan = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                @endphp
                                @foreach($tahapan_pelaksanaan as $bulan => $tahap)
                                    <div class="p-6 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                                        <div class="flex items-center gap-3 mb-4">
                                            <span class="px-3 py-1 bg-slate-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $nama_bulan[$bulan] ?? 'Bulan ' . $bulan }}</span>
                                            <div class="h-px flex-1 bg-slate-200"></div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Tahapan</label>
                                                <p class="text-xs text-slate-700 font-bold leading-relaxed">{{ $tahap }}</p>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Indikator</label>
                                                @php $ind = $indikator_keberhasilan[$bulan] ?? null; @endphp
                                                @if($ind)
                                                    <p class="text-xs text-slate-700 font-bold leading-relaxed mb-2">{{ is_array($ind) ? $ind['deskripsi'] : $ind }}</p>
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-600 text-[10px] font-black">
                                                        Target: {{ $ind['target_persen'] }}%
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. RAB --}}
                <div class="bg-white p-6 sm:p-10 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-2 h-full bg-slate-800"></div>
                    <h3 class="text-xl font-black text-slate-800 mb-10 flex items-center gap-3">
                        <i class="fas fa-calculator text-slate-800"></i>
                        4. Rincian Anggaran Biaya (RAB)
                    </h3>
                    
                    @php $grand_total = 0; @endphp
                    @foreach($rab_data as $kategori => $items)
                        <div class="mb-10 last:mb-0">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                {{ $kategori }}
                            </h4>
                            <div class="overflow-x-auto rounded-[1.5rem] border border-slate-100 shadow-inner bg-slate-50/30">
                                <table class="w-full">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-[9px] font-black text-slate-400 uppercase tracking-widest">Uraian</th>
                                            <th class="px-6 py-4 text-center text-[9px] font-black text-slate-400 uppercase tracking-widest">Volume</th>
                                            <th class="px-6 py-4 text-right text-[9px] font-black text-slate-400 uppercase tracking-widest">Harga</th>
                                            <th class="px-6 py-4 text-right text-[9px] font-black text-slate-400 uppercase tracking-widest">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($items as $item)
                                            @php $subtotal = $item['vol1'] * ($item['vol2'] ?? 1) * $item['harga']; $grand_total += $subtotal; @endphp
                                            <tr class="hover:bg-white transition-colors">
                                                <td class="px-6 py-5">
                                                    <div class="text-xs font-bold text-slate-700">{{ $item['uraian'] }}</div>
                                                    <div class="text-[9px] text-slate-400 font-medium mt-1">{{ $item['rincian'] }}</div>
                                                </td>
                                                <td class="px-6 py-5 text-center">
                                                    <span class="text-[10px] font-black text-slate-600">
                                                        {{ $item['vol1'] }} {{ $item['sat1'] }}
                                                        @if(isset($item['vol2'])) x {{ $item['vol2'] }} {{ $item['sat2'] }} @endif
                                                    </span>
                                                </td>
                                                <td class="px-6 py-5 text-right text-xs font-bold text-slate-500">{{ number_format($item['harga'], 0, ',', '.') }}</td>
                                                <td class="px-6 py-5 text-right text-xs font-black text-blue-600">{{ formatRupiah($subtotal) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-8 flex justify-end">
                        <div class="p-6 bg-slate-900 rounded-[1.5rem] text-white shadow-xl">
                            <span class="text-[9px] font-black uppercase tracking-widest opacity-60">Grand Total Anggaran</span>
                            <div class="text-2xl font-black mt-1">{{ formatRupiah($grand_total) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Actions --}}
            <div class="space-y-10">
                
                {{-- Metadata Card --}}
                <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/5 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <h3 class="text-sm font-black uppercase tracking-widest mb-8 flex items-center gap-2 opacity-60">
                        <i class="fas fa-paperclip"></i> Lampiran & Berkas
                    </h3>

                    <div class="space-y-6 relative z-10">
                        <div class="p-5 bg-white/5 rounded-2xl border border-white/10 hover:bg-white/10 transition-all cursor-pointer">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-red-500/20 flex items-center justify-center text-red-400">
                                    <i class="fas fa-file-pdf text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-[8px] font-black text-white/40 uppercase tracking-widest mb-1">Surat Pengantar</span>
                                    <span class="text-xs font-bold truncate block">{{ $kegiatan_data['surat_pengantar'] ?? 'Belum ada file' }}</span>
                                </div>
                                <i class="fas fa-download text-white/20"></i>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                                <span class="block text-[8px] font-black text-white/40 uppercase tracking-widest mb-2">Tanggal Mulai</span>
                                <span class="text-[10px] font-black">{{ fmtDateIndo($kegiatan_data['tanggal_mulai'] ?? null) }}</span>
                            </div>
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                                <span class="block text-[8px] font-black text-white/40 uppercase tracking-widest mb-2">Tanggal Selesai</span>
                                <span class="text-[10px] font-black">{{ fmtDateIndo($kegiatan_data['tanggal_selesai'] ?? null) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Approval Panel --}}
                <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-100 border border-slate-100 overflow-hidden p-8 lg:p-10 relative">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 z-0"></div>
                    
                    <div class="relative z-10">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-8 flex items-center gap-2">
                            <i class="fas fa-stamp text-blue-600"></i> Panel Persetujuan
                        </h3>

                        @if($status === 'Menunggu' || $status === 'Review')
                            <form action="{{ route('ppk.kegiatan.store', $id) }}" method="POST" class="space-y-6">
                                @csrf
                                <div class="space-y-3">
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan / Komentar PPK</label>
                                    <textarea name="catatan" rows="4" placeholder="Berikan catatan jika diperlukan..." 
                                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 outline-none transition-all font-medium text-xs leading-relaxed shadow-inner"></textarea>
                                </div>

                                <div class="space-y-3 pt-4">
                                    <button type="submit" name="action" value="approve" class="w-full py-4 bg-emerald-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-emerald-100 hover:bg-emerald-700 hover:-translate-y-1 transition-all flex items-center justify-center gap-2 group">
                                        <i class="fas fa-check-circle group-hover:scale-110 transition-transform"></i>
                                        Setujui Usulan
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
        </div>
    </section>
</main>

<style>
    @keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>
@endsection
