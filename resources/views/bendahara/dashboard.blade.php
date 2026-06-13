@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-3 sm:p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-3 rounded-r-lg shadow-sm flex items-center gap-2">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-green-700 font-medium text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Statistics Cards -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-6 md:mb-8">
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-blue-400 to-blue-500 hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div class="order-2 sm:order-1">
                    <h3 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-0.5">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Total KAK</p>
                </div>
                <div class="order-1 sm:order-2 p-2 sm:p-3 rounded-full bg-white/10">
                    <i class="fas fa-layer-group text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-emerald-400 to-emerald-500 hover:shadow-[0_0_20px_rgba(52,211,153,0.5)] hover:-translate-y-1 transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div class="order-2 sm:order-1">
                    <h3 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-0.5">{{ $stats['danaDiberikan'] ?? 0 }}</h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Dana Dicairkan</p>
                </div>
                <div class="order-1 sm:order-2 p-2 sm:p-3 rounded-full bg-white/10">
                    <i class="fas fa-money-bill-wave text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-amber-400 to-amber-500 hover:shadow-[0_0_20px_rgba(251,191,36,0.5)] hover:-translate-y-1 transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div class="order-2 sm:order-1">
                    <h3 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-0.5">{{ $stats['menunggu'] ?? 0 }}</h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Menunggu</p>
                </div>
                <div class="order-1 sm:order-2 p-2 sm:p-3 rounded-full bg-white/10">
                    <i class="fas fa-hourglass-half text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-rose-400 to-rose-500 hover:shadow-[0_0_20px_rgba(251,113,133,0.5)] hover:-translate-y-1 transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div class="order-2 sm:order-1">
                    <h3 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-0.5">{{ $stats['ditolak'] ?? 0 }}</h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Ditolak</p>
                </div>
                <div class="order-1 sm:order-2 p-2 sm:p-3 rounded-full bg-white/10">
                    <i class="fas fa-times-circle text-lg sm:text-xl"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Table KAK / Pencairan Dana -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 flex flex-col">
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-invoice-dollar text-blue-600"></i> Antrian Pencairan Dana (KAK)
                </h3>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filter-status-kak" class="pl-9 pr-8 py-2 border border-gray-300 rounded-lg text-xs text-gray-700 focus:ring-2 focus:ring-blue-500 bg-white appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="belum dicairkan">Belum Dicairkan</option>
                            <option value="sudah dicairkan">Sudah Dicairkan</option>
                        </select>
                    </div>
                    <button id="reset-filter-kak" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 transition-colors flex items-center gap-1">
                        <i class="fas fa-redo text-xs"></i> Reset
                    </button>
                </div>
            </div>
            <div class="mt-3 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-kak" placeholder="Cari nama kegiatan atau pengusul..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto overflow-y-auto" style="max-height:400px;">
            <table class="w-full min-w-[800px]" id="table-kak">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan & Pengusul</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-kak" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
        <div class="md:hidden overflow-y-auto" style="max-height:400px;">
            <div id="cards-kak" class="space-y-3 p-4"></div>
        </div>
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs sm:text-sm text-gray-600">
                    Menampilkan <span id="showing-kak" class="font-semibold text-gray-800">0</span> dari <span id="total-kak" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-kak" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

    <!-- Table LPJ -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 flex flex-col">
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-emerald-600"></i> Antrian Validasi LPJ
                </h3>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filter-status-lpj" class="pl-9 pr-8 py-2 border border-gray-300 rounded-lg text-xs text-gray-700 focus:ring-2 focus:ring-emerald-500 bg-white appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="menunggu verifikasi">Menunggu Verifikasi</option>
                            <option value="revisi">Revisi</option>
                            <option value="disetujui">Disetujui</option>
                        </select>
                    </div>
                    <button id="reset-filter-lpj" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 transition-colors flex items-center gap-1">
                        <i class="fas fa-redo text-xs"></i> Reset
                    </button>
                </div>
            </div>
            <div class="mt-3 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-lpj" placeholder="Cari nama kegiatan atau pengusul..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto overflow-y-auto" style="max-height:400px;">
            <table class="w-full min-w-[900px]" id="table-lpj">
                <thead class="bg-gradient-to-r from-emerald-50 to-green-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan & Pengusul</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-lpj" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
        <div class="md:hidden overflow-y-auto" style="max-height:400px;">
            <div id="cards-lpj" class="space-y-3 p-4"></div>
        </div>
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="text-xs sm:text-sm text-gray-600">
                    Menampilkan <span id="showing-lpj" class="font-semibold text-gray-800">0</span> dari <span id="total-lpj" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-lpj" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

</main>

@push('scripts')
<script>
window.dataKAK = @json($list_kak ?? []);
window.dataLPJ = @json($list_lpj ?? []);

(function() {
    const ROWS = 5;

    function esc(s) {
        if (!s) return '';
        const d = document.createElement('div'); d.textContent = s; return d.innerHTML;
    }
    function fmt(s) {
        if (!s) return '-';
        return new Date(s).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
    }
    function statusBadge(status) {
        const s = (status || '').toLowerCase().replace(/_/g, ' ');
        let cls = 'bg-slate-100 text-slate-600 border-slate-200';
        if (['sudah dicairkan', 'sudah dicairkan sebagian', 'disetujui', 'selesai', 'lpj disetujui'].includes(s)) cls = 'bg-emerald-100 text-emerald-700 border-emerald-200';
        else if (['belum dicairkan', 'menunggu verifikasi', 'menunggu'].includes(s)) cls = 'bg-blue-100 text-blue-700 border-blue-200';
        else if (['telah direvisi', 'telah diverifikasi'].includes(s)) cls = 'bg-purple-100 text-purple-700 border-purple-200';
        else if (s === 'revisi') cls = 'bg-amber-100 text-amber-700 border-amber-200';
        else if (s === 'ditolak') cls = 'bg-rose-100 text-rose-700 border-rose-200';
        return `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border ${cls}">${esc(status || '-').replace(/_/g, ' ')}</span>`;
    }

    function initTable({allData, searchId, filterStatusId, resetId, tbodyId, cardsId, showingId, totalId, paginationId, buildRow, buildCard, actionUrl}) {
        let filtered = [...allData], page = 1;
        const search = document.getElementById(searchId);
        const fStatus = document.getElementById(filterStatusId);
        const resetBtn = document.getElementById(resetId);
        const tbody = document.getElementById(tbodyId);
        const cards = document.getElementById(cardsId);
        const showing = document.getElementById(showingId);
        const total = document.getElementById(totalId);
        const pagination = document.getElementById(paginationId);

        function applyFilters() {
            const s = search?.value.toLowerCase().trim() || '';
            const st = fStatus?.value.toLowerCase() || '';
            filtered = allData.filter(item => {
                const txt = ((item.nama || '') + ' ' + (item.pengusul || '') + ' ' + (item.jurusan || '')).toLowerCase();
                const statusMatch = !st || (item.status || '').toLowerCase() === st;
                return (!s || txt.includes(s)) && statusMatch;
            });
            page = 1; render();
        }

        function render() {
            const start = (page - 1) * ROWS;
            const pageData = filtered.slice(start, start + ROWS);
            const totalPages = Math.ceil(filtered.length / ROWS);
            const empty = `<tr><td colspan="5" class="px-6 py-14 text-center"><i class="fas fa-inbox text-3xl text-gray-300 block mb-2"></i><span class="text-gray-500 text-sm">${allData.length === 0 ? 'Belum ada data.' : 'Data tidak ditemukan.'}</span></td></tr>`;

            if (tbody) tbody.innerHTML = pageData.length === 0 ? empty : pageData.map((item, i) => buildRow(item, start + i + 1, actionUrl)).join('');
            if (cards) cards.innerHTML = pageData.length === 0
                ? `<div class="text-center py-12 text-gray-400"><i class="fas fa-inbox text-3xl mb-2 block"></i>Belum ada data.</div>`
                : pageData.map((item, i) => buildCard(item, start + i + 1, actionUrl)).join('');

            if (showing) showing.textContent = filtered.length === 0 ? 0 : pageData.length;
            if (total) total.textContent = filtered.length;

            if (pagination) {
                if (totalPages <= 1) { pagination.innerHTML = ''; return; }
                let html = `<button class="px-3 py-1.5 rounded border text-xs ${page===1?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="window['goPage_${tbodyId}'](${page-1})" ${page===1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= page - 1 && i <= page + 1))
                        html += `<button class="px-3 py-1.5 rounded border text-xs font-medium ${i===page?'bg-blue-600 text-white':'hover:bg-gray-100'}" onclick="window['goPage_${tbodyId}'](${i})">${i}</button>`;
                    else if (i === page - 2 || i === page + 2)
                        html += `<span class="px-1 text-gray-400 self-center text-xs">...</span>`;
                }
                html += `<button class="px-3 py-1.5 rounded border text-xs ${page===totalPages?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="window['goPage_${tbodyId}'](${page+1})" ${page===totalPages?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
                pagination.innerHTML = html;
            }
            window[`goPage_${tbodyId}`] = p => { const tp = Math.ceil(filtered.length/ROWS); if(p>=1&&p<=tp&&p!==page){page=p;render();} };
        }

        if (search) { let t; search.addEventListener('input', () => { clearTimeout(t); t = setTimeout(applyFilters, 300); }); }
        if (fStatus) fStatus.addEventListener('change', applyFilters);
        if (resetBtn) resetBtn.addEventListener('click', () => { if(search) search.value=''; if(fStatus) fStatus.value=''; applyFilters(); });
        render();
    }

    function kakRow(item, no, url) {
        return `<tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-sm text-gray-600">${no}.</td>
            <td class="px-6 py-4 text-sm"><div class="font-semibold text-gray-900">${esc(item.nama)}</div><div class="text-xs text-gray-500 mt-0.5">${esc(item.pengusul||'')} (${esc(item.nim||'-')}) &bull; ${esc(item.jurusan||'-')}</div></td>
            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-blue-400 mr-1 text-xs"></i>${fmt(item.tanggal_pengajuan)}</td>
            <td class="px-6 py-4">${statusBadge(item.status)}</td>
            <td class="px-6 py-4"><a href="${url}/${item.id||0}?from=dashboard" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-blue-700 transition-all shadow-sm"><i class="fas fa-money-bill-wave text-[8px]"></i>Cairkan</a></td>
        </tr>`;
    }
    function kakCard(item, no, url) {
        return `<div class="bg-white border border-gray-200 border-l-4 border-l-blue-500 rounded-xl p-4 shadow-sm">
            <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-100"><span class="bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded">#${no}</span>${statusBadge(item.status)}</div>
            <p class="font-semibold text-gray-900 text-sm">${esc(item.nama)}</p>
            <p class="text-xs text-gray-500 mt-1">${esc(item.pengusul||'')} &bull; ${fmt(item.tanggal_pengajuan)}</p>
            <a href="${url}/${item.id||0}?from=dashboard" class="mt-3 w-full flex items-center justify-center gap-2 bg-blue-600 text-white py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-blue-700 transition-all shadow-sm"><i class="fas fa-money-bill-wave text-[8px]"></i>Detail</a>
        </div>`;
    }
    function lpjRow(item, no, url) {
        return `<tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-sm text-gray-600">${no}.</td>
            <td class="px-6 py-4 text-sm"><div class="font-semibold text-gray-900">${esc(item.nama)}</div><div class="text-xs text-gray-500 mt-0.5">${esc(item.pengusul||'')} (${esc(item.nim||'-')}) &bull; ${esc(item.jurusan||'-')}</div></td>
            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-gray-400 mr-1 text-xs"></i>${fmt(item.tanggal_pengajuan)}</td>
            <td class="px-6 py-4">${statusBadge(item.status)}</td>
            <td class="px-6 py-4"><a href="${url}/${item.id||0}?from=dashboard" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-sm"><i class="fas fa-file-alt text-[8px]"></i>Validasi</a></td>
        </tr>`;
    }
    function lpjCard(item, no, url) {
        return `<div class="bg-white border border-gray-200 border-l-4 border-l-emerald-500 rounded-xl p-4 shadow-sm">
            <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-100"><span class="bg-emerald-600 text-white text-xs font-bold px-2 py-0.5 rounded">#${no}</span>${statusBadge(item.status)}</div>
            <p class="font-semibold text-gray-900 text-sm">${esc(item.nama)}</p>
            <p class="text-xs text-gray-500 mt-1">${esc(item.pengusul||'')} &bull; ${fmt(item.tanggal_pengajuan)}</p>
            <a href="${url}/${item.id||0}?from=dashboard" class="mt-3 w-full flex items-center justify-center gap-2 bg-emerald-600 text-white py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-sm"><i class="fas fa-file-alt text-[8px]"></i>Validasi LPJ</a>
        </div>`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        initTable({
            allData: window.dataKAK || [],
            searchId: 'search-kak', filterStatusId: 'filter-status-kak', resetId: 'reset-filter-kak',
            tbodyId: 'tbody-kak', cardsId: 'cards-kak', showingId: 'showing-kak', totalId: 'total-kak', paginationId: 'pagination-kak',
            buildRow: kakRow, buildCard: kakCard,
            actionUrl: '{{ url("/bendahara/pencairan-dana/show") }}'
        });
        initTable({
            allData: window.dataLPJ || [],
            searchId: 'search-lpj', filterStatusId: 'filter-status-lpj', resetId: 'reset-filter-lpj',
            tbodyId: 'tbody-lpj', cardsId: 'cards-lpj', showingId: 'showing-lpj', totalId: 'total-lpj', paginationId: 'pagination-lpj',
            buildRow: lpjRow, buildCard: lpjCard,
            actionUrl: '{{ url("/bendahara/lpj/show") }}'
        });
    });
})();
</script>
@endpush
@endsection
