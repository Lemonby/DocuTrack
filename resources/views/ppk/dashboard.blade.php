@extends('layouts.app')

@section('title', 'Dashboard PPK')

@section('content')
<main class="main-content font-poppins p-3 sm:p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
        <div class="lg:col-span-12 grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Total Usulan --}}
            <div class="relative group p-6 rounded-2xl shadow-sm overflow-hidden text-white bg-gradient-to-br from-blue-500 to-blue-600 hover:shadow-blue-200/50 hover:-translate-y-1 transition-all duration-300">
                <div class="relative z-10 flex flex-col justify-between h-full">
                    <div class="p-3 w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                        <i class="fas fa-layer-group text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-4xl font-black mb-1 leading-none">{{ $stats['total'] }}</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Total Usulan</p>
                    </div>
                </div>
            </div>

            {{-- Disetujui --}}
            <div class="relative group p-6 rounded-2xl shadow-sm overflow-hidden text-white bg-gradient-to-br from-emerald-500 to-emerald-600 hover:shadow-emerald-200/50 hover:-translate-y-1 transition-all duration-300">
                <div class="relative z-10 flex flex-col justify-between h-full">
                    <div class="p-3 w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-4xl font-black mb-1 leading-none">{{ $stats['disetujui'] }}</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Disetujui</p>
                    </div>
                </div>
            </div>

            {{-- Menunggu --}}
            <div class="relative group p-6 rounded-2xl shadow-sm overflow-hidden text-slate-800 bg-amber-300 hover:shadow-amber-200/50 hover:-translate-y-1 transition-all duration-300">
                <div class="relative z-10 flex flex-col justify-between h-full">
                    <div class="p-3 w-12 h-12 rounded-xl bg-black/5 flex items-center justify-center mb-4">
                        <i class="fas fa-hourglass-half text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-4xl font-black mb-1 leading-none">{{ $stats['menunggu'] }}</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-70">Menunggu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Section --}}
    <section class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8 border border-slate-100">
        <div class="p-5 border-b border-slate-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-600"></i> Antrian Persetujuan
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-1 italic">Verifikasi usulan kegiatan yang masuk</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="relative group">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                    <input type="text" id="search-input" placeholder="Cari kegiatan..."
                           class="pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <select id="filter-status" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-600 outline-none focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer">
                    <option value="">Status</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="disetujui">Disetujui</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">No</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Kegiatan & Pengusul</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tgl. Masuk</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-slate-100"></tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-50 bg-slate-50/20">
            <div class="flex items-center justify-between">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <span id="showing" class="text-blue-600">0</span> / <span id="total" class="text-slate-800">0</span> Data
                </div>
                <div id="pagination" class="flex gap-1"></div>
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

    function esc(s) { if (!s) return ''; const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function fmt(s) { return s ? new Date(s).toLocaleDateString('id-ID',{day:'2-digit',month:'short',year:'numeric'}) : '-'; }
    
    function badge(status) {
        const m = {
            'disetujui': 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'menunggu':  'bg-blue-100 text-blue-700 border-blue-200',
        };
        const cls = m[(status||'').toLowerCase()] || 'bg-slate-100 text-slate-500 border-slate-200';
        return `<span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border ${cls}">${esc(status||'Menunggu')}</span>`;
    }

    function apply() {
        const s = ($id('search-input')?.value||'').toLowerCase();
        const st = ($id('filter-status')?.value||'').toLowerCase();
        filtered = all.filter(i => {
            const txt = ((i.nama||'')+(i.pengusul||'')+(i.nim||'')).toLowerCase();
            const istatus = (i.status||'menunggu').toLowerCase();
            return (!s||txt.includes(s)) && (!st||istatus===st);
        });
        page = 1; render();
    }

    function render() {
        const start = (page-1)*ROWS, data = filtered.slice(start, start+ROWS), tp = Math.ceil(filtered.length/ROWS);
        const empty = `<tr><td colspan="5" class="py-14 text-center text-[10px] font-black text-slate-300 uppercase tracking-widest">Tidak ada antrian</td></tr>`;

        $id('table-body').innerHTML = data.length===0 ? empty : data.map((it,idx) => `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-5 text-xs font-black text-slate-400">${start+idx+1}.</td>
                <td class="px-6 py-5">
                    <div class="text-xs font-black text-slate-800 uppercase tracking-tight">${esc(it.nama||'Tanpa Judul')}</div>
                    <div class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-widest">${esc(it.pengusul||'')} &bull; ${esc(it.prodi||'-')}</div>
                </td>
                <td class="px-6 py-5 text-[10px] font-bold text-slate-500 uppercase"><i class="far fa-calendar-alt text-blue-400 mr-2"></i>${fmt(it.tanggal_pengajuan)}</td>
                <td class="px-6 py-5 text-center">${badge(it.status)}</td>
                <td class="px-6 py-5 text-center">
                    <a href="/ppk/kegiatan/show/${it.id||0}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-sm active:scale-95">
                        <i class="fas fa-eye text-[8px]"></i> Detail
                    </a>
                </td>
            </tr>`).join('');

        $id('showing').textContent = filtered.length===0 ? 0 : data.length;
        $id('total').textContent = filtered.length;

        const pg = $id('pagination');
        if (tp<=1) { pg.innerHTML=''; return; }
        let h = `<button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 ${page===1?'opacity-40 cursor-not-allowed':'hover:bg-slate-100 transition-colors'}" onclick="goPage(${page-1})" ${page===1?'disabled':''}><i class="fas fa-chevron-left text-[10px]"></i></button>`;
        for (let i=1;i<=tp;i++) {
            if(i===1||i===tp||(i>=page-1&&i<=page+1)) h+=`<button class="w-8 h-8 flex items-center justify-center rounded-lg border ${i===page?'bg-blue-600 text-white border-blue-600 font-black shadow-sm':'border-slate-200 text-slate-600 hover:bg-slate-100'} text-[10px] transition-colors" onclick="goPage(${i})">${i}</button>`;
            else if(i===page-2||i===page+2) h+=`<span class="w-6 flex items-center justify-center text-slate-300 font-bold text-xs">...</span>`;
        }
        h+=`<button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 ${page===tp?'opacity-40 cursor-not-allowed':'hover:bg-slate-100 transition-colors'}" onclick="goPage(${page+1})" ${page===tp?'disabled':''}><i class="fas fa-chevron-right text-[10px]"></i></button>`;
        pg.innerHTML=h;
    }

    window.goPage = p => { const tp=Math.ceil(filtered.length/ROWS); if(p>=1&&p<=tp&&p!==page){page=p;render();} };

    let t; $id('search-input')?.addEventListener('input',()=>{clearTimeout(t);t=setTimeout(apply,300);});
    $id('filter-status')?.addEventListener('change', apply);
    render();
});
</script>
@endpush
