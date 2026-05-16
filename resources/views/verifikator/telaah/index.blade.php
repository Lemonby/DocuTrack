@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-600"></i> Antrian Pengajuan Telaah
                </h3>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filter-jurusan" class="pl-9 pr-8 py-2.5 border border-gray-300 rounded-lg text-sm bg-white appearance-none cursor-pointer focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusan_list as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    <button id="reset-btn" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 flex items-center gap-2">
                        <i class="fas fa-redo text-xs"></i> Reset
                    </button>
                </div>
            </div>
            <div class="mt-4 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-input" placeholder="Cari nama kegiatan, pengusul, atau NIM..."
                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[250px]">Kegiatan & Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-gray-100 bg-white"></tbody>
            </table>
        </div>

        <div class="md:hidden">
            <div id="mobile-list" class="p-3 space-y-3"></div>
        </div>

        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                    Menampilkan <span id="showing" class="font-semibold text-gray-800">0</span> dari <span id="total" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

</main>
@endsection

<style>
    .active-pill {
        background-color: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }
</style>

@push('scripts')
<script>
window.dataUsulan = @json($list_usulan ?? []);

document.addEventListener('DOMContentLoaded', () => {
    const ITEMS_PER_PAGE = 10;
    let all = window.dataUsulan || [], filtered = [...all], page = 1, currentStatus = '';
    const $id = id => document.getElementById(id);

    function esc(s) { if (!s) return ''; const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function fmt(s) { return s ? new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '-'; }
    function badge(status) {
        const m = {
            'telah direvisi': 'bg-purple-100 text-purple-700 border-purple-200',
            'menunggu':  'bg-blue-100 text-blue-700 border-blue-200',
        };
        const cls = m[(status||'').toLowerCase()] || 'bg-blue-100 text-blue-700 border-blue-200';
        const txt = (status||'Menunggu');
        return `<span class="px-2.5 py-1 rounded-full text-xs font-semibold border ${cls}">${esc(txt)}</span>`;
    }

    function apply() {
        const s = ($id('search-input')?.value||'').toLowerCase();
        const j = ($id('filter-jurusan')?.value||'').toLowerCase();

        filtered = all.filter(i => {
            const txt = ((i.nama||'')+(i.pengusul||'')+(i.nim||'')+(i.jurusan||'')).toLowerCase();
            const ijurusan = (i.jurusan||'').toLowerCase();
            
            const matchesSearch = !s || txt.includes(s);
            const matchesJurusan = !j || ijurusan === j;

            return matchesSearch && matchesJurusan;
        });
        page = 1; render();
    }

    function render() {
        const start = (page-1)*ITEMS_PER_PAGE, data = filtered.slice(start, start+ITEMS_PER_PAGE), tp = Math.ceil(filtered.length/ITEMS_PER_PAGE);
        const empty = `<tr><td colspan="5" class="py-14 text-center"><i class="fas fa-inbox text-3xl text-gray-300 block mb-2"></i><span class="text-gray-400 text-sm">${all.length===0?'Belum ada usulan.':'Data tidak ditemukan.'}</span></td></tr>`;

        $id('table-body').innerHTML = data.length===0 ? empty : data.map((it,idx) => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-600">${start+idx+1}.</td>
                <td class="px-6 py-4 text-sm">
                    <div class="font-semibold text-gray-900">${esc(it.nama||'Tanpa Judul')}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        <span class="font-medium text-blue-600">${esc(it.pengusul||'')} (${esc(it.nim||'-')})</span>
                        <span class="mx-1.5 text-gray-300">&bull;</span>
                        <span class="text-gray-400">${esc(it.jurusan||'-')}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-blue-400 mr-1 text-xs"></i>${fmt(it.tanggal_pengajuan)}</td>
                <td class="px-6 py-4">${badge(it.status)}</td>
                <td class="px-6 py-4">
                    <a href="/verifikator/telaah/show/${it.id||0}" class="inline-flex items-center gap-1 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700 shadow-sm">
                        <i class="fas fa-search"></i> Telaah
                    </a>
                </td>
            </tr>`).join('');

        $id('mobile-list').innerHTML = data.length===0 ?
            `<div class="text-center py-12 text-gray-400"><i class="fas fa-inbox text-3xl mb-2 block"></i>Belum ada data usulan.</div>` :
            data.map((it,idx) => `
            <div class="bg-white border border-l-4 border-l-blue-500 border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-100">
                    <span class="bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded">#${start+idx+1}</span>
                    ${badge(it.status)}
                </div>
                <p class="font-semibold text-gray-900 text-sm">${esc(it.nama||'Tanpa Judul')}</p>
                <p class="text-xs text-gray-500 mt-1">${esc(it.pengusul||'')} &bull; ${esc(it.jurusan||'-')}</p>
                <a href="/verifikator/telaah/show/${it.id||0}" class="mt-3 w-full flex items-center justify-center gap-1 bg-blue-600 text-white py-2 rounded-lg text-xs font-semibold hover:bg-blue-700">
                    <i class="fas fa-search"></i> Telaah Usulan
                </a>
            </div>`).join('');

        $id('showing').textContent = filtered.length===0 ? 0 : data.length;
        $id('total').textContent = filtered.length;

        const pg = $id('pagination');
        if (tp<=1) { pg.innerHTML=''; return; }
        let h = `<button class="px-3 py-1.5 rounded border text-xs ${page===1?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="goPage(${page-1})" ${page===1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
        for (let i=1;i<=tp;i++) {
            if(i===1||i===tp||(i>=page-1&&i<=page+1)) h+=`<button class="px-3 py-1.5 rounded border text-xs font-medium ${i===page?'bg-blue-600 text-white':'hover:bg-gray-100'}" onclick="goPage(${i})">${i}</button>`;
            else if(i===page-2||i===page+2) h+=`<span class="px-1 text-gray-400 self-center text-xs">...</span>`;
        }
        h+=`<button class="px-3 py-1.5 rounded border text-xs ${page===tp?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="goPage(${page+1})" ${page===tp?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
        pg.innerHTML=h;
    }

    window.goPage = p => { const tp=Math.ceil(filtered.length/ITEMS_PER_PAGE); if(p>=1&&p<=tp&&p!==page){page=p;render();} };

    let t; $id('search-input')?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(apply,300);});
    $id('filter-jurusan')?.addEventListener('change', apply);
    $id('reset-btn')?.addEventListener('click', () => { 
        $id('search-input').value=''; 
        $id('filter-jurusan').value=''; 
        apply(); 
    });

    render();
});
</script>
@endpush
