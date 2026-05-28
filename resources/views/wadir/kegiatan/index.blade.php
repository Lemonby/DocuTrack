@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Enhanced Table with Filters & Pagination - RESPONSIVE -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <!-- Header Section -->
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200 flex-shrink-0">
            <!-- Title and Filters Container -->
            <div class="flex flex-col gap-4">
                <!-- Title -->
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-600"></i>
                    <span>Antrian Verifikasi (Persetujuan Wadir)</span>
                </h3>
                
                <!-- Filter Controls - Stack on Mobile -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <!-- Jurusan Filter -->
                    <div class="relative flex-1">
                        <select id="filter-jurusan"
                                style="color: #374151 !important; font-size: 14px !important;"
                                class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="" selected style="color: #374151 !important;">Semua Jurusan</option>
                            @foreach ($jurusan_list as $jurusan)
                                <option value="{{ $jurusan }}" style="color: #374151 !important;">{{ $jurusan }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <!-- Reset Button -->
                    <button id="reset-filter" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-redo text-xs"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-3 sm:mt-4 relative">
                <input type="text" id="search-kegiatan-input" placeholder="Cari nama kegiatan, pengusul, atau NIM..." class="w-full pl-9 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="min-w-full" id="table-kegiatan">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[250px]">Kegiatan & Pengusul</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Tgl. Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kegiatan-table-body" class="divide-y divide-gray-100 bg-white">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="mobile-kegiatan-list" class="p-3 space-y-3">
                <!-- Mobile cards will be populated by JavaScript -->
            </div>
        </div>

        <!-- Pagination -->
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                    Menampilkan <span id="showing-kegiatan" class="font-semibold text-gray-800">0</span> dari <span id="total-kegiatan" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-container" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

</main>

<style>
    /* Mobile Card Styling */
    .mobile-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #3b82f6;
    }
    
    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .mobile-card-number {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
    }
    
    .mobile-card-row {
        margin-bottom: 0.875rem;
    }
    
    .mobile-card-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .mobile-card-label i {
        color: #3b82f6;
        font-size: 0.75rem;
    }
    
    .mobile-card-kegiatan {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
        line-height: 1.4;
    }
    
    .mobile-card-pengusul {
        font-size: 0.85rem;
        color: #4b5563;
        margin-top: 0.25rem;
    }
    
    .mobile-card-actions {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .mobile-card-btn {
        width: 100%;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        color: white;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    
    /* Pagination Button Styling */
    .pagination-btn {
        min-width: 2rem;
        height: 2rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .pagination-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: transparent;
    }
</style>

@push('scripts')
<script>
    window.allDataUsulan = @json($list_kegiatan ?? []);
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dataRaw = window.allDataUsulan || [];
    const ITEMS_PER_PAGE = 5;
    
    class KegiatanTableManager {
        constructor(data) {
            this.allData = data;
            this.filteredData = data;
            this.currentPage = 1;
            this.itemsPerPage = ITEMS_PER_PAGE;
            
            this.tbody = document.getElementById('kegiatan-table-body');
            this.mobileList = document.getElementById('mobile-kegiatan-list');
            this.paginationContainer = document.getElementById('pagination-container');
            this.showingSpan = document.getElementById('showing-kegiatan');
            this.totalSpan = document.getElementById('total-kegiatan');
            
            this.searchInput = document.getElementById('search-kegiatan-input');
            this.filterJurusan = document.getElementById('filter-jurusan');
            this.resetBtn = document.getElementById('reset-filter');
            
            if (this.tbody) this.init();
        }
        
        init() {
            this.render();
            this.attachEvents();
        }
        
        attachEvents() {
            const update = () => { this.currentPage = 1; this.filter(); };
            if(this.searchInput) this.searchInput.addEventListener('input', update);
            if(this.filterJurusan) this.filterJurusan.addEventListener('change', update);
            if(this.resetBtn) {
                this.resetBtn.addEventListener('click', () => {
                    if(this.searchInput) this.searchInput.value = '';
                    if(this.filterJurusan) this.filterJurusan.value = '';
                    this.currentPage = 1;
                    this.filter();
                });
            }
        }
        
        filter() {
            const search = this.searchInput ? this.searchInput.value.toLowerCase() : '';
            const jurusan = this.filterJurusan ? this.filterJurusan.value.toLowerCase() : '';
            this.filteredData = this.allData.filter(item => {
                const matchSearch = !search || 
                    (item.nama && item.nama.toLowerCase().includes(search)) ||
                    (item.pengusul && item.pengusul.toLowerCase().includes(search));
                const matchJurusan = !jurusan || (item.jurusan && item.jurusan.toLowerCase() === jurusan);
                return matchSearch && matchJurusan;
            });
            this.render();
        }
        
        render() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            const pageData = this.filteredData.slice(start, end);
            
            if(this.showingSpan) this.showingSpan.textContent = pageData.length;
            if(this.totalSpan) this.totalSpan.textContent = this.filteredData.length;
            
            if (pageData.length === 0) {
                this.renderEmpty();
                this.renderPagination(0);
                return;
            }
            
            if(this.tbody) {
                this.tbody.innerHTML = pageData.map((item, i) => {
                    const no = start + i + 1;
                    const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';
                    const isApproved = item.status === 'Disetujui' || item.status === 'Selesai' || item.status === 'Telah diverifikasi';
                    const statusClass = isApproved 
                        ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' 
                        : 'text-blue-700 bg-blue-50 border border-blue-200';
                    const statusIcon = isApproved ? 'fa-check-circle text-emerald-600' : 'fa-hourglass-half text-blue-500';
                    return `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${no}.</td>
                        <td class="px-6 py-5 text-sm">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900 mb-1">${item.nama}</span>
                                <span class="text-gray-600 text-xs">${item.pengusul}</span>
                                <span class="text-gray-500 text-xs mt-0.5 font-medium"><i class="fas fa-graduation-cap mr-1"></i>${item.prodi || '-'}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">${tgl}</td>
                        <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full ${statusClass}">
                                <i class="fas ${statusIcon}"></i> ${item.status || 'Menunggu'}
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                            <a href="{{ url('/wadir/kegiatan/show') }}/${item.id}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                                <i class="fas fa-check-circle"></i> Setujui
                            </a>
                        </td>
                    </tr>`;
                }).join('');
            }
            
            if(this.mobileList) {
                this.mobileList.innerHTML = pageData.map((item, i) => {
                    const no = start + i + 1;
                    const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';
                    const isApproved = item.status === 'Disetujui' || item.status === 'Selesai' || item.status === 'Telah diverifikasi';
                    const statusClass = isApproved 
                        ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' 
                        : 'text-blue-700 bg-blue-50 border border-blue-200';
                    const statusIcon = isApproved ? 'fa-check-circle text-emerald-600' : 'fa-hourglass-half text-blue-500';
                    return `
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <span class="mobile-card-number">#${no}</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">
                                <i class="fas ${statusIcon}"></i> ${item.status || 'Menunggu'}
                            </span>
                        </div>
                        <div class="mobile-card-row">
                            <div class="mobile-card-label"><i class="fas fa-clipboard-list"></i> Nama Kegiatan</div>
                            <div class="mobile-card-kegiatan">${item.nama}</div>
                        </div>
                        <div class="mobile-card-row">
                            <div class="mobile-card-label"><i class="fas fa-user"></i> Pengusul</div>
                            <div class="mobile-card-pengusul">${item.pengusul}</div>
                        </div>
                        <div class="mobile-card-actions">
                            <a href="{{ url('/wadir/kegiatan/show') }}/${item.id}" class="mobile-card-btn">
                                <i class="fas fa-check-circle"></i> Setujui
                            </a>
                        </div>
                    </div>`;
                }).join('');
            }
            this.renderPagination(Math.ceil(this.filteredData.length / this.itemsPerPage));
        }
        
        renderEmpty() {
            const html = `<div class="p-10 text-center text-gray-400">Tidak ada data.</div>`;
            if(this.tbody) this.tbody.innerHTML = `<tr><td colspan="5">${html}</td></tr>`;
            if(this.mobileList) this.mobileList.innerHTML = html;
        }
        
        renderPagination(totalPages) {
            if (!this.paginationContainer || totalPages <= 1) {
                if(this.paginationContainer) this.paginationContainer.innerHTML = '';
                return;
            }
            let btns = '';
            for (let i = 1; i <= totalPages; i++) {
                btns += `<button onclick="kegiatanTable.goToPage(${i})" class="pagination-btn ${i===this.currentPage?'active':''}">${i}</button>`;
            }
            this.paginationContainer.innerHTML = btns;
        }

        goToPage(page) {
            this.currentPage = page;
            this.render();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
    window.kegiatanTable = new KegiatanTableManager(dataRaw);
});
</script>
@endpush

@endsection
