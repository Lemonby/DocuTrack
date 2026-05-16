@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-indigo-600"></i> Riwayat Verifikasi
            </h3>
            <div class="mt-3 flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="search-input" placeholder="Cari nama kegiatan atau pengusul..."
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="sm:w-64">
                    <select id="jurusan-filter" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Semua Jurusan</option>
                        @foreach($jurusan_list as $jurusan)
                            <option value="{{ strtolower($jurusan) }}">{{ $jurusan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mt-4 flex flex-wrap gap-2" id="status-pill-container">
                <button onclick="setStatus('')" class="status-pill px-4 py-1.5 rounded-full text-xs font-bold transition-all border active-pill" data-status="">Semua</button>
                <button onclick="setStatus('disetujui')" class="status-pill px-4 py-1.5 rounded-full text-xs font-bold transition-all border border-gray-200 text-gray-600 hover:bg-gray-50" data-status="disetujui">Disetujui</button>
                <button onclick="setStatus('revisi')" class="status-pill px-4 py-1.5 rounded-full text-xs font-bold transition-all border border-gray-200 text-gray-600 hover:bg-gray-50" data-status="revisi">Revisi</button>
                <button onclick="setStatus('ditolak')" class="status-pill px-4 py-1.5 rounded-full text-xs font-bold transition-all border border-gray-200 text-gray-600 hover:bg-gray-50" data-status="ditolak">Ditolak</button>
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[250px]">Kegiatan & Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Verifikasi</th>
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
        background-color: #4f46e5 !important;
        color: white !important;
        border-color: #4f46e5 !important;
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }
</style>

@push('scripts')
<script>
window.riwayatData = @json($list_riwayat ?? []);

document.addEventListener('DOMContentLoaded', () => {
    const ITEMS_PER_PAGE = 10;
    let all = window.riwayatData || [], filtered = [...all], page = 1, currentStatus = '';
    const $id = id => document.getElementById(id);

    function esc(s) { if (!s) return ''; const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function fmt(s) { return s ? new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '-'; }
    function badge(status) {
        const m = {
            'disetujui': 'bg-green-100 text-green-700 border-green-200',
            'ditolak':   'bg-red-100 text-red-700 border-red-200',
            'revisi':    'bg-yellow-100 text-yellow-700 border-yellow-200',
        };
        const cls = m[(status||'').toLowerCase()] || 'bg-gray-100 text-gray-500 border-gray-200';
        return `<span class="px-2.5 py-1 rounded-full text-xs font-semibold border ${cls}">${esc(status||'')}</span>`;
    }

    function apply() {
        const s = ($id('search-input')?.value||'').toLowerCase();
        const fStatus = currentStatus.toLowerCase();
        const fJurusan = ($id('jurusan-filter')?.value||'').toLowerCase();
        
        filtered = all.filter(i => {
            const txt = ((i.nama||'')+(i.pengusul||'')+(i.nim||'')).toLowerCase();
            const matchesSearch = !s || txt.includes(s);
            const matchesStatus = !fStatus || (i.status||'').toLowerCase() === fStatus;
            const matchesJurusan = !fJurusan || (i.jurusan||'').toLowerCase() === fJurusan;
            return matchesSearch && matchesStatus && matchesJurusan;
        });
        page = 1; render();
    }

    function render() {
        const start = (page-1)*ITEMS_PER_PAGE, data = filtered.slice(start, start+ITEMS_PER_PAGE), tp = Math.ceil(filtered.length/ITEMS_PER_PAGE);
        const empty = `<tr><td colspan="5" class="py-14 text-center"><i class="fas fa-history text-3xl text-gray-300 block mb-2"></i><span class="text-gray-400 text-sm">Belum ada riwayat verifikasi.</span></td></tr>`;

        $id('table-body').innerHTML = data.length===0 ? empty : data.map((it,idx) => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-600">${start+idx+1}.</td>
                <td class="px-6 py-4 text-sm">
                    <div class="font-semibold text-gray-900">${esc(it.nama||'Tanpa Judul')}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        <span class="font-medium text-indigo-600">${esc(it.pengusul||'')} (${esc(it.nim||'-')})</span>
                        <span class="mx-1.5 text-gray-300">&bull;</span>
                        <span class="text-gray-400">${esc(it.jurusan||'-')}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-indigo-400 mr-1 text-xs"></i>${fmt(it.tanggal_verifikasi)}</td>
                <td class="px-6 py-4">${badge(it.status)}</td>
                <td class="px-6 py-4">
                    <a href="/verifikator/telaah/show/${it.id||0}" class="inline-flex items-center gap-1 bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-indigo-700 shadow-sm">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                </td>
            </tr>`).join('');

        $id('mobile-list').innerHTML = data.length===0 ?
            `<div class="text-center py-12 text-gray-400"><i class="fas fa-history text-3xl mb-2 block"></i>Belum ada data.</div>` :
            data.map((it,idx) => `
            <div class="bg-white border border-l-4 border-l-indigo-500 border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-100">
                    <span class="bg-indigo-600 text-white text-xs font-bold px-2 py-0.5 rounded">#${start+idx+1}</span>
                    ${badge(it.status)}
                </div>
                <p class="font-semibold text-gray-900 text-sm">${esc(it.nama||'Tanpa Judul')}</p>
                <p class="text-xs text-gray-500 mt-1">${esc(it.pengusul||'')} &bull; ${esc(it.jurusan||'')}</p>
                <p class="text-[10px] text-gray-400 mt-1">${fmt(it.tanggal_verifikasi)}</p>
                <a href="/verifikator/telaah/show/${it.id||0}" class="mt-3 w-full flex items-center justify-center gap-1 bg-indigo-600 text-white py-2 rounded-lg text-xs font-semibold hover:bg-indigo-700">
                    <i class="fas fa-eye"></i> Lihat Detail
                </a>
            </div>`).join('');

        $id('showing').textContent = filtered.length===0 ? 0 : data.length;
        $id('total').textContent = filtered.length;

        const pg = $id('pagination');
        if (tp<=1) { pg.innerHTML=''; return; }
        let h = `<button class="px-3 py-1.5 rounded border text-xs ${page===1?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="goPage(${page-1})" ${page===1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
        for (let i=1;i<=tp;i++) {
            if(i===1||i===tp||(i>=page-1&&i<=page+1)) h+=`<button class="px-3 py-1.5 rounded border text-xs font-medium ${i===page?'bg-indigo-600 text-white':'hover:bg-gray-100'}" onclick="goPage(${i})">${i}</button>`;
            else if(i===page-2||i===page+2) h+=`<span class="px-1 text-gray-400 self-center text-xs">...</span>`;
        }
        h+=`<button class="px-3 py-1.5 rounded border text-xs ${page===tp?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="goPage(${page+1})" ${page===tp?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
        pg.innerHTML=h;
    }

    window.setStatus = s => {
        currentStatus = s;
        document.querySelectorAll('.status-pill').forEach(p => {
            if(p.getAttribute('data-status') === s) p.classList.add('active-pill');
            else p.classList.remove('active-pill');
        });
        apply();
    };

    let t; 
    $id('search-input')?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(apply,300);});
    $id('jurusan-filter')?.addEventListener('change', apply);

    render();
});
</script>
@endpush
