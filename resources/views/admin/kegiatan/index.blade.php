@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-6 flex flex-col">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 pb-5 border-b border-gray-200 gap-3">
            <div class="flex-shrink-0">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">List Pengajuan Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1 hidden md:block">Daftar Pengajuan Kegiatan yang perlu dilengkapi.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 flex-wrap">
                <div class="relative">
                    <i class="fas fa-graduation-cap absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs"></i>
                    <select id="filter-jurusan" class="pl-9 pr-9 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none cursor-pointer">
                        <option value="">Semua Jurusan</option>
                        @foreach(['Teknik Informatika dan Komputer', 'Teknik Grafika dan Penerbitan', 'Teknik Elektro', 'Administrasi Niaga', 'Akuntansi', 'Teknik Mesin', 'Teknik Sipil'] as $jurusan)
                        <option value="{{ $jurusan }}">{{ $jurusan }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                </div>
                <div class="relative">
                    <i class="fas fa-filter absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs"></i>
                    <select id="filter-status" class="pl-9 pr-9 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="proses">Proses</option>
                        <option value="review">Review / Menunggu</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="revisi">Revisi</option>
                        <option value="ditolak">Ditolak</option>
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
            <input type="text" id="search-kegiatan-input" placeholder="Cari Kegiatan atau Mahasiswa..."
                   class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
        </div>

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-visible">
                <table class="w-full min-w-[800px]">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body-desktop" class="divide-y divide-gray-100 bg-white">
                        {{-- Populated by JS --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden overflow-y-visible">
            <div id="mobile-kegiatan-list" class="space-y-3"></div>
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
<script>
window.kegiatanData = @json($list_kegiatan ?? []);

document.addEventListener('DOMContentLoaded', () => {
    const allData = window.kegiatanData || [];
    const ROWS_PER_PAGE = 10;
    let filteredData = [...allData];
    let currentPage = 1;

    const searchInput = document.getElementById('search-kegiatan-input');
    const filterJurusan = document.getElementById('filter-jurusan');
    const filterStatus = document.getElementById('filter-status');
    const resetButton = document.getElementById('reset-filter');
    const tableBody = document.getElementById('table-body-desktop');
    const mobileList = document.getElementById('mobile-kegiatan-list');
    const paginationButtons = document.getElementById('pagination-buttons');
    const showingStart = document.getElementById('showing-start');
    const showingEnd = document.getElementById('showing-end');
    const totalRecords = document.getElementById('total-records');

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function applyFilters() {
        const s = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const j = filterJurusan ? filterJurusan.value : '';
        const statusVal = filterStatus ? filterStatus.value.toLowerCase() : '';
        filteredData = allData.filter(item => {
            const searchMatch = !s || (item.nama || '').toLowerCase().includes(s) || (item.nama_mahasiswa || '').toLowerCase().includes(s) || (item.nim || '').toLowerCase().includes(s);
            const jurusanMatch = !j || (item.jurusan || '') === j;
            
            let statusMatch = true;
            if (statusVal) {
                const itemStatus = (item.status || '').toLowerCase();
                if (statusVal === 'review' || statusVal === 'menunggu') {
                    statusMatch = ['review', 'menunggu'].includes(itemStatus);
                } else {
                    statusMatch = itemStatus === statusVal;
                }
            }
            
            return searchMatch && jurusanMatch && statusMatch;
        });
        currentPage = 1;
        render();
    }

    function getStatusHtml(statusRaw) {
        const s = (statusRaw || '').toLowerCase();
        let cls = 'text-gray-700 bg-gray-100 border-gray-200';
        let icon = 'fa-info-circle';
        let text = statusRaw || 'Proses';

        if (['disetujui', 'selesai'].includes(s)) {
            cls = 'text-emerald-700 bg-emerald-100 border-emerald-200';
            icon = 'fa-check-circle';
        } else if (['revisi'].includes(s)) {
            cls = 'text-amber-700 bg-amber-100 border-amber-200';
            icon = 'fa-exclamation-triangle';
        } else if (['menunggu', 'review'].includes(s)) {
            cls = 'text-blue-700 bg-blue-100 border-blue-200';
            icon = 'fa-clock';
        } else if (['ditolak'].includes(s)) {
            cls = 'text-rose-700 bg-rose-100 border-rose-200';
            icon = 'fa-times-circle';
        }
        return `<span class="px-3 py-1.5 rounded-full ${cls} border inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider"><i class="fas ${icon}"></i> ${escapeHtml(text)}</span>`;
    }

    function render() {
        const start = (currentPage - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = filteredData.slice(start, end);
        const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);

        const emptyHtml = `<tr><td colspan="5" class="px-6 py-16 text-center text-gray-500"><i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>${allData.length === 0 ? 'Belum ada kegiatan.' : 'Data tidak ditemukan.'}</td></tr>`;

        if (tableBody) {
            if (pageData.length === 0) {
                tableBody.innerHTML = emptyHtml;
            } else {
                tableBody.innerHTML = pageData.map((item, idx) => {
                    const no = start + idx + 1;
                    const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';
                    const isReady = (parseInt(item.posisi) === 1 && parseInt(item.statusUtamaId) === 3);
                    const statusHtml = isReady
                        ? `<span class="px-3 py-1.5 rounded-full text-blue-700 bg-blue-100 border border-blue-200 inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider"><i class="fas fa-edit"></i> Siap Dilengkapi</span>`
                        : getStatusHtml(item.status);
                    return `<tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 text-sm text-gray-700">${no}.</td>
                        <td class="px-6 py-5 text-sm">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900">${escapeHtml(item.nama || 'Tanpa Judul')}</span>
                                <span class="text-gray-500 text-xs mt-0.5"><i class="fas fa-user mr-1"></i>${escapeHtml(item.nama_mahasiswa || 'N/A')} (${escapeHtml(item.nim || '-')})</span>
                                <span class="text-gray-400 text-xs"><i class="fas fa-graduation-cap mr-1"></i>${escapeHtml(item.prodi || item.jurusan || '-')}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-gray-600 whitespace-nowrap"><i class="fas fa-calendar-alt text-blue-400 mr-1"></i>${tgl}</td>
                        <td class="px-6 py-5">${statusHtml}</td>
                        <td class="px-6 py-5">
                            <a href="/admin/pengajuan-kegiatan/show/${item.id || 0}" class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors shadow-sm inline-flex items-center gap-1">
                                <i class="fas fa-pen"></i> Lengkapi
                            </a>
                        </td>
                    </tr>`;
                }).join('');
            }
        }

        if (mobileList) {
            if (pageData.length === 0) {
                mobileList.innerHTML = `<div class="text-center py-16 text-gray-500"><i class="fas fa-inbox text-4xl text-gray-300 mb-3 block"></i>${allData.length === 0 ? 'Belum ada kegiatan.' : 'Data tidak ditemukan.'}</div>`;
            } else {
                mobileList.innerHTML = pageData.map((item, idx) => {
                    const no = start + idx + 1;
                    const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';
                    const isReady = (parseInt(item.posisi) === 1 && parseInt(item.statusUtamaId) === 3);
                    const statusHtml = isReady
                        ? `<span class="px-3 py-1.5 rounded-full text-blue-700 bg-blue-100 border border-blue-200 inline-flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider"><i class="fas fa-edit"></i> Siap Dilengkapi</span>`
                        : getStatusHtml(item.status);
                    return `<div class="bg-white border border-gray-200 border-l-4 border-l-blue-500 rounded-xl p-4 shadow-sm">
                        <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-100">
                            <span class="bg-blue-600 text-white text-xs font-bold px-2.5 py-1 rounded-lg">#${no}</span>
                            ${statusHtml}
                        </div>
                        <p class="font-semibold text-gray-900 mb-1">${escapeHtml(item.nama || 'Tanpa Judul')}</p>
                        <p class="text-xs text-gray-500 mb-3">${escapeHtml(item.nama_mahasiswa || 'N/A')} &bull; ${escapeHtml(item.jurusan || '-')}</p>
                        <a href="/admin/pengajuan-kegiatan/show/${item.id || 0}" class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-pen"></i> Lengkapi Data
                        </a>
                    </div>`;
                }).join('');
            }
        }

        if (showingStart) showingStart.textContent = filteredData.length === 0 ? 0 : start + 1;
        if (showingEnd) showingEnd.textContent = Math.min(end, filteredData.length);
        if (totalRecords) totalRecords.textContent = filteredData.length;
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
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            currentPage = page;
            render();
        }
    };

    if (searchInput) { let t; searchInput.addEventListener('input', () => { clearTimeout(t); t = setTimeout(applyFilters, 300); }); }
    if (filterJurusan) filterJurusan.addEventListener('change', applyFilters);
    if (filterStatus) filterStatus.addEventListener('change', applyFilters);
    if (resetButton) resetButton.addEventListener('click', () => { if (searchInput) searchInput.value = ''; if (filterJurusan) filterJurusan.value = ''; if (filterStatus) filterStatus.value = ''; applyFilters(); });

    render();
});
</script>
@endpush
