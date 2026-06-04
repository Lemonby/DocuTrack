@extends('layouts.app')

@section('content')
<main class="main-content font-poppins px-3 py-4 sm:p-6 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Statistics Cards -->
    <section class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-5">
        
        <div class="relative group p-4 rounded-xl shadow-md overflow-hidden text-white
                    bg-gradient-to-br from-blue-400 to-blue-500 
                    hover:shadow-lg 
                    transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col gap-1">
                <h3 class="text-3xl md:text-5xl font-bold">{{ $stats['total'] }}</h3>
                <p class="text-xs md:text-sm font-medium opacity-90">Total Usulan</p>
                <div class="mt-2 text-right opacity-70">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="relative group p-4 rounded-xl shadow-md overflow-hidden text-white
                    bg-gradient-to-br from-green-400 to-green-500 
                    hover:shadow-lg 
                    transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col gap-1">
                <h3 class="text-3xl md:text-5xl font-bold">{{ $stats['disetujui'] }}</h3>
                <p class="text-xs md:text-sm font-medium opacity-90">Disetujui</p>
                <div class="mt-2 text-right opacity-70">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="relative group p-4 rounded-xl shadow-md overflow-hidden text-yellow-900 
                    bg-gradient-to-br from-yellow-300 to-yellow-400 
                    hover:shadow-lg 
                    transition-all duration-300 col-span-2 lg:col-span-1">
            <div class="absolute inset-0 z-0 opacity-[0.05] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(0,0,0,0.3)_4px,rgba(0,0,0,0.3)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col gap-1">
                <h3 class="text-3xl md:text-5xl font-bold">{{ $stats['menunggu'] }}</h3>
                <p class="text-xs md:text-sm font-medium opacity-90">Menunggu Persetujuan</p>
                <div class="mt-2 text-right opacity-70 text-yellow-800">
                    <i class="fas fa-hourglass-half text-2xl"></i>
                </div>
            </div>
        </div>
        
    </section>

    <!-- Table Section -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-5 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2 mb-3">
                <i class="fas fa-list-alt text-blue-600"></i>
                <span>Daftar Usulan (Semua Status)</span>
            </h3>
            
            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mb-3">
                <div class="relative flex-1">
                    <select id="filter-status" 
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="" selected style="color: #374151 !important;">Semua Status</option>
                        <option value="menunggu" style="color: #374151 !important;">Menunggu</option>
                        <option value="disetujui" style="color: #374151 !important;">Disetujui</option>
                        <option value="ditolak" style="color: #374151 !important;">Ditolak</option>
                    </select>
                    <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
                
                <div class="relative flex-1">
                    <select id="filter-jurusan"
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="" selected style="color: #374151 !important;">Semua Jurusan</option>
                        @foreach ($jurusan_list as $jurusan)
                            <option value="{{ strtolower($jurusan) }}" style="color: #374151 !important;">{{ $jurusan }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
                
                <button id="reset-filter" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-3 sm:mt-4 relative">
                <input type="text" id="search-wadir-input" placeholder="Cari nama kegiatan, pengusul, atau NIM..." class="w-full pl-9 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <div>
                <table class="min-w-full" id="table-wadir">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kegiatan & Pengusul</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="wadir-table-body" class="divide-y divide-gray-100 bg-white"></tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden">
            <div id="mobile-wadir-list" class="p-3 space-y-3"></div>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-50 bg-slate-50/20">
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-600">
                    Menampilkan <span id="showing-wadir" class="text-blue-600 font-semibold">0</span> dari <span id="total-wadir" class="text-slate-800 font-semibold">0</span> Data
                </div>
                <div id="pagination-wadir" class="flex gap-1"></div>
            </div>
        </div>
    </section>

</main>

<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    /* Mobile Card Styling - Optimized for Phone */
    .mobile-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        border-left: 4px solid #3b82f6;
    }
    
    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
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
    
    .mobile-card-row:last-child {
        margin-bottom: 0;
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
        font-size: 0.75rem;
        color: #9ca3af;
    }
    
    .mobile-card-value {
        font-size: 0.875rem;
        color: #1f2937;
        font-weight: 500;
        line-height: 1.5;
    }
    
    .mobile-card-kegiatan {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
        line-height: 1.4;
        margin-bottom: 0.25rem;
    }
    
    .mobile-card-pengusul {
        font-size: 0.8rem;
        color: #6b7280;
    }
    
    .mobile-card-prodi {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .mobile-card-actions {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #f3f4f6;
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
    
    .mobile-card-btn:active {
        opacity: 0.9;
        transform: scale(0.98);
    }
    
    /* Status Badge */
    .status-badge {
        padding: 0.375rem 0.625rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .status-badge i {
        font-size: 0.625rem;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #9ca3af;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .empty-state-text {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
    }
    
    /* Pagination Buttons */
    .pagination-buttons {
        min-width: 2.25rem;
        height: 2.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        background: white;
        color: #374151;
        transition: all 0.2s;
    }
    
    .pagination-buttons:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination-buttons.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: transparent;
    }
    
    .pagination-buttons:not(:disabled):active {
        transform: scale(0.95);
    }
</style>

@push('scripts')
<script>
    window.dataUsulan = @json($list_usulan ?? []);
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const dataUsulan = window.dataUsulan || [];
    const ITEMS_PER_PAGE = 5; 
    
    class WadirTableManager {
        constructor(data) {
            this.allData = data;
            this.filteredData = data;
            this.currentPage = 1;
            this.itemsPerPage = ITEMS_PER_PAGE;
            
            this.tbody = document.getElementById('wadir-table-body');
            this.mobileList = document.getElementById('mobile-wadir-list');
            this.paginationContainer = document.getElementById('pagination-wadir');
            this.showingSpan = document.getElementById('showing-wadir');
            this.totalSpan = document.getElementById('total-wadir');
            
            this.searchInput = document.getElementById('search-wadir-input');
            this.filterStatus = document.getElementById('filter-status');
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
            if(this.filterStatus) this.filterStatus.addEventListener('change', update);
            if(this.filterJurusan) this.filterJurusan.addEventListener('change', update);
            
            if(this.resetBtn) {
                this.resetBtn.addEventListener('click', () => {
                    if(this.searchInput) this.searchInput.value = '';
                    if(this.filterStatus) this.filterStatus.value = '';
                    if(this.filterJurusan) this.filterJurusan.value = '';
                    this.currentPage = 1;
                    this.filter();
                });
            }
        }
        
        filter() {
            const search = this.searchInput ? this.searchInput.value.toLowerCase() : '';
            const status = this.filterStatus ? this.filterStatus.value.toLowerCase() : '';
            const jurusan = this.filterJurusan ? this.filterJurusan.value.toLowerCase() : '';
            
            this.filteredData = this.allData.filter(item => {
                const matchSearch = !search || 
                    (item.nama && item.nama.toLowerCase().includes(search)) ||
                    (item.pengusul && item.pengusul.toLowerCase().includes(search)) ||
                    (item.nim && item.nim.toLowerCase().includes(search));
                const matchStatus = !status || (item.status && item.status.toLowerCase() === status);
                const matchJurusan = !jurusan || (item.jurusan && item.jurusan.toLowerCase() === jurusan);
                
                return matchSearch && matchStatus && matchJurusan;
            });
            this.render();
        }
        
        render() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            const pageData = this.filteredData.slice(start, end);
            
            // Update counter
            if(this.showingSpan) this.showingSpan.textContent = pageData.length;
            if(this.totalSpan) this.totalSpan.textContent = this.filteredData.length;
            
            if (pageData.length === 0) {
                this.renderEmpty();
                this.renderPagination(0);
                return;
            }
            
            // Render Desktop Table
            if(this.tbody) {
                this.tbody.innerHTML = pageData.map((item, i) => {
                    const no = start + i + 1;
                    const s = (item.status || '').toLowerCase();
                    let statusClass = 'bg-slate-100 text-slate-500 border-slate-200';
                    let iconClass = 'fa-info-circle';
                    
                    if (['disetujui', 'setuju', 'selesai', 'tuntas'].includes(s)) {
                        statusClass = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                        iconClass = 'fa-check-circle';
                    } else if (['revisi', 'perlu perbaikan'].includes(s)) {
                        statusClass = 'bg-amber-100 text-amber-700 border-amber-200';
                        iconClass = 'fa-exclamation-triangle';
                    } else if (['menunggu', 'review', 'pending', 'sedang diproses', 'menunggu verifikasi'].includes(s)) {
                        statusClass = 'bg-blue-100 text-blue-700 border-blue-200';
                        iconClass = 'fa-hourglass-half';
                    } else if (['ditolak', 'tolak', 'batal'].includes(s)) {
                        statusClass = 'bg-rose-100 text-rose-700 border-rose-200';
                        iconClass = 'fa-times-circle';
                    }
                    const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';

                    return `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${no}.</td>
                        <td class="px-6 py-5 text-sm">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900 mb-1">${item.nama || ''}</span>
                                <span class="text-gray-600 text-xs">${item.pengusul || ''} <span class="text-gray-400">(${item.nim || '-'})</span></span>
                                <span class="text-gray-500 text-xs mt-0.5 font-medium"><i class="fas fa-graduation-cap mr-1"></i>${item.prodi || '-'}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                                ${tgl}
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-[10px] font-bold uppercase tracking-wider">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border ${statusClass}">
                                <i class="fas ${iconClass}"></i> ${item.status || 'Menunggu'}
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                            <a href="{{ url('/wadir/kegiatan/show') }}/${item.id}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-blue-700 transition-all shadow-sm flex items-center gap-2 w-max">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>`;
                }).join('');
            }
            
            // Render Mobile Cards
            if(this.mobileList) {
                this.mobileList.innerHTML = pageData.map((item, i) => {
                    const no = start + i + 1;
                    const s = (item.status || '').toLowerCase();
                    let statusClass = 'bg-slate-100 text-slate-500 border-slate-200';
                    let iconClass = 'fa-info-circle';
                    
                    if (['disetujui', 'setuju', 'selesai', 'tuntas'].includes(s)) {
                        statusClass = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                        iconClass = 'fa-check-circle';
                    } else if (['revisi', 'perlu perbaikan'].includes(s)) {
                        statusClass = 'bg-amber-100 text-amber-700 border-amber-200';
                        iconClass = 'fa-exclamation-triangle';
                    } else if (['menunggu', 'review', 'pending', 'sedang diproses', 'menunggu verifikasi'].includes(s)) {
                        statusClass = 'bg-blue-100 text-blue-700 border-blue-200';
                        iconClass = 'fa-hourglass-half';
                    } else if (['ditolak', 'tolak', 'batal'].includes(s)) {
                        statusClass = 'bg-rose-100 text-rose-700 border-rose-200';
                        iconClass = 'fa-times-circle';
                    }
                    const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';

                    return `
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <span class="mobile-card-number">#${no}</span>
                            <span class="status-badge border ${statusClass} text-[10px] uppercase font-bold">
                                <i class="fas ${iconClass}"></i>
                                ${item.status || 'Menunggu'}
                            </span>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-clipboard-list"></i>
                                Nama Kegiatan
                            </div>
                            <div class="mobile-card-kegiatan">${item.nama || '-'}</div>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-user"></i>
                                Pengusul
                            </div>
                            <div class="mobile-card-pengusul">
                                ${item.pengusul || '-'} (${item.nim || '-'})
                            </div>
                            <div class="mobile-card-prodi">
                                <i class="fas fa-graduation-cap"></i>
                                ${item.prodi || '-'}
                            </div>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-calendar-alt"></i>
                                Tanggal Pengajuan
                            </div>
                            <div class="mobile-card-value">${tgl}</div>
                        </div>
                        
                        <div class="mobile-card-actions">
                            <a href="{{ url('/wadir/kegiatan/show') }}/${item.id}" class="mobile-card-btn font-bold uppercase tracking-widest text-[10px]">
                                <i class="fas fa-eye"></i>
                                Lihat Detail
                            </a>
                        </div>
                    </div>`;
                }).join('');
            }
            
            this.renderPagination(Math.ceil(this.filteredData.length / this.itemsPerPage));
        }
        
        renderEmpty() {
            const emptyHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <div class="empty-state-text">Data tidak ditemukan</div>
                </div>`;
            
            if(this.tbody) this.tbody.innerHTML = `<tr><td colspan="5">${emptyHTML}</td></tr>`;
            if(this.mobileList) this.mobileList.innerHTML = emptyHTML;
        }
        
        renderPagination(totalPages) {
            if (!this.paginationContainer) return;
            
            if (totalPages <= 1) {
                this.paginationContainer.innerHTML = '';
                return;
            }

            let btns = '';
            // Prev Button
            btns += `<button onclick="wadirTable.goToPage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''} class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 ${this.currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 transition-colors'}" ${this.currentPage === 1 ? 'disabled' : ''}><i class="fas fa-chevron-left text-[10px]"></i></button>`;

            // Page Numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= this.currentPage - 1 && i <= this.currentPage + 1)) {
                    const activeCls = i === this.currentPage ? 'bg-blue-600 text-white border-blue-600 font-bold shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-slate-100';
                    btns += `<button onclick="wadirTable.goToPage(${i})" class="w-8 h-8 flex items-center justify-center rounded-lg border ${activeCls} text-[10px] transition-colors">${i}</button>`;
                } else if (i === this.currentPage - 2 || i === this.currentPage + 2) {
                    btns += `<span class="w-6 flex items-center justify-center text-slate-300 font-bold text-xs">...</span>`;
                }
            }

            // Next Button
            btns += `<button onclick="wadirTable.goToPage(${this.currentPage + 1})" ${this.currentPage === totalPages ? 'disabled' : ''} class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 ${this.currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 transition-colors'}" ${this.currentPage === totalPages ? 'disabled' : ''}><i class="fas fa-chevron-right text-[10px]"></i></button>`;

            this.paginationContainer.innerHTML = btns;
        }

        goToPage(page) {
            const totalPages = Math.ceil(this.filteredData.length / this.itemsPerPage);
            if (page >= 1 && page <= totalPages && page !== this.currentPage) {
                this.currentPage = page;
                this.render();
            }
        }
    }
    
    window.wadirTable = new WadirTableManager(dataUsulan);
});
</script>
@endpush

@endsection
