@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-green-700 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <p class="text-red-700 font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <section class="bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-6 flex flex-col">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 pb-5 border-b border-gray-200 gap-3">
            <div class="flex-shrink-0">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Antrian LPJ</h2>
                <p class="text-sm text-gray-500 mt-1 hidden md:block">Daftar Laporan Pertanggungjawaban yang perlu diverifikasi.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 flex-wrap">
                <div class="relative">
                    <i class="fas fa-graduation-cap absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs"></i>
                    <select id="filter-jurusan" class="pl-9 pr-9 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none cursor-pointer">
                        <option value="">Semua Jurusan</option>
                        @foreach(['Teknik Informatika dan Komputer', 'Teknik Grafika dan Penerbitan', 'Teknik Elektro', 'Administrasi Niaga', 'Akuntansi', 'Teknik Mesin'] as $j)
                        <option value="{{ strtolower($j) }}">{{ $j }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                </div>
                <div class="relative">
                    <i class="fas fa-filter absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs"></i>
                    <select id="filter-status" class="pl-9 pr-9 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="menunggu_upload">Perlu Upload</option>
                        <option value="siap_submit">Siap Submit</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="revisi">Revisi</option>
                        <option value="setuju">Setuju</option>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                </div>
                <button id="reset-filter" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors flex items-center gap-2">
                    <i class="fas fa-redo text-xs"></i> Reset
                </button>
            </div>
        </div>

        {{-- Search --}}
        <div class="relative mb-4">
            <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 z-10 text-sm"></i>
            <input type="text" id="search-lpj-input" placeholder="Cari Kegiatan atau Mahasiswa..."
                   class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-visible">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tenggat LPJ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body-desktop" class="divide-y divide-gray-100 bg-white"></tbody>
                </table>
            </div>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden overflow-y-visible">
            <div id="mobile-lpj-list" class="space-y-3 pt-2"></div>
        </div>

        {{-- Pagination --}}
        <div class="p-3 sm:p-4 mt-4 border-t border-gray-200 bg-gray-50 rounded-lg">
            <div class="flex flex-col gap-3">
                <div id="pagination-buttons" class="flex gap-1 flex-wrap justify-center"></div>
                <div class="text-xs sm:text-sm text-gray-600 text-center">
                    Menampilkan <span id="showing-start" class="font-semibold text-gray-800">0</span> s.d.
                    <span id="showing-end" class="font-semibold text-gray-800">0</span> dari
                    <span id="total-records" class="font-semibold text-gray-800">0</span> data
                </div>
            </div>
        </div>

    </section>
</main>
@endsection

@push('scripts')
<style>
    .status-upload { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; border: 1px solid #fcd34d; }
    .status-wait { background: linear-gradient(135deg, #f3f4f6, #e5e7eb); color: #4b5563; border: 1px solid #d1d5db; }
    .status-rev { background: linear-gradient(135deg, #fef9c3, #fcd34d); color: #78350f; border: 1px solid #f59e0b; }
    .status-ok { background: linear-gradient(135deg, #d1fae5, #6ee7b7); color: #065f46; border: 1px solid #34d399; }
</style>
<script>
window.lpjData = @json($list_lpj ?? []);

document.addEventListener('DOMContentLoaded', () => {
    const allData = window.lpjData || [];
    const ROWS_PER_PAGE = 10;
    let filteredData = [...allData];
    let currentPage = 1;

    const searchInput = document.getElementById('search-lpj-input');
    const filterJurusan = document.getElementById('filter-jurusan');
    const filterStatus = document.getElementById('filter-status');
    const resetButton = document.getElementById('reset-filter');
    const tableBody = document.getElementById('table-body-desktop');
    const mobileList = document.getElementById('mobile-lpj-list');
    const paginationButtons = document.getElementById('pagination-buttons');

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function formatDate(d) {
        if (!d) return '-';
        return new Date(d).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
    }

    function getStatusInfo(s) {
        const m = {
            'setuju': { text: 'Disetujui', cls: 'status-ok', icon: 'fa-check-circle' },
            'disetujui': { text: 'Disetujui', cls: 'status-ok', icon: 'fa-check-circle' },
            'selesai': { text: 'Selesai', cls: 'status-ok', icon: 'fa-check-circle' },
            'revisi': { text: 'Revisi', cls: 'status-rev', icon: 'fa-exclamation-triangle' },
            'telah_direvisi': { text: 'Telah Direvisi', cls: 'text-cyan-800 bg-cyan-100 border border-cyan-200', icon: 'fa-history' },
            'menunggu_upload': { text: 'Perlu Upload', cls: 'status-upload', icon: 'fa-upload' },
            'siap_submit': { text: 'Siap Submit', cls: 'text-blue-700 bg-blue-100 border border-blue-200', icon: 'fa-file-upload' },
            'menunggu': { text: 'Menunggu', cls: 'status-wait', icon: 'fa-hourglass-half' },
        };
        return m[s] || { text: 'Tidak Diketahui', cls: 'text-red-800 bg-red-100 border border-red-200', icon: 'fa-question-circle' };
    }

    function getBtnInfo(s) {
        const m = {
            'menunggu_upload': { text: 'Upload Bukti', cls: 'bg-orange-600 hover:bg-orange-700', icon: 'fa-upload' },
            'siap_submit': { text: 'Submit LPJ', cls: 'bg-blue-600 hover:bg-blue-700', icon: 'fa-file-upload' },
            'menunggu': { text: 'Lihat Status', cls: 'bg-gray-600 hover:bg-gray-700', icon: 'fa-eye' },
            'setuju': { text: 'Lihat Detail', cls: 'bg-green-600 hover:bg-green-700', icon: 'fa-eye' },
            'disetujui': { text: 'Lihat Detail', cls: 'bg-green-600 hover:bg-green-700', icon: 'fa-eye' },
            'selesai': { text: 'Lihat Detail', cls: 'bg-green-600 hover:bg-green-700', icon: 'fa-eye' },
            'revisi': { text: 'Lihat Revisi', cls: 'bg-yellow-600 hover:bg-yellow-700', icon: 'fa-eye' },
            'telah_direvisi': { text: 'Cek Revisi', cls: 'bg-cyan-600 hover:bg-cyan-700', icon: 'fa-check-double' },
        };
        return m[s] || { text: 'Review', cls: 'bg-indigo-600 hover:bg-indigo-700', icon: 'fa-eye' };
    }

    function getDeadlineHtml(item, status_raw) {
        const d = item.tenggatLpj;
        if (status_raw === 'menunggu_upload' && d) {
            const diff = Math.ceil((new Date(d) - new Date()) / 86400000);
            let cls, icon, txt;
            if (diff < 0) { cls = 'bg-red-50 border-red-200 text-red-700'; icon = 'fa-exclamation-triangle'; txt = `Terlewat ${Math.abs(diff)} hari`; }
            else if (diff === 0) { cls = 'bg-red-50 border-red-200 text-red-700'; icon = 'fa-exclamation-circle'; txt = 'Hari Ini!'; }
            else if (diff <= 3) { cls = 'bg-orange-50 border-orange-200 text-orange-700'; icon = 'fa-hourglass-end'; txt = `Sisa ${diff} hari`; }
            else { cls = 'bg-blue-50 border-blue-200 text-blue-700'; icon = 'fa-calendar-day'; txt = `Sisa ${diff} hari`; }
            return `<div class="flex flex-col gap-1"><span class="text-gray-900 font-medium">${formatDate(d)}</span><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border ${cls}"><i class="fas ${icon}"></i> ${txt}</span></div>`;
        } else if (d) {
            return `<span class="text-gray-900 font-medium">${formatDate(d)}</span>`;
        } else if (status_raw === 'menunggu_upload') {
            return `<span class="px-2 py-0.5 rounded-full text-orange-700 bg-orange-100 border border-orange-200 text-xs font-medium">Belum Ditetapkan</span>`;
        }
        return `<span class="text-gray-600">-</span>`;
    }

    function applyFilters() {
        const s = searchInput?.value.toLowerCase().trim() || '';
        const j = filterJurusan?.value.toLowerCase() || '';
        const st = filterStatus?.value.toLowerCase() || '';
        filteredData = allData.filter(item => {
            const srch = ((item.nama || '') + ' ' + (item.nama_mahasiswa || '') + ' ' + (item.prodi || '')).toLowerCase();
            return (!s || srch.includes(s)) && (!j || (item.jurusan || '').toLowerCase() === j) && (!st || (item.status || '').toLowerCase() === st);
        });
        currentPage = 1;
        render();
    }

    function render() {
        const start = (currentPage - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = filteredData.slice(start, end);
        const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
        const emptyMsg = allData.length === 0 ? 'Belum ada data pengajuan LPJ.' : 'Data tidak ditemukan.';

        if (tableBody) {
            if (pageData.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6" class="px-6 py-16 text-center text-gray-500"><i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>${emptyMsg}</td></tr>`;
            } else {
                tableBody.innerHTML = pageData.map((item, idx) => {
                    const no = start + idx + 1;
                    const sr = (item.status || 'menunggu').toLowerCase();
                    const si = getStatusInfo(sr);
                    const bi = getBtnInfo(sr);
                    return `<tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 text-sm text-gray-700">${no}.</td>
                        <td class="px-6 py-5 text-sm">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900">${escapeHtml(item.nama || 'Tanpa Judul')}</span>
                                <span class="text-gray-500 text-xs mt-0.5"><i class="fas fa-user mr-1"></i>${escapeHtml(item.nama_mahasiswa || 'N/A')} (${escapeHtml(item.nim || '-')})</span>
                                <span class="text-gray-400 text-xs"><i class="fas fa-graduation-cap mr-1 text-blue-400"></i>${escapeHtml(item.prodi || item.jurusan || '-')}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-gray-400 mr-1"></i>${formatDate(item.tanggal_pengajuan)}</td>
                        <td class="px-6 py-5 text-sm">${getDeadlineHtml(item, sr)}</td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="px-3 py-1.5 rounded-full inline-flex items-center gap-1.5 text-xs font-semibold ${si.cls}"><i class="fas ${si.icon}"></i>${si.text}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <a href="/admin/pengajuan-lpj/show/${item.id || 0}?from=index" class="${bi.cls} text-white px-4 py-1.5 rounded-lg text-xs font-semibold shadow-md transition-all inline-flex items-center gap-2">
                                <i class="fas ${bi.icon}"></i> ${bi.text}
                            </a>
                        </td>
                    </tr>`;
                }).join('');
            }
        }

        if (mobileList) {
            if (pageData.length === 0) {
                mobileList.innerHTML = `<div class="text-center py-16 text-gray-500"><i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>${emptyMsg}</div>`;
            } else {
                mobileList.innerHTML = pageData.map((item, idx) => {
                    const no = start + idx + 1;
                    const sr = (item.status || 'menunggu').toLowerCase();
                    const si = getStatusInfo(sr);
                    const bi = getBtnInfo(sr);
                    return `<div class="bg-white border border-gray-200 border-l-4 border-l-blue-500 rounded-xl p-4 shadow-sm">
                        <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-100">
                            <span class="bg-blue-600 text-white text-xs font-bold px-2.5 py-1 rounded-lg">#${no}</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold ${si.cls}"><i class="fas ${si.icon} mr-1"></i>${si.text}</span>
                        </div>
                        <p class="font-semibold text-gray-900 mb-1">${escapeHtml(item.nama || 'Tanpa Judul')}</p>
                        <p class="text-xs text-gray-500 mb-1">${escapeHtml(item.nama_mahasiswa || 'N/A')} &bull; ${escapeHtml(item.jurusan || '-')}</p>
                        <p class="text-xs text-gray-400 mb-3"><i class="fas fa-calendar-alt mr-1"></i>${formatDate(item.tanggal_pengajuan)}</p>
                        <a href="/admin/pengajuan-lpj/show/${item.id || 0}?from=index" class="w-full flex items-center justify-center gap-2 ${bi.cls} text-white py-2.5 rounded-lg text-sm font-semibold transition-colors">
                            <i class="fas ${bi.icon}"></i> ${bi.text}
                        </a>
                    </div>`;
                }).join('');
            }
        }

        const tot = filteredData.length;
        document.getElementById('showing-start').textContent = tot === 0 ? 0 : start + 1;
        document.getElementById('showing-end').textContent = Math.min(end, tot);
        document.getElementById('total-records').textContent = tot;
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (!paginationButtons) return;
        if (totalPages <= 1) { paginationButtons.innerHTML = ''; return; }
        let html = `<button class="px-3 py-1.5 rounded-lg border text-sm font-medium ${currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100'}" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>`;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `<button class="px-3 py-1.5 rounded-lg border text-sm font-medium ${i === currentPage ? 'bg-blue-600 text-white border-transparent' : 'hover:bg-gray-100'}" onclick="goToPage(${i})">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span class="px-2 text-gray-400 self-center">...</span>`;
            }
        }
        html += `<button class="px-3 py-1.5 rounded-lg border text-sm font-medium ${currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100'}" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>`;
        paginationButtons.innerHTML = html;
    }

    window.goToPage = function(page) {
        const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
        if (page >= 1 && page <= totalPages && page !== currentPage) { currentPage = page; render(); }
    };

    if (searchInput) { let t; searchInput.addEventListener('input', () => { clearTimeout(t); t = setTimeout(applyFilters, 300); }); }
    if (filterJurusan) filterJurusan.addEventListener('change', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);
    if (resetButton) resetButton.addEventListener('click', () => {
        if (searchInput) searchInput.value = '';
        if (filterJurusan) filterJurusan.value = '';
        if (filterStatus) filterStatus.value = '';
        applyFilters();
    });

    render();
});
</script>
@endpush
