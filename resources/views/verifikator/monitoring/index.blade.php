@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="monitoring-section" class="bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Progres Proposal</h2>
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="sm:w-64 w-full">
                    <select id="filter-jurusan" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusan_list as $j)
                            <option value="{{ strtolower($j) }}">{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="relative w-full md:w-64">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="search-monitoring" placeholder="Cari Proposal..."
                           class="w-full pl-11 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 mb-6" id="status-pill-container">
            <button onclick="setStatus('')" class="status-pill px-4 py-1.5 rounded-full text-xs font-bold transition-all border active-pill" data-status="">Semua</button>
            <button onclick="setStatus('approved')" class="status-pill px-4 py-1.5 rounded-full text-xs font-bold transition-all border border-gray-200 text-gray-600 hover:bg-gray-50" data-status="approved">Approved</button>
            <button onclick="setStatus('in process')" class="status-pill px-4 py-1.5 rounded-full text-xs font-bold transition-all border border-gray-200 text-gray-600 hover:bg-gray-50" data-status="in process">In Process</button>
            <button onclick="setStatus('ditolak')" class="status-pill px-4 py-1.5 rounded-full text-xs font-bold transition-all border border-gray-200 text-gray-600 hover:bg-gray-50" data-status="ditolak">Ditolak</button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-50/50 rounded-lg">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-1/3">Proposal Details</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-1/2">Progres Tahapan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-1/6 text-center">Status Akhir</th>
                    </tr>
                </thead>
                <tbody id="monitoring-table-body" class="divide-y divide-gray-100">
                    @forelse($list_proposal as $item)
                        @php
                            $total_langkah = count($tahapan_all) - 1;
                            $posisi_sekarang = array_search($item['tahap_sekarang'], $tahapan_all);
                            if ($posisi_sekarang === false) $posisi_sekarang = 0;

                            $status_lower = strtolower($item['status']);
                            $is_ditolak = $status_lower === 'ditolak';
                            $lebar_progress = $posisi_sekarang > 0 ? ($posisi_sekarang / $total_langkah) * 100 : 0;
                            
                            $progress_color = $is_ditolak ? 'bg-red-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500';
                            
                            $status_badge = match ($status_lower) {
                                'approved'   => 'text-green-700 bg-green-100 border-green-200',
                                'ditolak'    => 'text-red-700 bg-red-100 border-red-200',
                                'in process' => 'text-blue-700 bg-blue-100 border-blue-200',
                                default      => 'text-gray-700 bg-gray-100 border-gray-200',
                            };
                        @endphp
                        <tr class="monitoring-row hover:bg-blue-50/30 transition-colors group" 
                            data-nama="{{ strtolower($item['nama']) }} {{ strtolower($item['pengusul']) }} {{ $item['nim'] }}"
                            data-jurusan="{{ strtolower($item['jurusan']) }}"
                            data-status="{{ strtolower($item['status']) }}">
                            <td class="px-6 py-6 align-top">
                                <div class="text-sm font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ $item['nama'] }}</div>
                                <div class="text-[11px] text-gray-500 mt-1.5">
                                    <span class="font-bold text-blue-600">{{ $item['pengusul'] }} ({{ $item['nim'] }})</span>
                                    <span class="mx-1.5 text-gray-300">&bull;</span>
                                    <span class="text-gray-400 font-medium">{{ $item['jurusan'] }}</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-6 align-middle">
                                <div class="relative w-full h-12 flex items-center">
                                    {{-- Progress Line Background --}}
                                    <div class="absolute top-1/2 -translate-y-1/2 left-0 w-full h-1.5 bg-gray-100 rounded-full z-0"></div> 
                                    {{-- Progress Line Active --}}
                                    <div class="absolute top-1/2 -translate-y-1/2 left-0 h-1.5 {{ $progress_color }} rounded-full z-0 transition-all duration-700 ease-out shadow-sm" 
                                         style="width: {{ $lebar_progress }}%;"></div> 
                                    
                                    @foreach($tahapan_all as $index => $nama_tahap)
                                        @php
                                            $is_completed = $index < $posisi_sekarang;
                                            $is_active = $index == $posisi_sekarang;
                                            
                                            $dot_cls = 'bg-white border-gray-300';
                                            $icon = '';
                                            
                                            if ($is_completed) {
                                                $dot_cls = 'bg-blue-600 border-blue-600 text-white';
                                                $icon = '<i class="fas fa-check text-[8px]"></i>';
                                            } elseif ($is_active) {
                                                if ($is_ditolak) {
                                                    $dot_cls = 'bg-red-500 border-red-500 ring-4 ring-red-100 scale-125 text-white';
                                                    $icon = '<i class="fas fa-times text-[8px]"></i>';
                                                } else {
                                                    $dot_cls = 'bg-white border-blue-600 ring-4 ring-blue-100 scale-125 text-blue-600';
                                                    $icon = '<div class="w-1.5 h-1.5 bg-blue-600 rounded-full animate-pulse"></div>';
                                                }
                                            }
                                            
                                            $pos_left = $total_langkah > 0 ? ($index / $total_langkah) * 100 : 0;
                                        @endphp
                                        <div class="absolute z-10 flex flex-col items-center" style="left: {{ $pos_left }}%; transform: translateX(-50%);">
                                            <div class="w-5 h-5 rounded-full border-2 {{ $dot_cls }} flex items-center justify-center transition-all duration-300 shadow-sm bg-white">
                                                {!! $icon !!}
                                            </div>
                                            <span class="absolute -bottom-6 whitespace-nowrap text-[10px] font-bold text-gray-500 uppercase tracking-tighter opacity-0 group-hover:opacity-100 transition-opacity">
                                                {{ $nama_tahap }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            
                            <td class="px-6 py-6 align-top text-center">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $status_badge }}">
                                    {{ $item['status'] }}
                                </span>
                                <div class="text-[10px] text-gray-400 mt-2 font-medium italic">
                                    {{ $item['tahap_sekarang'] }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="3" class="text-center py-20 text-gray-400">
                                <i class="fas fa-search text-4xl mb-3 block opacity-20"></i>
                                Tidak ada proposal untuk dimonitor.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </section>
</main>

<style>
    .active-pill {
        background-color: #3b82f6 !important;
        color: white !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.2);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-monitoring');
    const filterJurusan = document.getElementById('filter-jurusan');
    const rows = document.querySelectorAll('.monitoring-row');
    const empty = document.getElementById('empty-row');
    let currentStatus = '';

    function applyFilters() {
        const searchVal = searchInput.value.toLowerCase().trim();
        const jurusanVal = filterJurusan.value.toLowerCase();
        const statusVal = currentStatus.toLowerCase();
        let visible = 0;

        rows.forEach(row => {
            const matchesSearch = row.dataset.nama.includes(searchVal);
            const matchesJurusan = !jurusanVal || row.dataset.jurusan === jurusanVal;
            const matchesStatus = !statusVal || row.dataset.status === statusVal;

            if (matchesSearch && matchesJurusan && matchesStatus) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        if (empty) empty.style.display = visible === 0 ? '' : 'none';
    }

    window.setStatus = s => {
        currentStatus = s;
        document.querySelectorAll('.status-pill').forEach(p => {
            if(p.getAttribute('data-status') === s) p.classList.add('active-pill');
            else p.classList.remove('active-pill');
        });
        applyFilters();
    };

    searchInput?.addEventListener('input', applyFilters);
    filterJurusan?.addEventListener('change', applyFilters);
});
</script>
@endsection
