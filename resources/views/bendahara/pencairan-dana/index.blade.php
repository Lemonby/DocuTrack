@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-6 flex flex-col">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 pb-5 border-b border-gray-200 gap-3">
            <div class="flex-shrink-0">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Antrian Pencairan Dana (KAK)</h2>
                <p class="text-sm text-gray-500 mt-1 hidden md:block">KAK yang telah disetujui dan menunggu pencairan dana.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 flex-wrap">
                <div class="relative">
                    <i class="fas fa-filter absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs"></i>
                    <select id="filter-status" class="pl-9 pr-9 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="belum dicairkan">Belum Dicairkan</option>
                        <option value="sudah dicairkan">Sudah Dicairkan</option>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                </div>
                <div class="relative">
                    <i class="fas fa-graduation-cap absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs"></i>
                    <select id="filter-jurusan" class="pl-9 pr-9 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none cursor-pointer">
                        <option value="">Semua Jurusan</option>
                        @foreach(['Teknik Informatika dan Komputer', 'Teknik Grafika dan Penerbitan', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil', 'Administrasi Niaga', 'Akuntansi'] as $j)
                        <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                </div>
                <button id="reset-filter" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors flex items-center gap-2">
                    <i class="fas fa-redo text-xs"></i> Reset
                </button>
            </div>
        </div>

        <div class="relative mb-4">
            <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 z-10 text-sm"></i>
            <input type="text" id="search-input" placeholder="Cari nama kegiatan atau pengusul..."
                   class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan & Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body-desktop" class="divide-y divide-gray-100 bg-white"></tbody>
            </table>
        </div>

        <div class="md:hidden overflow-y-visible">
            <div id="mobile-list" class="space-y-3"></div>
        </div>

        <div class="p-3 sm:p-4 mt-4 border-t border-gray-200 bg-gray-50 rounded-lg">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                    Menampilkan <span id="showing-start" class="font-semibold text-gray-800">0</span> s.d.
                    <span id="showing-end" class="font-semibold text-gray-800">0</span> dari
                    <span id="total-records" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-buttons" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
window.kakData = @json($list_kak ?? []);

document.addEventListener('DOMContentLoaded', () => {
    const allData = window.kakData || [];
    const ROWS = 5;
    let filtered = [...allData], page = 1;

    const searchInput = document.getElementById('search-input');
    const filterStatus = document.getElementById('filter-status');
    const filterJurusan = document.getElementById('filter-jurusan');
    const resetBtn = document.getElementById('reset-filter');
    const tbody = document.getElementById('table-body-desktop');
    const mobileList = document.getElementById('mobile-list');
    const paginationEl = document.getElementById('pagination-buttons');

    function esc(s) { if(!s)return''; const d=document.createElement('div');d.textContent=s;return d.innerHTML; }
    function fmt(s) { if(!s)return'-'; return new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}); }
    function badge(status) {
        const s = (status||'').toLowerCase();
        let cls = 'bg-blue-100 text-blue-700 border-blue-200';
        if (['sudah dicairkan'].includes(s)) cls = 'bg-emerald-100 text-emerald-700 border-emerald-200';
        else if (['belum dicairkan'].includes(s)) cls = 'bg-amber-100 text-amber-700 border-amber-200';
        return `<span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border ${cls}">${esc(status||'-')}</span>`;
    }

    function applyFilters() {
        const s = (searchInput?.value||'').toLowerCase();
        const st = (filterStatus?.value||'').toLowerCase();
        const j = filterJurusan?.value || '';
        filtered = allData.filter(item => {
            const txt = ((item.nama||'')+(item.pengusul||'')+(item.nim||'')+(item.jurusan||'')).toLowerCase();
            const stMatch = !st || (item.status||'').toLowerCase() === st;
            const jMatch = !j || (item.jurusan||'') === j;
            return (!s||txt.includes(s)) && stMatch && jMatch;
        });
        page = 1; render();
    }

    function render() {
        const start = (page-1)*ROWS;
        const pageData = filtered.slice(start, start+ROWS);
        const totalPages = Math.ceil(filtered.length/ROWS);
        const empty = `<tr><td colspan="5" class="px-6 py-14 text-center"><i class="fas fa-inbox text-3xl text-gray-300 block mb-2"></i><span class="text-gray-400 text-sm">${allData.length===0?'Belum ada data pencairan.':'Data tidak ditemukan.'}</span></td></tr>`;

        if (tbody) tbody.innerHTML = pageData.length===0 ? empty : pageData.map((item,idx) => {
            const no = start+idx+1;
            return `<tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-5 text-sm text-gray-700">${no}.</td>
                <td class="px-6 py-5 text-sm">
                    <div class="font-semibold text-gray-900">${esc(item.nama||'Tanpa Judul')}</div>
                    <div class="text-xs text-gray-500 mt-0.5"><i class="fas fa-user mr-1"></i>${esc(item.pengusul||'N/A')} (${esc(item.nim||'-')})</div>
                    <div class="text-xs text-gray-400"><i class="fas fa-graduation-cap mr-1 text-blue-400"></i>${esc(item.jurusan||'-')}</div>
                </td>
                <td class="px-6 py-5 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-blue-400 mr-1"></i>${fmt(item.tanggal_pengajuan)}</td>
                <td class="px-6 py-5">${badge(item.status)}</td>
                <td class="px-6 py-5">
                    <a href="/bendahara/pencairan-dana/show/${item.id||0}" class="inline-flex items-center gap-1.5 bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-blue-700 transition-colors shadow-sm">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </td>
            </tr>`;
        }).join('');

        if (mobileList) mobileList.innerHTML = pageData.length===0
            ? `<div class="text-center py-12 text-gray-400"><i class="fas fa-inbox text-3xl mb-2 block"></i>Belum ada data.</div>`
            : pageData.map((item,idx) => {
                const no = start+idx+1;
                return `<div class="bg-white border border-gray-200 border-l-4 border-l-blue-500 rounded-xl p-4 shadow-sm">
                    <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-100"><span class="bg-blue-600 text-white text-xs font-bold px-2.5 py-1 rounded-lg">#${no}</span>${badge(item.status)}</div>
                    <p class="font-semibold text-gray-900 mb-1">${esc(item.nama||'Tanpa Judul')}</p>
                    <p class="text-xs text-gray-500 mb-3">${esc(item.pengusul||'N/A')} &bull; ${esc(item.jurusan||'-')}</p>
                    <a href="/bendahara/pencairan-dana/show/${item.id||0}" class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye"></i> Lihat Detail
                    </a>
                </div>`;
            }).join('');

        document.getElementById('showing-start').textContent = filtered.length===0?0:start+1;
        document.getElementById('showing-end').textContent = Math.min(start+ROWS, filtered.length);
        document.getElementById('total-records').textContent = filtered.length;

        if (paginationEl) {
            if (totalPages<=1){paginationEl.innerHTML='';return;}
            let html = `<button class="px-3 py-1.5 rounded-lg border text-sm font-medium ${page===1?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="goPage(${page-1})" ${page===1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
            for (let i=1;i<=totalPages;i++) {
                if(i===1||i===totalPages||(i>=page-1&&i<=page+1)) html+=`<button class="px-3 py-1.5 rounded-lg border text-sm font-medium ${i===page?'bg-blue-600 text-white border-transparent':'hover:bg-gray-100'}" onclick="goPage(${i})">${i}</button>`;
                else if(i===page-2||i===page+2) html+=`<span class="px-2 text-gray-400 self-center">...</span>`;
            }
            html+=`<button class="px-3 py-1.5 rounded-lg border text-sm font-medium ${page===totalPages?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="goPage(${page+1})" ${page===totalPages?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
            paginationEl.innerHTML = html;
        }
    }

    window.goPage = p => { const tp=Math.ceil(filtered.length/ROWS); if(p>=1&&p<=tp&&p!==page){page=p;render();} };
    if(searchInput){let t;searchInput.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(applyFilters,300);});}
    if(filterStatus) filterStatus.addEventListener('change', applyFilters);
    if(filterJurusan) filterJurusan.addEventListener('change', applyFilters);
    if(resetBtn) resetBtn.addEventListener('click',()=>{if(searchInput)searchInput.value='';if(filterStatus)filterStatus.value='';if(filterJurusan)filterJurusan.value='';applyFilters();});
    render();
});
</script>
@endpush
