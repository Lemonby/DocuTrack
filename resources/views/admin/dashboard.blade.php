@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-3 sm:p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Statistics Cards -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="relative group p-6 rounded-2xl shadow-sm overflow-hidden text-white bg-gradient-to-br from-blue-500 to-blue-600 hover:shadow-blue-200/50 hover:-translate-y-1 transition-all duration-300">
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="p-3 w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                    <i class="fas fa-layer-group text-xl"></i>
                </div>
                <div>
                    <h3 class="text-4xl font-black mb-1">{{ $stats['total'] ?? 0 }}</h3>
                    <p class="text-xs font-bold uppercase tracking-widest opacity-80">Total Usulan</p>
                </div>
            </div>
        </div>

        <div class="relative group p-6 rounded-2xl shadow-sm overflow-hidden text-white bg-gradient-to-br from-emerald-500 to-emerald-600 hover:shadow-emerald-200/50 hover:-translate-y-1 transition-all duration-300">
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="p-3 w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <h3 class="text-4xl font-black mb-1">{{ $stats['disetujui'] ?? 0 }}</h3>
                    <p class="text-xs font-bold uppercase tracking-widest opacity-80">Disetujui</p>
                </div>
            </div>
        </div>

        <div class="relative group p-6 rounded-2xl shadow-sm overflow-hidden text-white bg-gradient-to-br from-rose-500 to-rose-600 hover:shadow-rose-200/50 hover:-translate-y-1 transition-all duration-300">
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="p-3 w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4">
                    <i class="fas fa-times-circle text-xl"></i>
                </div>
                <div>
                    <h3 class="text-4xl font-black mb-1">{{ $stats['ditolak'] ?? 0 }}</h3>
                    <p class="text-xs font-bold uppercase tracking-widest opacity-80">Ditolak</p>
                </div>
            </div>
        </div>

        <div class="relative group p-6 rounded-2xl shadow-sm overflow-hidden text-slate-800 bg-amber-300 hover:shadow-amber-200/50 hover:-translate-y-1 transition-all duration-300">
            <div class="relative z-10 flex flex-col justify-between h-full">
                <div class="p-3 w-12 h-12 rounded-xl bg-black/5 flex items-center justify-center mb-4">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
                <div>
                    <h3 class="text-4xl font-black mb-1">{{ $stats['menunggu'] ?? 0 }}</h3>
                    <p class="text-xs font-bold uppercase tracking-widest opacity-70">Menunggu</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Progress Workflow Sections -->

    <!-- Progress Workflow Sections -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 md:gap-8 mb-6 md:mb-8">
        <!-- Alur KAK -->
        <section class="bg-white p-4 sm:p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100"> 
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 md:mb-8 gap-2">
                <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-5 sm:h-6 bg-blue-500 rounded-full"></span>
                    Alur KAK Saat Ini
                </h3>
                <span class="text-[10px] sm:text-xs font-medium px-2 py-1 bg-blue-50 text-blue-600 rounded-md border border-blue-100">Live Status</span>
            </div>
            
            <div class="relative px-1 sm:px-2 pt-2 pb-8 sm:pb-10"> 
                @php
                    $posisi_sekarang_kak = array_search($tahap_sekarang_kak, $tahapan_kak);
                    if ($posisi_sekarang_kak === false) {
                        $posisi_sekarang_kak = 0;
                    }
                    $total_langkah_kak = count($tahapan_kak) - 1;
                    $lebar_progress_kak = $total_langkah_kak > 0 ? ($posisi_sekarang_kak / $total_langkah_kak) * 100 : 0;
                @endphp
                
                <!-- Progress Bar Container -->
                <div class="absolute top-[16px] sm:top-[20px] md:top-[22px] left-0 right-0 h-1 sm:h-1.5 z-0">
                    <!-- Background Bar -->
                    <div class="absolute inset-0 bg-gray-200 rounded-full"></div>
                    <!-- Progress Fill -->
                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(59,130,246,0.4)]" 
                         style="width: {{ $lebar_progress_kak }}%;"></div>
                </div> 

                <!-- Progress Steps -->
                <div class="relative z-10 flex justify-between w-full">
                    @foreach ($tahapan_kak as $index => $nama_tahap)
                        @php
                        $is_completed = $index < $posisi_sekarang_kak;
                        $is_active = $index == $posisi_sekarang_kak;

                        if ($is_active) {
                            $circle_class = 'bg-blue-500 border-blue-500 text-white shadow-lg ring-2 sm:ring-4 ring-blue-100 scale-110';
                            $text_class = 'text-blue-700 font-bold';
                        } elseif ($is_completed) {
                            $circle_class = 'bg-blue-500 border-blue-500 text-white shadow-md';
                            $text_class = 'text-blue-600 font-medium';
                        } else {
                            $circle_class = 'bg-white border-2 border-gray-300 text-gray-400';
                            $text_class = 'text-gray-400';
                        }
                        @endphp
                    <div class="flex flex-col items-center group transition-transform hover:-translate-y-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center transition-all duration-300 {{ $circle_class }}"> 
                            <i class="fas {{ $icons_kak[$nama_tahap] ?? 'fa-circle' }} text-xs sm:text-sm"></i> 
                        </div>
                        <span class="mt-2 sm:mt-3 md:mt-4 text-[8px] sm:text-[10px] md:text-xs text-center max-w-[60px] sm:max-w-[70px] md:max-w-[80px] leading-tight {{ $text_class }}">
                            {{ $nama_tahap }}
                        </span> 
                    </div>
                    @endforeach
                </div>
            </div> 
        </section>

        <!-- Alur LPJ -->
        <section class="bg-white p-4 sm:p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 md:mb-8 gap-2">
                <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-5 sm:h-6 bg-green-500 rounded-full"></span>
                    Alur LPJ Saat Ini
                </h3>
                <span class="text-[10px] sm:text-xs font-medium px-2 py-1 bg-green-50 text-green-600 rounded-md border border-green-100">Live Status</span>
            </div>

            <div class="relative px-1 sm:px-2 pb-6 sm:pb-8"> 
                @php
                    $posisi_sekarang_lpj = array_search($tahap_sekarang_lpj, $tahapan_lpj);
                    if ($posisi_sekarang_lpj === false) {
                        $posisi_sekarang_lpj = 0;
                    }
                    $total_langkah_lpj = count($tahapan_lpj) - 1;
                    $lebar_progress_lpj = $total_langkah_lpj > 0 ? ($posisi_sekarang_lpj / $total_langkah_lpj) * 100 : 0;
                @endphp
                
                <!-- Progress Bar Container -->
                <div class="absolute top-[16px] sm:top-[20px] md:top-[22px] left-0 right-0 h-1 sm:h-1.5 z-0">
                    <!-- Background Bar -->
                    <div class="absolute inset-0 bg-gray-200 rounded-full"></div>
                    <!-- Progress Fill -->
                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-green-500 to-green-600 rounded-full transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(34,197,94,0.4)]" 
                         style="width: {{ $lebar_progress_lpj }}%;"></div>
                </div> 
 
                <!-- Progress Steps -->
                <div class="relative z-10 flex justify-between w-full">
                    @foreach ($tahapan_lpj as $index => $nama_tahap)
                        @php
                        $is_completed = $index < $posisi_sekarang_lpj;
                        $is_active = $index == $posisi_sekarang_lpj;

                        if ($is_active) {
                            $circle_class = 'bg-green-500 border-green-500 text-white shadow-lg ring-2 sm:ring-4 ring-green-100 scale-110';
                            $text_class = 'text-green-700 font-bold';
                        } elseif ($is_completed) {
                            $circle_class = 'bg-green-500 border-green-500 text-white shadow-md';
                            $text_class = 'text-green-600 font-medium';
                        } else {
                            $circle_class = 'bg-white border-2 border-gray-300 text-gray-400';
                            $text_class = 'text-gray-400';
                        }
                        @endphp
                    <div class="flex flex-col items-center group transition-transform hover:-translate-y-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center transition-all duration-300 {{ $circle_class }}"> 
                            <i class="fas {{ $icons_lpj[$nama_tahap] ?? 'fa-circle' }} text-xs sm:text-sm"></i> 
                        </div>
                        <span class="mt-2 sm:mt-3 md:mt-4 text-[8px] sm:text-[10px] md:text-xs text-center max-w-[60px] sm:max-w-[70px] md:max-w-[80px] leading-tight {{ $text_class }}">
                            {{ $nama_tahap }}
                        </span> 
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>

    {{-- Table KAK --}}
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 md:mb-8 flex flex-col">
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-blue-600"></i> List Pengajuan KAK
                </h3>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filter-status-kak" class="pl-9 pr-8 py-2 border border-gray-300 rounded-lg text-xs text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                            <option value="revisi">Revisi</option>
                            <option value="menunggu">Menunggu</option>
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filter-jurusan-kak" class="pl-9 pr-8 py-2 border border-gray-300 rounded-lg text-xs text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white appearance-none cursor-pointer">
                            <option value="">Semua Jurusan</option>
                            <option value="Teknik Informatika dan Komputer">Teknik Informatika dan Komputer</option>
                            <option value="Teknik Grafika dan Penerbitan">Teknik Grafika dan Penerbitan</option>
                            <option value="Teknik Elektro">Teknik Elektro</option>
                            <option value="Administrasi Niaga">Administrasi Niaga</option>
                            <option value="Akuntansi">Akuntansi</option>
                            <option value="Teknik Mesin">Teknik Mesin</option>
                        </select>
                    </div>
                    <button id="reset-filter-kak" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 transition-colors flex items-center gap-1">
                        <i class="fas fa-redo text-xs"></i> Reset
                    </button>
                </div>
            </div>
            <div class="mt-3 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-kak" placeholder="Cari nama kegiatan..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto overflow-y-auto" style="max-height:400px;">
            <table class="w-full min-w-[800px]" id="table-kak">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
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

    {{-- Table LPJ --}}
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 md:mb-8 flex flex-col">
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-green-600"></i> List Pengajuan LPJ
                </h3>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filter-status-lpj" class="pl-9 pr-8 py-2 border border-gray-300 rounded-lg text-xs text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="menunggu_upload">Perlu Upload</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="revisi">Revisi</option>
                            <option value="setuju">Setuju</option>
                        </select>
                    </div>
                    <div class="relative">
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        <select id="filter-jurusan-lpj" class="pl-9 pr-8 py-2 border border-gray-300 rounded-lg text-xs text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white appearance-none cursor-pointer">
                            <option value="">Semua Jurusan</option>
                            <option value="Teknik Informatika dan Komputer">Teknik Informatika dan Komputer</option>
                            <option value="Teknik Grafika dan Penerbitan">Teknik Grafika dan Penerbitan</option>
                            <option value="Teknik Elektro">Teknik Elektro</option>
                            <option value="Administrasi Niaga">Administrasi Niaga</option>
                            <option value="Akuntansi">Akuntansi</option>
                            <option value="Teknik Mesin">Teknik Mesin</option>
                        </select>
                    </div>
                    <button id="reset-filter-lpj" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-200 transition-colors flex items-center gap-1">
                        <i class="fas fa-redo text-xs"></i> Reset
                    </button>
                </div>
            </div>
            <div class="mt-3 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-lpj" placeholder="Cari nama kegiatan..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all">
            </div>
        </div>

        <div class="hidden md:block overflow-x-auto overflow-y-auto" style="max-height:400px;">
            <table class="w-full min-w-[900px]" id="table-lpj">
                <thead class="bg-gradient-to-r from-green-50 to-emerald-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tenggat LPJ</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
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
@endsection

@push('scripts')
<script>
window.dataKAK = @json($list_kak ?? []);
window.dataLPJ = @json($list_lpj ?? []);

(function() {
    const ROWS = 5;

    function escHtml(s) {
        if (!s) return '';
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function fmtDate(s) {
        if (!s) return '-';
        const dateStr = typeof s === 'string' ? s.split('T')[0].replace(/-/g, '/') : s;
        return new Date(dateStr).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
    }

    function statusBadge(status) {
        const s = (status || '').toLowerCase().replace(/_/g, ' ');
        let cls = 'bg-slate-100 text-slate-500 border-slate-200';
        
        if (['disetujui', 'setuju', 'selesai', 'tuntas', 'lpj disetujui', 'dana diberikan', 'dana diberikan sebagian'].includes(s)) {
            cls = 'bg-emerald-100 text-emerald-700 border-emerald-200';
        } else if (['revisi', 'perlu perbaikan'].includes(s)) {
            cls = 'bg-amber-100 text-amber-700 border-amber-200';
        } else if (['telah direvisi', 'telah diverifikasi'].includes(s)) {
            cls = 'bg-purple-100 text-purple-700 border-purple-200';
        } else if (['menunggu', 'review', 'pending', 'sedang diproses', 'menunggu verifikasi', 'siap submit', 'menunggu upload'].includes(s)) {
            cls = 'bg-blue-100 text-blue-700 border-blue-200';
        } else if (['ditolak', 'tolak', 'batal'].includes(s)) {
            cls = 'bg-rose-100 text-rose-700 border-rose-200';
        }

        return `<span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border ${cls}">${escHtml(status || '-').replace(/_/g, ' ')}</span>`;
    }

    function initTable({ allData, rowsPerPage, searchId, filterStatusId, filterJurusanId, resetId, tbodyId, cardsId, showingId, totalId, paginationId, buildRow, buildCard, actionUrl }) {
        let filtered = [...allData];
        let page = 1;

        const search = document.getElementById(searchId);
        const fStatus = document.getElementById(filterStatusId);
        const fJurusan = document.getElementById(filterJurusanId);
        const resetBtn = document.getElementById(resetId);
        const tbody = document.getElementById(tbodyId);
        const cards = document.getElementById(cardsId);
        const showing = document.getElementById(showingId);
        const total = document.getElementById(totalId);
        const pagination = document.getElementById(paginationId);

        function applyFilters() {
            const s = search?.value.toLowerCase().trim() || '';
            const st = fStatus?.value.toLowerCase() || '';
            const ju = fJurusan?.value || '';
            filtered = allData.filter(item => {
                const txt = ((item.nama || '') + ' ' + (item.nama_mahasiswa || '') + ' ' + (item.jurusan || '')).toLowerCase();
                const statusMatch = !st || (item.status || '').toLowerCase() === st;
                const jurusanMatch = !ju || (item.jurusan || '') === ju;
                return (!s || txt.includes(s)) && statusMatch && jurusanMatch;
            });
            page = 1;
            render();
        }

        function render() {
            const start = (page - 1) * rowsPerPage;
            const pageData = filtered.slice(start, start + rowsPerPage);
            const totalPages = Math.ceil(filtered.length / rowsPerPage);
            const emptyMsg = `<tr><td colspan="6" class="px-6 py-14 text-center"><i class="fas fa-inbox text-3xl text-gray-300 block mb-2"></i><span class="text-gray-500 text-sm">${allData.length === 0 ? 'Belum ada data.' : 'Data tidak ditemukan.'}</span></td></tr>`;

            if (tbody) tbody.innerHTML = pageData.length === 0 ? emptyMsg : pageData.map((item, i) => buildRow(item, start + i + 1, actionUrl)).join('');
            if (cards) cards.innerHTML = pageData.length === 0
                ? `<div class="text-center py-12 text-gray-400"><i class="fas fa-inbox text-3xl mb-2 block"></i>Belum ada data.</div>`
                : pageData.map((item, i) => buildCard(item, start + i + 1, actionUrl)).join('');

            if (showing) showing.textContent = filtered.length === 0 ? 0 : start + 1;
            if (total) total.textContent = filtered.length;

            // Pagination
            if (pagination) {
                if (totalPages <= 1) { pagination.innerHTML = ''; return; }
                let html = `<button class="px-3 py-1.5 rounded border text-xs font-medium ${page===1?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="this.closest('section').goToPage(${page-1})" ${page===1?'disabled':''}><i class="fas fa-chevron-left"></i></button>`;
                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= page - 1 && i <= page + 1)) {
                        html += `<button class="px-3 py-1.5 rounded border text-xs font-medium ${i===page?'bg-blue-600 text-white border-transparent':'hover:bg-gray-100'}" onclick="this.closest('section').goToPage(${i})">${i}</button>`;
                    } else if (i === page - 2 || i === page + 2) {
                        html += `<span class="px-1 text-gray-400 self-center text-xs">...</span>`;
                    }
                }
                html += `<button class="px-3 py-1.5 rounded border text-xs font-medium ${page===totalPages?'opacity-40 cursor-not-allowed':'hover:bg-gray-100'}" onclick="this.closest('section').goToPage(${page+1})" ${page===totalPages?'disabled':''}><i class="fas fa-chevron-right"></i></button>`;
                pagination.innerHTML = html;
                pagination.closest('section').goToPage = function(p) {
                    if (p >= 1 && p <= totalPages && p !== page) { page = p; render(); }
                };
            }
        }

        if (search) { let t; search.addEventListener('input', () => { clearTimeout(t); t = setTimeout(applyFilters, 300); }); }
        if (fStatus) fStatus.addEventListener('change', applyFilters);
        if (fJurusan) fJurusan.addEventListener('change', applyFilters);
        if (resetBtn) resetBtn.addEventListener('click', () => {
            if (search) search.value = '';
            if (fStatus) fStatus.value = '';
            if (fJurusan) fJurusan.value = '';
            applyFilters();
        });

        render();
    }

    // ---- KAK Table ----
    function kakRow(item, no, url) {
        const tgl = fmtDate(item.tanggal_pengajuan);
        return `<tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-sm text-gray-600">${no}.</td>
            <td class="px-6 py-4 text-sm">
                <div class="font-semibold text-gray-900">${escHtml(item.nama || 'Tanpa Judul')}</div>
                <div class="text-xs text-gray-500 mt-0.5">${escHtml(item.nama_mahasiswa || '')} &bull; ${escHtml(item.jurusan || '-')}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-blue-400 mr-1 text-xs"></i>${tgl}</td>
            <td class="px-6 py-4">${statusBadge(item.status)}</td>
            <td class="px-6 py-4">
                <a href="${url}/${item.id || 0}?from=dashboard" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-sm active:scale-95">
                    <i class="fas fa-eye text-[8px]"></i> Detail
                </a>
            </td>
        </tr>`;
    }

    function kakCard(item, no, url) {
        const tgl = fmtDate(item.tanggal_pengajuan);
        return `<div class="bg-white border border-gray-200 border-l-4 border-l-blue-500 rounded-xl p-4 shadow-sm">
            <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-100">
                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded">#${no}</span>
                ${statusBadge(item.status)}
            </div>
            <p class="font-semibold text-gray-900 text-sm">${escHtml(item.nama || 'Tanpa Judul')}</p>
            <p class="text-xs text-gray-500 mt-1">${escHtml(item.nama_mahasiswa || '')} &bull; <i class="fas fa-calendar-alt"></i> ${tgl}</p>
            <a href="${url}/${item.id || 0}?from=dashboard" class="mt-3 w-full flex items-center justify-center gap-2 bg-blue-600 text-white py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-sm active:scale-95">
                <i class="fas fa-eye text-[8px]"></i> Detail KAK
            </a>
        </div>`;
    }

    // ---- LPJ Table ----
    function lpjRow(item, no, url) {
        const tgl = fmtDate(item.tanggal_pengajuan);
        const tenggat = fmtDate(item.tenggatLpj);
        return `<tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4 text-sm text-gray-600">${no}.</td>
            <td class="px-6 py-4 text-sm">
                <div class="font-semibold text-gray-900">${escHtml(item.nama || 'Tanpa Judul')}</div>
                <div class="text-xs text-gray-500 mt-0.5">${escHtml(item.nama_mahasiswa || '')} &bull; ${escHtml(item.jurusan || '-')}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-gray-400 mr-1 text-xs"></i>${tgl}</td>
            <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">${item.tenggatLpj ? `<i class="fas fa-clock text-green-500 mr-1 text-xs"></i>${tenggat}` : '<span class="text-gray-400">-</span>'}</td>
            <td class="px-6 py-4">${statusBadge(item.status)}</td>
            <td class="px-6 py-4">
                <a href="${url}/${item.id || 0}?from=dashboard" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-sm active:scale-95">
                    <i class="fas fa-file-alt text-[8px]"></i> Rincian
                </a>
            </td>
        </tr>`;
    }

    function lpjCard(item, no, url) {
        const tgl = fmtDate(item.tanggal_pengajuan);
        return `<div class="bg-white border border-gray-200 border-l-4 border-l-emerald-500 rounded-xl p-4 shadow-sm">
            <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-100">
                <span class="bg-emerald-600 text-white text-xs font-bold px-2 py-0.5 rounded">#${no}</span>
                ${statusBadge(item.status)}
            </div>
            <p class="font-semibold text-gray-900 text-sm">${escHtml(item.nama || 'Tanpa Judul')}</p>
            <p class="text-xs text-gray-500 mt-1">${escHtml(item.nama_mahasiswa || '')} &bull; <i class="fas fa-calendar-alt"></i> ${tgl}</p>
            <a href="${url}/${item.id || 0}?from=dashboard" class="mt-3 w-full flex items-center justify-center gap-2 bg-emerald-600 text-white py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-sm active:scale-95">
                <i class="fas fa-file-alt text-[8px]"></i> Buka LPJ
            </a>
        </div>`;
    }

    document.addEventListener('DOMContentLoaded', function () {
        initTable({
            allData: window.dataKAK || [],
            rowsPerPage: ROWS,
            searchId: 'search-kak',
            filterStatusId: 'filter-status-kak',
            filterJurusanId: 'filter-jurusan-kak',
            resetId: 'reset-filter-kak',
            tbodyId: 'tbody-kak',
            cardsId: 'cards-kak',
            showingId: 'showing-kak',
            totalId: 'total-kak',
            paginationId: 'pagination-kak',
            buildRow: kakRow,
            buildCard: kakCard,
            actionUrl: '{{ url("/admin/pengajuan-usulan/show") }}'
        });

        initTable({
            allData: window.dataLPJ || [],
            rowsPerPage: ROWS,
            searchId: 'search-lpj',
            filterStatusId: 'filter-status-lpj',
            filterJurusanId: 'filter-jurusan-lpj',
            resetId: 'reset-filter-lpj',
            tbodyId: 'tbody-lpj',
            cardsId: 'cards-lpj',
            showingId: 'showing-lpj',
            totalId: 'total-lpj',
            paginationId: 'pagination-lpj',
            buildRow: lpjRow,
            buildCard: lpjCard,
            actionUrl: '{{ url("/admin/pengajuan-lpj/show") }}'
        });
    });
})();
</script>
@endpush
