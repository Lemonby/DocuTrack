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
    @elseif($kegiatan->posisi_id == 4)
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-xl shadow-sm animate-slide-up">
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
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded-r-xl shadow-sm animate-slide-up">
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

    <section class="bg-white p-4 sm:p-6 lg:p-10 rounded-xl lg:rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden mb-6 border border-slate-100">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 pb-6 border-b border-slate-100 gap-4">
            <div class="w-full lg:w-auto">
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-lg bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-[10px] font-black uppercase tracking-wider border border-{{ $statusColor }}-200">
                        {{ $kegiatan->posisi_id == 4 ? 'Menunggu' : ($status ?? 'Pending') }}
                    </span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-400 text-xs font-medium">ID USULAN: #USL-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Detail Usulan Kegiatan</h2>
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
        <div class="mb-12 px-4">
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
                <div class="bg-gradient-to-br from-{{ $statusColor }}-600 to-{{ $statusColor }}-700 p-8 rounded-3xl shadow-xl shadow-{{ $statusColor }}-100 text-white relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-2 bg-white/20 rounded-lg backdrop-blur-md">
                                <i class="fas fa-wallet text-white text-sm"></i>
                            </div>
                            <span class="text-xs font-black uppercase tracking-widest opacity-80 italic">Estimasi Anggaran</span>
                        </div>
                        <div class="text-4xl font-black mb-2 tracking-tighter">
                            @php 
                                $grand_total = 0;
                                foreach($rab_data as $cat => $items) {
                                    foreach($items as $it) {
                                        $grand_total += $it['vol1'] * ($it['vol2'] ?? 1) * $it['harga'];
                                    }
                                }
                            @endphp
                            {{ formatRupiah($grand_total) }}
                        </div>
                        <p class="text-[10px] font-bold opacity-60">Berdasarkan rincian yang diajukan pada sistem DocuTrack.</p>
                        
                        <div class="mt-8 pt-6 border-t border-white/20 flex flex-col gap-3">
                            @foreach($rab_data as $kategori => $items)
                                @php 
                                    $sub = 0;
                                    foreach($items as $i) $sub += $i['vol1'] * ($i['vol2'] ?? 1) * $i['harga'];
                                @endphp
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black uppercase opacity-70">{{ $kategori }}</span>
                                    <span class="text-xs font-black">{{ formatRupiah($sub) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Action Card based on Status -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden p-8">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="fas fa-tasks text-blue-600"></i> Panel Telaah
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
