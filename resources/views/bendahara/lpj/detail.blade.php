@extends('layouts.app')

@section('title', 'Validasi LPJ')

@section('content')
@php
    if (!function_exists('formatRupiah')) {
        function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
    }
    if (!function_exists('fmtDateIndo')) {
        function fmtDateIndo($date) {
            if (!$date) return '-';
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $d = strtotime($date);
            return date('d', $d) . ' ' . $months[(int)date('m', $d)] . ' ' . date('Y', $d);
        }
    }
    $isEditable = false; 
    $statusColor = match(strtolower(str_replace(' ', '_', $status))) {
        'selesai', 'disetujui', 'setuju' => 'emerald',
        'revisi' => 'amber',
        'menunggu', 'review', 'siap_submit', 'menunggu_verifikasi' => 'blue',
        'ditolak' => 'rose',
        'menunggu_upload', 'telah_direvisi' => 'indigo',
        default => 'slate'
    };
@endphp

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full animate-fade-in">

    {{-- Status Alert Notifications --}}
    @if(strtolower($status) === 'revisi')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-6 mb-8 rounded-r-3xl shadow-sm animate-slide-up">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-600 border border-amber-200">
                    <i class="fas fa-history text-xl"></i>
                </div>
                <div>
                    <h3 class="text-amber-800 font-black text-xs uppercase tracking-widest mb-1">Menunggu Hasil Revisi</h3>
                    <p class="text-amber-700 text-sm leading-relaxed font-medium">LPJ ini sedang dalam proses perbaikan oleh Admin berdasarkan catatan Anda.</p>
                </div>
            </div>
        </div>
    @elseif(strtolower($status) === 'telah direvisi')
        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-6 mb-8 rounded-r-3xl shadow-sm animate-slide-up">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 flex items-center justify-center flex-shrink-0 text-indigo-600 border border-indigo-200">
                    <i class="fas fa-check-double text-xl"></i>
                </div>
                <div>
                    <h3 class="text-indigo-800 font-black text-xs uppercase tracking-widest mb-1">LPJ Telah Direvisi</h3>
                    <p class="text-indigo-700 text-sm leading-relaxed font-medium">Laporan telah diperbarui. Silakan tinjau kembali rincian bukti sebelum memberikan keputusan final.</p>
                </div>
            </div>
        </div>
    @endif

    <section class="bg-white rounded-xl lg:rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden mb-10">
        
        <!-- Premium Header -->
        <div class="px-8 md:px-12 py-10 border-b border-slate-50 bg-gradient-to-br from-white via-white to-slate-50/50 relative overflow-hidden">
            <div class="absolute right-0 top-0 w-64 h-64 bg-blue-50/30 rounded-full -mr-32 -mt-32 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 rounded-xl bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-[10px] font-black uppercase tracking-widest border border-{{ $statusColor }}-200 shadow-sm">
                            {{ $status }}
                        </span>
                        <div class="h-4 w-px bg-slate-200 mx-1"></div>
                        <span class="text-slate-400 text-[10px] font-black uppercase tracking-widest">KODE LPJ: #{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tighter leading-tight">Validasi Pertanggungjawaban</h2>
                    <p class="text-sm text-slate-500 mt-2 font-medium flex items-center gap-2">
                        <i class="fas fa-calendar-check text-blue-500"></i>
                        Kegiatan: <span class="text-blue-600 font-black tracking-tight underline decoration-blue-200 underline-offset-4">{{ $kegiatan_data['nama_kegiatan'] }}</span>
                    </p>
                </div>
                
                @php
                    $backUrl = ($from === 'dashboard') ? route('bendahara.dashboard') : route('bendahara.lpj.index');
                    $backText = ($from === 'dashboard') ? 'Kembali ke Dashboard' : 'Kembali ke Antrian';
                @endphp
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ $backUrl }}" class="flex-1 md:flex-none inline-flex items-center justify-center gap-3 px-8 py-4 bg-white hover:bg-slate-50 text-slate-600 rounded-2xl transition-all font-black text-[10px] uppercase tracking-widest border border-slate-200 shadow-xl shadow-slate-100/50 active:scale-95">
                        <i class="fas fa-arrow-left"></i> {{ $backText }}
                    </a>
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
                    elseif (in_array($s, ['menunggu', 'review', 'revisi', 'telah_direvisi', 'menunggu_verifikasi'])) $progressWidth = '50%';
                    elseif (in_array($s, ['selesai', 'disetujui', 'setuju'])) $progressWidth = '100%';
                @endphp
                <div class="absolute top-1/2 left-0 h-1.5 bg-{{ $statusColor }}-500 -translate-y-1/2 z-0 transition-all duration-1000 rounded-full" style="width: {{ $progressWidth }}"></div>
                
                @foreach(['Penyusunan', 'Verifikasi', 'Selesai'] as $index => $step)
                    @php
                        $stepActive = false;
                        $stepDone = false;
                        if ($index == 0) {
                            $stepDone = in_array($s, ['menunggu', 'review', 'revisi', 'telah_direvisi', 'selesai', 'disetujui', 'setuju', 'menunggu_verifikasi']);
                            $stepActive = in_array($s, ['menunggu_upload', 'siap_submit', 'draft']);
                        } elseif ($index == 1) {
                            $stepDone = in_array($s, ['selesai', 'disetujui', 'setuju']);
                            $stepActive = in_array($s, ['menunggu', 'review', 'revisi', 'telah_direvisi', 'menunggu_verifikasi']);
                        } elseif ($index == 2) {
                            $stepDone = false;
                            $stepActive = in_array($s, ['selesai', 'disetujui', 'setuju']);
                        }
                    @endphp
                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-12 h-12 rounded-2xl {{ $stepDone ? 'bg-'.$statusColor.'-500 text-white shadow-lg shadow-'.$statusColor.'-100' : ($stepActive ? 'bg-white border-4 border-'.$statusColor.'-500 text-'.$statusColor.'-500 shadow-lg shadow-'.$statusColor.'-50' : 'bg-white border-4 border-slate-200 text-slate-300') }} flex items-center justify-center transition-all duration-500">
                            @if($stepDone) <i class="fas fa-check"></i> @else <span class="text-sm font-black">{{ $index + 1 }}</span> @endif
                        </div>
                        <span class="absolute -bottom-8 text-[10px] font-black uppercase tracking-widest whitespace-nowrap {{ $stepDone || $stepActive ? 'text-slate-800' : 'text-slate-400' }}">{{ $step }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-8 md:p-12">
            <!-- Basic Information Summary -->
            <div class="mb-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pelaksana</label>
                    <div class="text-sm font-bold text-slate-700 leading-tight">{{ $kegiatan_data['nama_pelaksana'] }}</div>
                    <div class="text-[10px] text-slate-400 mt-1 font-medium">{{ $kegiatan_data['nama_pengusul'] }}</div>
                </div>
                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Penanggung Jawab</label>
                    <div class="text-sm font-bold text-slate-700 leading-tight">{{ $kegiatan_data['nama_penanggung_jawab'] }}</div>
                    <div class="text-[10px] text-slate-400 mt-1 font-medium">NIP. {{ $kegiatan_data['nip_penanggung_jawab'] }}</div>
                </div>
                <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100">
                    <label class="block text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2">Kode MAK Aktif</label>
                    <div class="text-sm font-black text-blue-600 tracking-tight font-mono">{{ $kode_mak ?? '-' }}</div>
                </div>
                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Waktu Pelaksanaan</label>
                    <div class="text-sm font-bold text-slate-700">
                        {{ fmtDateIndo($kegiatan_data['tanggal_mulai']) }} - {{ fmtDateIndo($kegiatan_data['tanggal_selesai']) }}
                    </div>
                </div>
            </div>

            <form id="form-lpj-verify" action="#" method="POST" class="space-y-16">
                @csrf
                @php $grand_total_anggaran = 0; $grand_total_realisasi = 0; @endphp
                @foreach($rab_items as $kategori => $items)
                    @php $subtotal_anggaran = 0; $subtotal_realisasi = 0; @endphp
                    
                    <div class="relative">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-blue-600 border border-slate-200 shadow-sm relative z-10">
                                <i class="fas fa-folder-open text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest leading-none">{{ $kategori }}</h3>
                                <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">Kategori Belanja Operasional</p>
                            </div>
                            <div class="flex-1 h-px bg-slate-100 ml-4"></div>
                        </div>
                        
                        <div class="overflow-hidden border border-slate-100 rounded-3xl shadow-xl shadow-slate-100/50 bg-white">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-slate-50/80">
                                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">Uraian Transaksi</th>
                                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 w-44">Anggaran</th>
                                        <th class="px-8 py-5 text-right text-[10px] font-black text-blue-500 uppercase tracking-widest border-b border-slate-100 w-52">Realisasi</th>
                                        <th class="px-8 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 w-64">Validasi / Feedback</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($items as $index => $item)
                                        @php 
                                            $anggaran = $item['total'] ?? ($item['vol1'] * ($item['vol2'] ?? 1) * $item['harga']);
                                            $realisasi = $item['realisasi'] ?? 0;
                                            $subtotal_anggaran += $anggaran;
                                            $subtotal_realisasi += $realisasi;
                                        @endphp
                                        <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                                            <td class="px-8 py-6">
                                                <div class="text-sm font-black text-slate-800 group-hover:text-blue-600 transition-colors">{{ $item['uraian'] }}</div>
                                                <div class="text-[10px] text-slate-400 font-bold mt-1.5 flex items-center gap-2">
                                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 border border-slate-200">{{ $item['rincian'] }}</span>
                                                    <span class="opacity-30">&bull;</span>
                                                    <span>{{ $item['vol1'] }} {{ $item['sat1'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <span class="text-sm font-black text-slate-500 tracking-tighter">{{ number_format($anggaran, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <span class="text-sm font-black text-blue-600 tracking-tighter">{{ number_format($realisasi, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="px-8 py-6 text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <button type="button" class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-600 border border-slate-100 hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center">
                                                        <i class="fas fa-file-invoice text-xs"></i>
                                                    </button>
                                                    
                                                    <button type="button" onclick="toggleFeedback('{{ $item['id'] }}')" class="w-12 h-12 rounded-2xl {{ !empty($item['catatan_item']) ? 'bg-amber-100 text-amber-600 border-amber-200' : 'bg-slate-50 text-slate-400 border-slate-100' }} hover:bg-amber-500 hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center relative">
                                                        <i class="fas fa-comment-dots text-xs"></i>
                                                        @if(!empty($item['catatan_item']))
                                                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-rose-500 rounded-full border-2 border-white animate-bounce"></span>
                                                        @endif
                                                    </button>
                                                </div>
                                                
                                                <div id="feedback-{{ $item['id'] }}" class="hidden mt-4 p-5 bg-slate-50 rounded-2xl border border-slate-200 shadow-inner animate-fade-in text-left">
                                                    <div class="flex items-center gap-2 mb-3">
                                                        <i class="fas fa-pen-nib text-[10px] text-blue-600"></i>
                                                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Catatan Perbaikan Bendahara</label>
                                                    </div>
                                                    @php $isActionable = in_array(strtolower($status), ['menunggu verifikasi', 'telah direvisi', 'menunggu_verifikasi']); @endphp
                                                    <textarea name="item_feedback[{{ $item['id'] }}]" 
                                                        class="w-full p-4 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all placeholder:text-slate-300 shadow-sm {{ !$isActionable ? 'opacity-70 cursor-not-allowed' : '' }}" 
                                                        placeholder="Tuliskan alasan jika perlu perbaikan pada item ini..."
                                                        rows="3" {{ !$isActionable ? 'readonly' : '' }}>{{ $item['catatan_item'] }}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @php $grand_total_anggaran += $subtotal_anggaran; $grand_total_realisasi += $subtotal_realisasi; @endphp
                @endforeach

                <!-- Summary Dashboard -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="lg:col-span-2 bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl shadow-slate-300">
                        <div class="absolute right-0 bottom-0 w-64 h-64 bg-blue-500/20 rounded-full -mr-32 -mb-32 blur-3xl"></div>
                        <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-10">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3 italic">Anggaran Disetujui</span>
                                <div class="text-4xl font-black tracking-tighter">{{ formatRupiah($grand_total_anggaran) }}</div>
                                <div class="mt-6 flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dana Terverifikasi</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3 italic">Total Realisasi Dilaporkan</span>
                                <div id="grand-total-realisasi" class="text-4xl font-black tracking-tighter text-blue-400">{{ formatRupiah($grand_total_realisasi) }}</div>
                                @php $diff = $grand_total_realisasi - $grand_total_anggaran; @endphp
                                <div class="mt-6 text-[10px] font-black uppercase tracking-widest">
                                    @if(abs($diff) < 1)
                                        <span class="text-emerald-500"><i class="fas fa-check-circle mr-1"></i> Balance: Sesuai Anggaran</span>
                                    @elseif($diff > 0)
                                        <span class="text-rose-500"><i class="fas fa-exclamation-triangle mr-1"></i> Over: {{ formatRupiah($diff) }}</span>
                                    @else
                                        <span class="text-blue-400"><i class="fas fa-info-circle mr-1"></i> Under: {{ formatRupiah(abs($diff)) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-[2.5rem] border border-slate-100 p-10 shadow-xl shadow-slate-100 flex flex-col justify-center items-center text-center relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-slate-50 rounded-full blur-2xl"></div>
                        <div class="w-20 h-20 rounded-full border-8 border-slate-50 flex items-center justify-center mb-6 shadow-inner">
                            <i class="fas fa-shield-alt text-slate-300 text-2xl"></i>
                        </div>
                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-2">Validitas Data</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase leading-relaxed max-w-[180px]">Sistem mendeteksi tingkat akurasi bukti sebesar 100%.</p>
                    </div>
                </div>

                @if(in_array(strtolower($status), ['menunggu verifikasi', 'telah direvisi', 'menunggu_verifikasi']))
                    <div class="mt-12 pt-12 border-t border-slate-50">
                        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden p-8 lg:p-12 relative">
                            <div class="absolute top-0 right-0 w-48 h-48 bg-slate-50 rounded-full -mr-24 -mt-24 z-0"></div>
                            
                            <div class="relative z-10">
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-10 flex items-center gap-3">
                                    <i class="fas fa-tasks text-blue-600"></i> Panel Verifikasi Bendahara
                                </h3>

                                <div class="space-y-8">
                                    <div class="space-y-3">
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Catatan Penutup / Instruksi Global</label>
                                        <textarea name="notes" rows="4" placeholder="Tuliskan feedback akhir untuk seluruh laporan ini..." 
                                            class="w-full px-6 py-5 bg-slate-50 border border-slate-200 rounded-3xl focus:border-blue-500 focus:ring-4 focus:ring-blue-50/50 outline-none transition-all font-medium text-xs leading-relaxed"></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                                        <button type="button" onclick="handleAction('approve')" class="w-full py-6 bg-emerald-600 text-white rounded-[1.8rem] font-black text-[11px] uppercase tracking-[0.2em] shadow-2xl shadow-emerald-200 hover:bg-emerald-700 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 group">
                                            <i class="fas fa-check-circle group-hover:scale-110 transition-transform"></i>
                                            <span>Setujui LPJ Lunas</span>
                                        </button>
                                        
                                        <button type="button" onclick="handleAction('revise')" class="w-full py-6 bg-amber-500 text-white rounded-[1.8rem] font-black text-[11px] uppercase tracking-[0.2em] shadow-2xl shadow-amber-200 hover:bg-amber-600 hover:-translate-y-1 transition-all flex items-center justify-center gap-3 group">
                                            <i class="fas fa-edit group-hover:rotate-12 transition-transform"></i>
                                            <span>Kirim Permintaan Revisi</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mt-12 pt-12 border-t border-slate-50">
                        <div class="bg-slate-50 rounded-[2.5rem] p-12 text-center border-2 border-dashed border-slate-200 relative overflow-hidden">
                            <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-white rounded-full blur-3xl opacity-50"></div>
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300 shadow-xl shadow-slate-100">
                                <i class="fas fa-lock text-2xl"></i>
                            </div>
                            <h4 class="text-base font-black text-slate-400 uppercase tracking-widest mb-2">Halaman Terkunci (Read-Only)</h4>
                            <p class="text-[11px] font-bold text-slate-400 uppercase leading-relaxed max-w-md mx-auto">
                                @if(strtolower($status) === 'revisi')
                                    Sedang diperbaiki oleh Admin. Pantau status hingga berubah menjadi <span class="text-blue-500 font-black">"Telah Direvisi"</span>.
                                @else
                                    Laporan ini telah selesai divalidasi. Status saat ini: <span class="text-emerald-500 font-black">{{ strtoupper($status) }}</span>.
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            </form>
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
    function toggleFeedback(id) {
        const el = document.getElementById(`feedback-${id}`);
        if (el) {
            el.classList.toggle('hidden');
        }
    }

    function handleAction(type) {
        const config = {
            approve: { title: 'Setujui LPJ?', text: 'Seluruh rincian realisasi akan dianggap lunas dan status menjadi Selesai.', icon: 'success', color: '#10b981', label: 'Ya, Setujui Lunas' },
            revise:  { title: 'Kirim Revisi?', text: 'Admin akan menerima notifikasi untuk memperbaiki rincian sesuai catatan Anda.', icon: 'warning', color: '#f59e0b', label: 'Ya, Minta Revisi' }
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
            borderRadius: '32px',
            customClass: {
                popup: 'rounded-[2rem] font-poppins',
                confirmButton: 'rounded-2xl px-8 py-3 font-black text-[10px] uppercase tracking-widest',
                cancelButton: 'rounded-2xl px-8 py-3 font-black text-[10px] uppercase tracking-widest'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('form-lpj-verify');
                form.action = "{{ route('bendahara.lpj.proses', $id) }}";
                
                // Add a hidden input for action
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = type;
                form.appendChild(actionInput);
                
                form.submit();
            }
        });
    }
</script>
@endpush
