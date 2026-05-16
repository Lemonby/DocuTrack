@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-3 md:p-5 lg:p-6 -mt-10 md:-mt-20 max-w-[1600px] mx-auto w-full">

    <!-- Kontainer Monitoring (Readable White Theme - Zero Dropdown) -->
    <div class="bg-white rounded-[3rem] p-6 md:p-10 border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden">
        
        <!-- Subtle Accents -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-50/50 rounded-full blur-[130px] -mr-80 -mt-80 opacity-60"></div>
        
        <div class="relative z-10">
            <!-- Header Section -->
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="px-4 py-1.5 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-200">Real-Time Oversight</div>
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse border-2 border-white shadow-sm"></div>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tighter">Pemantauan Progres</h1>
                    <p class="text-slate-500 text-xs font-medium italic">Monitor alur pengajuan & anggaran institusi secara akurat.</p>
                </div>

                <div class="flex items-center gap-4 bg-slate-50 p-2 rounded-2xl border border-slate-100 shadow-inner">
                    <button id="refresh-data" class="h-11 px-6 bg-white text-slate-700 rounded-xl font-black text-[10px] tracking-widest flex items-center gap-3 hover:bg-blue-50 transition-all border border-slate-200 shadow-sm">
                        <i class="fas fa-sync-alt text-[10px]"></i>
                        <span>REFRESH DATA</span>
                    </button>
                    <button class="w-11 h-11 bg-blue-600 text-white rounded-xl flex items-center justify-center hover:bg-blue-700 transition-all shadow-xl shadow-blue-100">
                        <i class="fas fa-file-download text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- UNIT SELECTOR (REPLACES DROPDOWN) -->
            <div class="mb-10">
                <div class="flex justify-between items-center mb-4 px-1">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Unit Kerja Filter</h3>
                    <span class="text-[10px] font-bold text-blue-500 uppercase italic">Slide to explore all units</span>
                </div>
                <div class="flex items-center gap-3 overflow-x-auto pb-4 scrollbar-hide px-1">
                    <button class="jurusan-pill px-8 py-4 rounded-[1.8rem] text-xs font-black uppercase tracking-widest transition-all duration-500 whitespace-nowrap bg-blue-600 text-white shadow-xl shadow-blue-100 border border-blue-500 active-jurusan" data-jurusan="semua">
                        SEMUA UNIT
                    </button>
                    @foreach ($list_jurusan as $jurusan)
                        <button class="jurusan-pill px-8 py-4 rounded-[1.8rem] text-xs font-black uppercase tracking-widest transition-all duration-500 whitespace-nowrap bg-slate-50 text-slate-500 border border-slate-100 hover:border-blue-300 shadow-sm" data-jurusan="{{ $jurusan }}">
                            {{ $jurusan }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- SEARCH & STATUS (ZERO DROPDOWN) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12 items-center">
                <div class="lg:col-span-6 relative">
                    <i class="fas fa-search absolute top-1/2 left-8 -translate-y-1/2 text-blue-400 text-base"></i>
                    <input type="text" id="search-monitoring-input" placeholder="Cari kegiatan, pengusul, atau nomor usulan..." 
                           class="w-full pl-16 pr-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] text-sm font-medium text-slate-700 focus:outline-none focus:ring-4 focus:ring-blue-50 transition-all shadow-inner">
                </div>

                <div class="lg:col-span-6 flex items-center p-2 bg-slate-50 rounded-[2rem] border border-slate-100 shadow-inner overflow-x-auto scrollbar-hide">
                    <button class="status-tab flex-1 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-500 whitespace-nowrap" data-status="Semua">Semua</button>
                    <button class="status-tab flex-1 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-500 whitespace-nowrap" data-status="Menunggu">Menunggu</button>
                    <button class="status-tab flex-1 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-500 whitespace-nowrap" data-status="Approved">Disetujui</button>
                    <button class="status-tab flex-1 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-500 whitespace-nowrap" data-status="Ditolak">Ditolak</button>
                    <button class="status-tab flex-1 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all duration-500 whitespace-nowrap" data-status="LPJ">LPJ</button>
                </div>
            </div>

            <!-- Monitoring Content Area -->
            <div class="space-y-8">
                <div class="hidden md:grid grid-cols-12 gap-10 px-12 mb-4">
                    <div class="col-span-4 text-[11px] font-black text-slate-400 uppercase tracking-widest">Detail Usulan</div>
                    <div class="col-span-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">Tahapan Progres</div>
                    <div class="col-span-3 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">Nilai Anggaran</div>
                </div>

                <div id="monitoring-list-container" class="space-y-6 min-h-[500px]">
                    {{-- Dynamically Populated --}}
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="hidden py-32 text-center bg-slate-50 rounded-[3rem] border border-dashed border-slate-200">
                    <i class="fas fa-search text-blue-200 text-5xl mb-8"></i>
                    <h3 class="text-slate-900 font-black text-xl">Data Tidak Ditemukan</h3>
                    <p class="text-slate-400 text-sm mt-2">Coba gunakan filter lain.</p>
                </div>

                <!-- Loading State -->
                <div id="loading-state" class="hidden space-y-6">
                    @for($i=0; $i<3; $i++)
                    <div class="h-32 bg-slate-50 animate-pulse rounded-[2.5rem] border border-slate-100"></div>
                    @endfor
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col md:flex-row justify-between items-center mt-16 gap-8 border-t border-slate-50 pt-10">
                <p id="pagination-info" class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] italic"></p>
                <div id="pagination-nav" class="flex items-center gap-3"></div>
            </div>
        </div>
    </div>

</main>

<style>
    /* ZERO DROPDOWN TABS */
    .status-tab { color: #94a3b8; cursor: pointer; border: 1px solid transparent; }
    .status-tab.active-tab { 
        background-color: #2563eb !important; 
        color: #ffffff !important; 
        box-shadow: 0 15px 30px -5px rgba(37, 99, 235, 0.4) !important;
        transform: translateY(-2px);
    }

    .scrollbar-hide::-webkit-scrollbar { display: none; }
    
    /* Progress Timeline */
    .timeline-wrapper { @apply relative w-full h-16 flex items-center px-10; }
    .line-bg { @apply absolute left-10 right-10 h-2.5 bg-slate-100 rounded-full z-0; }
    .line-fill { @apply absolute left-10 h-2.5 bg-blue-500 rounded-full z-10 transition-all duration-1500 shadow-[0_0_15px_rgba(59,130,246,0.4)]; }
    
    /* Pills Interaction */
    .jurusan-pill { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .jurusan-pill:active { transform: scale(0.95); }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const listContainer = document.getElementById('monitoring-list-container');
    const searchInput = document.getElementById('search-monitoring-input');
    const statusTabs = document.querySelectorAll('.status-tab');
    const jurusanPills = document.querySelectorAll('.jurusan-pill');
    const paginationNav = document.getElementById('pagination-nav');
    const paginationInfo = document.getElementById('pagination-info');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');

    let state = { currentPage: 1, status: 'semua', jurusan: 'semua', search: '' };

    async function fetchData() {
        loadingState.classList.remove('hidden');
        listContainer.classList.add('hidden');
        emptyState.classList.add('hidden');
        const params = new URLSearchParams({ page: state.currentPage, status: state.status, jurusan: state.jurusan, search: state.search });

        try {
            const res = await fetch(`{{ route('direktur.monitoring.data') }}?${params}`);
            const data = await res.json();
            if (data.proposals && data.proposals.length > 0) {
                renderList(data.proposals);
                renderPagination(data.pagination);
                listContainer.classList.remove('hidden');
            } else {
                emptyState.classList.remove('hidden');
                listContainer.innerHTML = '';
            }
        } catch (e) { console.error(e); } finally { loadingState.classList.add('hidden'); }
    }

    function renderList(items) {
        listContainer.innerHTML = items.map(item => {
            const isTermin = Math.random() > 0.5;
            const terminText = isTermin ? 'Termin 1 (50%)' : 'Langsung (100%)';
            const terminColor = isTermin ? 'text-amber-600 bg-amber-50 border-amber-100' : 'text-emerald-600 bg-emerald-50 border-emerald-100';

            return `
            <div class="bg-white border border-slate-100 p-8 rounded-[3rem] shadow-sm hover:shadow-blue-200/40 transition-all duration-700 group relative overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-10 items-center relative z-10">
                    <!-- Unit Info -->
                    <div class="md:col-span-4 flex items-center gap-8">
                        <div class="w-20 h-20 rounded-[2.5rem] bg-slate-50 text-slate-300 group-hover:bg-blue-600 group-hover:text-white transition-all duration-1000 shadow-inner flex items-center justify-center">
                            <i class="fas fa-file-invoice-dollar text-3xl"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-lg font-black text-slate-800 truncate mb-2 group-hover:text-blue-600 transition-colors tracking-tight">${item.nama}</h4>
                            <div class="flex flex-wrap items-center gap-4">
                                <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">${item.pengusul}</span>
                                <span class="px-4 py-2 ${terminColor} border rounded-2xl text-[10px] font-black uppercase tracking-tighter shadow-sm">${terminText}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="md:col-span-5 px-8">
                        <div class="timeline-wrapper">
                            <div class="line-bg"></div>
                            <div class="line-fill" style="width: calc(${getProgWidth(item.tahap_sekarang)}%)"></div>
                            <div class="relative w-full flex justify-between">
                                ${['Usulan', 'Verifikasi', 'PPK', 'WD', 'Cair', 'LPJ'].map((s, i) => `
                                    <div class="relative flex flex-col items-center">
                                        <div class="w-5 h-5 rounded-full border-[4px] border-white shadow-lg z-20 transition-all duration-1000 ${i <= getStageIdx(item.tahap_sekarang) ? 'bg-blue-600 scale-125 shadow-blue-200' : 'bg-slate-200'}"></div>
                                        <span class="absolute -bottom-10 text-[10px] font-black uppercase tracking-tighter transition-all duration-700 ${i <= getStageIdx(item.tahap_sekarang) ? 'text-blue-600 opacity-100' : 'text-slate-400 opacity-40'}">${s}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>

                    <!-- Value -->
                    <div class="md:col-span-3 text-right space-y-4">
                        <span class="px-6 py-3 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest ${getStatusStyle(item.status)} shadow-sm">
                            ${item.status}
                        </span>
                        <div class="pt-3">
                            <p class="text-2xl font-black text-slate-900 tracking-tighter">Rp ${numberFormat(item.dana || 15000000)}</p>
                            <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mt-2 italic">VALUASI REALISASI</p>
                        </div>
                    </div>
                </div>
            </div>
        `; }).join('');
    }

    function getStageIdx(stage) { const stages = ['Usulan', 'Verifikasi', 'PPK', 'WD', 'Cair', 'LPJ']; return Math.max(0, stages.indexOf(stage)); }
    function getProgWidth(stage) { const idx = getStageIdx(stage); return (idx / 5) * 100; }

    function getStatusStyle(status) {
        switch(status.toLowerCase()) {
            case 'approved': return 'bg-emerald-50 text-emerald-600 border border-emerald-100';
            case 'ditolak': return 'bg-rose-50 text-rose-600 border border-rose-100';
            case 'menunggu': return 'bg-blue-50 text-blue-600 border border-blue-100';
            case 'revisi': return 'bg-amber-50 text-amber-600 border border-amber-100';
            default: return 'bg-slate-50 text-slate-400';
        }
    }

    function renderPagination(p) {
        paginationInfo.textContent = `HALAMAN ${p.currentPage} DARI ${Math.ceil(p.totalItems / 5)}`;
        let html = '';
        if(p.currentPage > 1) html += `<button class="p-btn w-14 h-14 rounded-2xl bg-white text-slate-700 border border-slate-100 shadow-xl" data-page="${p.currentPage - 1}"><i class="fas fa-chevron-left text-sm"></i></button>`;
        html += `<button class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-base font-black shadow-2xl shadow-blue-100 scale-110">${p.currentPage}</button>`;
        if(p.showingTo < p.totalItems) html += `<button class="p-btn w-14 h-14 rounded-2xl bg-white text-slate-700 border border-slate-100 shadow-xl" data-page="${p.currentPage + 1}"><i class="fas fa-chevron-right text-sm"></i></button>`;
        paginationNav.innerHTML = html;
        paginationNav.querySelectorAll('.p-btn').forEach(b => b.onclick = () => { state.currentPage = b.dataset.page; fetchData(); });
    }

    function numberFormat(v) { return new Intl.NumberFormat('id-ID').format(v); }

    // INITIAL ACTIVE TAB
    statusTabs[0].classList.add('active-tab');

    // TAB CLICKS
    statusTabs.forEach(tab => tab.onclick = () => {
        statusTabs.forEach(t => t.classList.remove('active-tab'));
        tab.classList.add('active-tab');
        state.status = tab.dataset.status.toLowerCase();
        state.currentPage = 1;
        fetchData();
    });

    // JURUSAN PILL CLICKS
    jurusanPills.forEach(pill => pill.onclick = () => {
        jurusanPills.forEach(p => {
            p.classList.remove('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-100', 'border-blue-500', 'active-jurusan');
            p.classList.add('bg-slate-50', 'text-slate-500', 'border-slate-100');
        });
        pill.classList.add('bg-blue-600', 'text-white', 'shadow-md', 'shadow-blue-100', 'border-blue-500', 'active-jurusan');
        state.jurusan = pill.dataset.jurusan;
        state.currentPage = 1;
        fetchData();
    });

    searchInput.oninput = (e) => { clearTimeout(window.db); window.db = setTimeout(() => { state.search = e.target.value; state.currentPage = 1; fetchData(); }, 400); };
    document.getElementById('refresh-data').onclick = fetchData;

    fetchData();
});
</script>
@endpush

@endsection
