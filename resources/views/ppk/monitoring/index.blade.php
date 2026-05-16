@extends('layouts.app')

@section('title', 'Monitoring Progres Kegiatan (PPK)')

@section('content')
<main class="main-content font-poppins p-3 sm:p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="monitoring-section" class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8 border border-slate-100 flex flex-col">
        
        {{-- Header Section --}}
        <div class="p-5 border-b border-slate-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-desktop text-blue-600"></i> Monitoring Realisasi
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-1 italic">Pantau realisasi anggaran secara realtime</p>
            </div>
            
            <!-- Status Filter Tabs -->
            <div class="w-full overflow-x-auto scrollbar-hide mb-5">
                <div class="inline-flex p-2 bg-slate-100/80 rounded-2xl items-center gap-3 min-w-min border border-slate-200/50 shadow-inner overflow-x-auto scrollbar-hide">
                    <button type="button" 
                        class="ppk-filter-tab-btn px-6 py-2.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 flex items-center gap-2.5 whitespace-nowrap active:scale-95 bg-white text-slate-800 shadow-md border border-slate-200" 
                        data-status="Semua">
                        <i class="fas fa-th-large"></i> Semua
                    </button>
                    <button type="button" 
                        class="ppk-filter-tab-btn px-6 py-2.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 flex items-center gap-2.5 whitespace-nowrap active:scale-95 text-slate-400 hover:text-slate-600 hover:bg-white/50" 
                        data-status="In Process">
                        <i class="fas fa-sync-alt"></i> In Process
                    </button>
                    <button type="button" 
                        class="ppk-filter-tab-btn px-6 py-2.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 flex items-center gap-2.5 whitespace-nowrap active:scale-95 text-slate-400 hover:text-slate-600 hover:bg-white/50" 
                        data-status="Menunggu">
                        <i class="fas fa-hourglass-half"></i> Menunggu
                    </button>
                    <button type="button" 
                        class="ppk-filter-tab-btn px-6 py-2.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 flex items-center gap-2.5 whitespace-nowrap active:scale-95 text-slate-400 hover:text-slate-600 hover:bg-white/50" 
                        data-status="Approved">
                        <i class="fas fa-check-circle"></i> Approved
                    </button>
                    <button type="button" 
                        class="ppk-filter-tab-btn px-6 py-2.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 flex items-center gap-2.5 whitespace-nowrap active:scale-95 text-slate-400 hover:text-slate-600 hover:bg-white/50" 
                        data-status="Ditolak">
                        <i class="fas fa-times-circle"></i> Ditolak
                    </button>
                </div>
            </div>

            <div class="relative group">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                <input type="text" id="search-monitoring" placeholder="Cari kegiatan atau pengusul..."
                       class="pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all w-full sm:w-64">
            </div>
        </div>
        
        {{-- Content Section --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Detail Kegiatan</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Alur & Progres</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Status Terakhir</th>
                    </tr>
                </thead>
                <tbody id="monitoring-table-body" class="divide-y divide-slate-100">
                    @forelse($list_proposal as $item)
                        @php
                            $total_langkah = count($tahapan_all) - 1;
                            $posisi_sekarang = array_search($item['tahap_sekarang'], $tahapan_all);
                            if ($posisi_sekarang === false) $posisi_sekarang = 0;

                            $status_lower = strtolower($item['status']);
                            $is_ditolak = $status_lower === 'ditolak' || $status_lower === 'rejected';
                            $lebar_progress = $posisi_sekarang > 0 ? ($posisi_sekarang / $total_langkah) * 100 : 0;
                            
                            $progress_color = $is_ditolak ? 'bg-rose-500' : 'bg-blue-600';
                            
                            $status_badge = match ($status_lower) {
                                'approved', 'disetujui' => 'text-emerald-700 bg-emerald-100 border-emerald-200',
                                'ditolak', 'rejected'  => 'text-rose-700 bg-rose-100 border-rose-200',
                                'in process', 'proses' => 'text-blue-700 bg-blue-100 border-blue-200',
                                default                => 'text-slate-500 bg-slate-100 border-slate-200',
                            };
                        @endphp
                        <tr class="monitoring-row hover:bg-slate-50 transition-colors" data-nama="{{ strtolower($item['nama'] . ' ' . $item['pengusul']) }}">
                            <td class="px-6 py-6 align-top">
                                <div class="text-xs font-black text-slate-800 uppercase tracking-tight mb-1">{{ $item['nama'] }}</div>
                                <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">
                                    <i class="fas fa-user-circle opacity-50"></i> {{ $item['pengusul'] }}
                                </div>
                            </td>
                            
                            <td class="px-6 py-6 align-middle">
                                <div class="relative w-full h-12 flex items-center">
                                    {{-- Background Track --}}
                                    <div class="absolute top-1/2 -translate-y-1/2 left-0 w-full h-1.5 bg-slate-100 rounded-full z-0 shadow-inner"></div> 
                                    
                                    {{-- Active Progress --}}
                                    <div class="absolute top-1/2 -translate-y-1/2 left-0 h-1.5 {{ $progress_color }} rounded-full z-0 transition-all duration-700 ease-out shadow-sm" 
                                         style="width: {{ $lebar_progress }}%;"></div> 
                                    
                                    {{-- Step Dots --}}
                                    @foreach($tahapan_all as $index => $nama_tahap)
                                        @php
                                            $is_completed = $index < $posisi_sekarang;
                                            $is_active = $index == $posisi_sekarang;
                                            
                                            $dot_cls = 'bg-white border-slate-200';
                                            $icon = '';
                                            
                                            if ($is_completed) {
                                                $dot_cls = 'bg-blue-600 border-blue-600 text-white shadow-sm';
                                                $icon = '<i class="fas fa-check text-[8px]"></i>';
                                            } elseif ($is_active) {
                                                if ($is_ditolak) {
                                                    $dot_cls = 'bg-rose-500 border-rose-500 scale-110 text-white shadow-md';
                                                    $icon = '<i class="fas fa-times text-[9px]"></i>';
                                                } else {
                                                    $dot_cls = 'bg-white border-blue-600 scale-110 text-blue-600 shadow-md';
                                                    $icon = '<div class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></div>';
                                                }
                                            }
                                            
                                            $pos_left = $total_langkah > 0 ? ($index / $total_langkah) * 100 : 0;
                                        @endphp
                                        <div class="absolute z-10 flex flex-col items-center" style="left: {{ $pos_left }}%; transform: translateX(-50%);">
                                            <div class="w-4 h-4 rounded-full border {{ $dot_cls }} flex items-center justify-center transition-all bg-white shadow-sm">
                                                {!! $icon !!}
                                            </div>
                                            <div class="absolute -bottom-6 whitespace-nowrap">
                                                <span class="text-[8px] font-black {{ $is_active ? 'text-blue-600' : 'text-slate-400' }} uppercase tracking-tighter">
                                                    {{ $nama_tahap }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            
                            <td class="px-6 py-6 align-top text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="status-badge px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $status_badge }}">
                                        {{ $item['status'] }}
                                    </span>
                                    <div class="text-[9px] text-slate-300 font-black uppercase tracking-widest italic">
                                        {{ $item['tahap_sekarang'] }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="3" class="text-center py-20 text-[10px] font-black text-slate-300 uppercase tracking-widest italic">
                                Belum ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-monitoring');
    const filterTabs = document.querySelectorAll('.ppk-filter-tab-btn');
    const rows = document.querySelectorAll('.monitoring-row');
    const empty = document.getElementById('empty-row');

    let currentStatus = 'semua';
    let currentSearch = '';

    function applyFilters() {
        let visible = 0;
        rows.forEach(row => {
            const status = row.querySelector('.status-badge')?.textContent.trim().toLowerCase() || '';
            const searchContent = row.dataset.nama.toLowerCase();
            
            const matchStatus = currentStatus === 'semua' || status === currentStatus.toLowerCase();
            const matchSearch = !currentSearch || searchContent.includes(currentSearch);

            if (matchStatus && matchSearch) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        if (empty) empty.style.display = visible === 0 ? '' : 'none';
    }

    searchInput?.addEventListener('input', (e) => {
        currentSearch = e.target.value.toLowerCase().trim();
        applyFilters();
    });

    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Reset all tabs
            filterTabs.forEach(t => {
                t.className = "ppk-filter-tab-btn px-6 py-2.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 flex items-center gap-2.5 whitespace-nowrap active:scale-95 text-slate-400 hover:text-slate-600 hover:bg-white/50";
            });

            // Set active state
            const status = tab.dataset.status;
            let activeClass = "ppk-filter-tab-btn px-6 py-2.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all duration-300 flex items-center gap-2.5 whitespace-nowrap active:scale-95 shadow-lg scale-105 ";
            
            if (status === "Semua") activeClass += "bg-white text-slate-800 border border-slate-200";
            else if (status === "In Process") activeClass += "bg-blue-500 text-white shadow-blue-200";
            else if (status === "Menunggu") activeClass += "bg-amber-400 text-slate-900 shadow-amber-100";
            else if (status === "Approved") activeClass += "bg-emerald-500 text-white shadow-emerald-200";
            else if (status === "Ditolak") activeClass += "bg-rose-500 text-white shadow-rose-200";

            tab.className = activeClass;
            currentStatus = status.toLowerCase();
            applyFilters();
        });
    });
});
</script>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
