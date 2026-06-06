@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 sm:p-6 lg:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="riwayat-list" class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        
        <!-- Header Section -->
        <div class="flex flex-col p-4 sm:p-6 border-b border-gray-200 flex-shrink-0 gap-3 sm:gap-4">
            <div>
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">Riwayat Persetujuan</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftar semua usulan yang telah Anda setujui.</p>
            </div>
            
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute top-1/2 left-3 sm:left-4 -translate-y-1/2 text-gray-400 z-10 text-sm"></i>
                    <input type="text" id="search-riwayat-input" placeholder="Cari Nama Kegiatan..."
                           class="w-full pl-9 sm:pl-11 pr-3 sm:pr-4 py-2 sm:py-2.5 text-xs sm:text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400">
                </div>
                
                <div class="relative w-full sm:w-64 lg:w-80">
                    <i class="fas fa-graduation-cap absolute top-1/2 left-3 sm:left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10 text-sm"></i>
                    <select id="filter-jurusan" 
                            style="color: #374151 !important;"
                            class="w-full pl-9 sm:pl-11 pr-8 sm:pr-10 py-2 sm:py-2.5 text-xs sm:text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                        @foreach ($jurusan_list as $jurusan)
                            <option value="{{ strtolower($jurusan) }}" style="color: #374151 !important; font-weight: 600;">{{ $jurusan }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 sm:right-4 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
                </div>
            </div>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto border border-gray-100 rounded-lg">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">Kegiatan & Pengusul</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">Tgl. Disetujui</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="riwayat-table-body" class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div id="riwayat-mobile-cards" class="md:hidden divide-y divide-gray-200">
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200 gap-3 sm:gap-4">
            <div id="pagination-info" class="text-xs sm:text-sm text-gray-600 text-center sm:text-left"></div>
            <div id="pagination-riwayat" class="flex gap-1 flex-wrap justify-center"></div>
        </div>
        
    </section>
</main>

@push('scripts')
<script>
    window.riwayatData = @json(array_values($list_riwayat));
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-riwayat-input');
    const filterJurusan = document.getElementById('filter-jurusan');
    const tableBody = document.getElementById('riwayat-table-body');
    const mobileCards = document.getElementById('riwayat-mobile-cards');
    const paginationContainer = document.getElementById('pagination-riwayat');
    const paginationInfo = document.getElementById('pagination-info');
    
    const allData = window.riwayatData || [];
    const ITEMS_PER_PAGE = 10;
    let filteredData = [...allData];
    let currentPage = 1;

    function applyFilters() {
        const searchText = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const jurusanFilter = filterJurusan ? filterJurusan.value.toLowerCase() : '';
        
        filteredData = allData.filter(item => {
            const nama = (item.nama || '').toLowerCase();
            const pengusul = (item.pengusul || '').toLowerCase();
            const jurusan = (item.jurusan || '').toLowerCase();
            
            const searchMatch = !searchText || nama.includes(searchText) || pengusul.includes(searchText);
            const jurusanMatch = !jurusanFilter || jurusan === jurusanFilter;
            
            return searchMatch && jurusanMatch;
        });
        
        currentPage = 1;
        render();
    }

    function render() {
        const totalItems = filteredData.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));
        const start = (currentPage - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageData = filteredData.slice(start, end);
        
        // Render Desktop Table
        if (tableBody) {
            if (pageData.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 lg:px-6 py-10 text-center text-gray-500 italic text-sm">
                            Tidak ada data.
                        </td>
                    </tr>`;
            } else {
                tableBody.innerHTML = pageData.map((item, index) => {
                    const no = start + index + 1;
                    return `
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-4 lg:px-6 py-4 lg:py-5 whitespace-nowrap text-sm text-gray-700">${no}.</td>
                        <td class="px-4 lg:px-6 py-4 lg:py-5 text-sm">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900 mb-1">${item.nama}</span>
                                <span class="text-gray-600 text-xs">${item.pengusul} (${item.nim || '-'})</span>
                                <span class="text-gray-500 text-xs mt-0.5"><i class="fas fa-graduation-cap mr-1"></i>${item.prodi || item.jurusan}</span>
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-4 lg:py-5 whitespace-nowrap text-sm text-gray-600">${item.tgl}</td>
                        <td class="px-4 lg:px-6 py-4 lg:py-5 whitespace-nowrap text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-green-700 bg-green-100">
                                <i class="fas fa-check-circle"></i> ${item.status || 'Disetujui'}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 lg:py-5 whitespace-nowrap text-sm font-medium">
                            <a href="{{ url('/wadir/kegiatan/show') }}/${item.id}" class="bg-blue-600 text-white px-3 lg:px-4 py-1.5 lg:py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>`;
                }).join('');
            }
        }

        // Render Mobile Cards
        if (mobileCards) {
            if (pageData.length === 0) {
                mobileCards.innerHTML = `<div class="p-8 text-center text-gray-500 italic text-sm">Tidak ada data.</div>`;
            } else {
                mobileCards.innerHTML = pageData.map((item, index) => {
                    const no = start + index + 1;
                    return `
                    <div class="p-4 hover:bg-gray-50 transition-colors border-b border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-500">#${no}</span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold text-green-700 bg-green-100">
                                <i class="fas fa-check-circle text-[10px]"></i> ${item.status || 'Disetujui'}
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-900 text-sm mb-2">${item.nama}</h3>
                        <div class="space-y-1 text-xs text-gray-600 mb-3">
                            <div><i class="fas fa-user w-4"></i> ${item.pengusul}</div>
                            <div><i class="fas fa-calendar-check w-4"></i> ${item.tgl}</div>
                        </div>
                        <a href="{{ url('/wadir/kegiatan/show') }}/${item.id}" class="w-full bg-blue-600 text-white py-2 rounded-md text-xs font-medium text-center block">Detail</a>
                    </div>`;
                }).join('');
            }
        }
        
        if (paginationInfo) {
            paginationInfo.innerHTML = `Menampilkan <span class="font-semibold">${totalItems > 0 ? start + 1 : 0}</span> - <span class="font-semibold">${Math.min(end, totalItems)}</span> dari <span class="font-semibold">${totalItems}</span>`;
        }
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (!paginationContainer || totalPages <= 1) {
            if(paginationContainer) paginationContainer.innerHTML = '';
            return;
        }
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<button class="px-3 py-1 rounded border ${i===currentPage?'bg-blue-600 text-white':'bg-white hover:bg-gray-50'}" onclick="window.wadirRiwayat.goToPage(${i})">${i}</button>`;
        }
        paginationContainer.innerHTML = html;
    }

    window.wadirRiwayat = {
        goToPage: function(page) {
            currentPage = page;
            render();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    searchInput?.addEventListener('input', applyFilters);
    filterJurusan?.addEventListener('change', applyFilters);
    render();
});
</script>
@endpush

@endsection
