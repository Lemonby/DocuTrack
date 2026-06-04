@extends('layouts.app')

@section('content')
<main class="main-content font-poppins px-3 py-4 sm:p-6 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    {{-- Stats Cards --}}
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        @php
        $cards = [
            ['value' => $stats['total'],     'label' => 'Total Usulan', 'icon' => 'fa-layer-group',    'from' => 'from-blue-400',   'to' => 'to-blue-500',   'text' => 'text-white'],
            ['value' => $stats['disetujui'], 'label' => 'Disetujui',    'icon' => 'fa-check-circle',   'from' => 'from-green-400',  'to' => 'to-green-500',  'text' => 'text-white'],
            ['value' => $stats['ditolak'],   'label' => 'Ditolak',      'icon' => 'fa-times-circle',   'from' => 'from-red-400',    'to' => 'to-red-500',    'text' => 'text-white'],
            ['value' => $stats['pending'],   'label' => 'Pending',      'icon' => 'fa-hourglass-half', 'from' => 'from-yellow-300', 'to' => 'to-yellow-400', 'text' => 'text-yellow-900'],
        ];
        @endphp
        @foreach($cards as $card)
        <div class="relative group p-4 rounded-xl shadow-md overflow-hidden {{ $card['text'] }} bg-gradient-to-br {{ $card['from'] }} {{ $card['to'] }} hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col gap-1">
                <h3 class="text-3xl md:text-5xl font-bold">{{ $card['value'] }}</h3>
                <p class="text-xs md:text-sm font-medium opacity-90">{{ $card['label'] }}</p>
                <div class="mt-2 text-right opacity-70"><i class="fas {{ $card['icon'] }} text-2xl"></i></div>
            </div>
        </div>
        @endforeach
    </section>

    {{-- Daftar Usulan --}}
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-5 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <h3 class="text-base font-black text-slate-800 flex items-center gap-2 uppercase tracking-tight">
                    <i class="fas fa-clipboard-list text-blue-600"></i> Daftar Usulan Masuk
                </h3>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filter-status" class="pl-9 pr-8 py-2 border border-gray-300 rounded-lg text-xs bg-white appearance-none cursor-pointer focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[140px]">
                            <option value="">Semua Status</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="review">Review</option>
                            <option value="revisi">Revisi</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    <div class="relative">
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filter-jurusan" class="pl-9 pr-8 py-2 border border-gray-300 rounded-lg text-xs bg-white appearance-none cursor-pointer focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusan_list as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    <button id="reset-filter" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 flex items-center gap-1">
                        <i class="fas fa-redo text-xs"></i> Reset
                    </button>
                </div>
            </div>
            <div class="mt-3 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-input" placeholder="Cari nama kegiatan, pengusul, atau NIM..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kegiatan & Pengusul</th>
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
        <div class="p-3 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="text-xs text-gray-600">Menampilkan <span id="showing" class="font-semibold">0</span> dari <span id="total" class="font-semibold">0</span> data</div>
                <div id="pagination" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

</main>
@endsection

@push('scripts')
<script>
window.dataUsulan = @json($list_usulan ?? []);

document.addEventListener('DOMContentLoaded', () => {
    const ROWS = 5;
    let all = window.dataUsulan || [], filtered = [...all], page = 1;
    const $id = id => document.getElementById(id);
    const searchInput = $id('search-input');
    const filterStatus = $id('filter-status');
    const filterJurusan = $id('filter-jurusan');

    function esc(s) { if (!s) return ''; const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function fmt(s) { return s ? new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '-'; }
    function badge(status) {
        const s = (status || '').toLowerCase();
        let cls = 'bg-slate-100 text-slate-500 border-slate-200';
        
        if (['disetujui', 'setuju', 'selesai', 'tuntas'].includes(s)) {
            cls = 'bg-emerald-100 text-emerald-700 border-emerald-200';
        } else if (['revisi', 'perlu perbaikan'].includes(s)) {
            cls = 'bg-amber-100 text-amber-700 border-amber-200';
        } else if (['menunggu', 'review', 'pending', 'sedang diproses', 'menunggu verifikasi'].includes(s)) {
            cls = 'bg-blue-100 text-blue-700 border-blue-200';
        } else if (['ditolak', 'tolak', 'batal'].includes(s)) {
            cls = 'bg-rose-100 text-rose-700 border-rose-200';
        }

        return `<span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border ${cls}">${esc(status || 'Menunggu')}</span>`;
    }

    function apply() {
        const s = (searchInput?.value||'').toLowerCase();
        const st = (filterStatus?.value||'').toLowerCase();
        const j = (filterJurusan?.value||'').toLowerCase();
        filtered = all.filter(i => {
            const txt = ((i.nama||'')+(i.pengusul||'')+(i.nim||'')+(i.jurusan||'')).toLowerCase();
            const istatus = (i.status||'menunggu').toLowerCase();
            const ijurusan = (i.jurusan||'').toLowerCase();
            
            return (!s||txt.includes(s)) && (!st||istatus===st) && (!j||ijurusan===j);
        });
        page = 1; render();
    }

    function render() {
        const start = (page-1)*ROWS, data = filtered.slice(start, start+ROWS), tp = Math.ceil(filtered.length/ROWS);
        const empty = `<tr><td colspan="5" class="py-14 text-center"><i class="fas fa-inbox text-3xl text-gray-300 block mb-2"></i><span class="text-gray-400 text-sm">${all.length===0?'Belum ada usulan masuk.':'Data tidak ditemukan.'}</span></td></tr>`;

        $id('table-body').innerHTML = data.length===0 ? empty : data.map((it,idx) => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-600">${start+idx+1}.</td>
                <td class="px-6 py-4 text-sm">
                    <div class="font-semibold text-gray-900">${esc(it.nama||'Tanpa Judul')}</div>
                    <div class="text-xs text-gray-500 mt-0.5">${esc(it.pengusul||'')} (${esc(it.nim||'-')}) &bull; ${esc(it.jurusan||'-')}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-blue-400 mr-1 text-xs"></i>${fmt(it.tanggal_pengajuan)}</td>
                <td class="px-6 py-4">${badge(it.status)}</td>
                <td class="px-6 py-4">
                    <a href="/verifikator/telaah/show/${it.id||0}" class="inline-flex items-center gap-1 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700 shadow-sm">
                        <i class="fas fa-eye"></i> Detail
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
                    <i class="fas fa-eye"></i> Lihat Detail
                </a>
            </div>`).join('');

        $id('showing').textContent = filtered.length===0 ? 0 : start+1;
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

    window.goPage = p => { const tp=Math.ceil(filtered.length/ROWS); if(p>=1&&p<=tp&&p!==page){page=p;render();} };

    let t; searchInput?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(apply,300);});
    filterStatus?.addEventListener('change', apply);
    filterJurusan?.addEventListener('change', apply);
    $id('reset-filter')?.addEventListener('click', () => { 
        searchInput.value=''; 
        filterStatus.value=''; 
        filterJurusan.value=''; 
        apply(); 
    });

    render();
});
</script>
</style>
@endpush
