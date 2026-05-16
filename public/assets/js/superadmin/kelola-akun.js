// File: public/assets/js/super_admin/kelola-akun.js

document.addEventListener('DOMContentLoaded', function() {
    
    const dataUsers = window.dataUsers || [];
    const ITEMS_PER_PAGE = 10;
    
    let filteredData = dataUsers;
    let currentPage = 1;
    
    // Elements
    const tbody = document.getElementById('tbody-users');
    const pagination = document.getElementById('pagination-users');
    const showingSpan = document.getElementById('showing-users');
    const totalSpan = document.getElementById('total-users');
    const searchInput = document.getElementById('search-users');
    const filterProdi = document.getElementById('filter-prodi');
    const filterStatus = document.getElementById('filter-status');
    const resetBtn = document.getElementById('reset-filter');
    
    // Update Statistics
    function updateStats() {
        const total = dataUsers.length;
        const aktif = dataUsers.filter(u => u.status === 'Aktif').length;
        const tidakAktif = total - aktif;
        
        document.getElementById('totalAkun').textContent = total;
        document.getElementById('totalAktif').textContent = aktif;
        document.getElementById('totalTidakAktif').textContent = tidakAktif;
    }
    
    // Apply Filters
    function applyFilters() {
        const search = searchInput.value.toLowerCase();
        const prodi = filterProdi.value;
        const status = filterStatus.value;
        
        filteredData = dataUsers.filter(item => {
            const matchSearch = !search || 
                item.nama.toLowerCase().includes(search) ||
                item.email.toLowerCase().includes(search) ||
                (item.nim && item.nim.includes(search));
            
            const matchProdi = !prodi || item.prodi === prodi;
            const matchStatus = !status || item.status === status;
            
            return matchSearch && matchProdi && matchStatus;
        });
        
        currentPage = 1;
        render();
    }
    
    // Get Status Badge
    function getStatusBadge(status) {
        if (status === 'Aktif') {
            return `<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                <i class="fas fa-circle text-xs mr-1"></i>Aktif
            </span>`;
        }
        return `<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
            <i class="fas fa-circle text-xs mr-1"></i>Tidak Aktif
        </span>`;
    }
    
    // Get Role Badge
    function getRoleBadge(role) {
        const colors = {
            'Admin': 'bg-purple-100 text-purple-700',
            'Dosen': 'bg-blue-100 text-blue-700',
            'Mahasiswa': 'bg-amber-100 text-amber-700',
            'Super Admin': 'bg-rose-100 text-rose-700'
        };
        return `<span class="px-3 py-1 rounded-lg text-xs font-medium ${colors[role] || colors['Mahasiswa']}">${role}</span>`;
    }
    
    // Format Last Login
    function formatLastLogin(datetime) {
        if (!datetime) return '<span class="text-gray-400 text-xs">-</span>';
        
        const date = new Date(datetime);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 60) return `<span class="text-green-600 text-xs">${diffMins} menit lalu</span>`;
        if (diffHours < 24) return `<span class="text-blue-600 text-xs">${diffHours} jam lalu</span>`;
        if (diffDays < 7) return `<span class="text-amber-600 text-xs">${diffDays} hari lalu</span>`;
        
        return `<span class="text-gray-500 text-xs">${date.toLocaleDateString('id-ID', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric' 
        })}</span>`;
    }
    
    // Render Table
    function renderTable() {
        const start = (currentPage - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageData = filteredData.slice(start, end);
        
        if (pageData.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">Tidak ada data yang ditemukan</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = pageData.map((item, index) => {
            const rowNumber = start + index + 1;
            return `
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-600">${rowNumber}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="font-medium text-gray-800">${escapeHtml(item.nama)}</div>
                        <div class="text-xs text-gray-500">${escapeHtml(item.email)}</div>
                    </td>
                    <td class="px-4 py-3 text-sm">${getRoleBadge(item.role)}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(item.prodi)}</td>
                    <td class="px-4 py-3 text-sm">${getStatusBadge(item.status)}</td>
                    <td class="px-4 py-3 text-sm">${formatLastLogin(item.last_login)}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex gap-2 justify-center">
                            <button onclick="viewUser(${item.id})" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">
                                Detail
                            </button>
                            <button onclick="editUser(${item.id})" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded text-xs hover:bg-gray-50">
                                Edit
                            </button>
                            <button onclick="deleteUser(${item.id})" class="px-3 py-1 bg-white border border-gray-300 text-red-600 rounded text-xs hover:bg-red-50">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }
    
    // Render Pagination
    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / ITEMS_PER_PAGE);
        
        if (totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }
        
        let html = '';
        
        // Previous
        html += `<button onclick="window.usersTable.goToPage(${currentPage - 1})" 
                    ${currentPage === 1 ? 'disabled' : ''} 
                    class="px-3 py-1 rounded text-sm ${currentPage === 1 ? 'bg-gray-100 text-gray-400' : 'bg-white border border-gray-300 hover:bg-gray-50'}">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>`;
        
        // Pages
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `<button onclick="window.usersTable.goToPage(${i})" 
                            class="px-3 py-1 rounded text-sm ${i === currentPage ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 hover:bg-gray-50'}">
                            ${i}
                        </button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += '<span class="px-2 text-gray-400">...</span>';
            }
        }
        
        // Next
        html += `<button onclick="window.usersTable.goToPage(${currentPage + 1})" 
                    ${currentPage === totalPages ? 'disabled' : ''} 
                    class="px-3 py-1 rounded text-sm ${currentPage === totalPages ? 'bg-gray-100 text-gray-400' : 'bg-white border border-gray-300 hover:bg-gray-50'}">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>`;
        
        pagination.innerHTML = html;
    }
    
    // Update Info
    function updateInfo() {
        const start = (currentPage - 1) * ITEMS_PER_PAGE + 1;
        const end = Math.min(start + ITEMS_PER_PAGE - 1, filteredData.length);
        
        showingSpan.textContent = filteredData.length > 0 ? `${start}-${end}` : '0';
        totalSpan.textContent = filteredData.length;
    }
    
    // Render All
    function render() {
        renderTable();
        renderPagination();
        updateInfo();
    }
    
    // Go To Page
    function goToPage(page) {
        const totalPages = Math.ceil(filteredData.length / ITEMS_PER_PAGE);
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            render();
        }
    }
    
    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Event Listeners
    searchInput.addEventListener('input', applyFilters);
    filterProdi.addEventListener('change', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
    resetBtn.addEventListener('click', () => {
        searchInput.value = '';
        filterProdi.value = '';
        filterStatus.value = '';
        applyFilters();
    });
    
    // Global functions
    window.usersTable = { goToPage };
    
    window.viewUser = function(id) {
        alert('View user: ' + id);
    };
    
    window.editUser = function(id) {
        alert('Edit user: ' + id);
    };
    
    window.deleteUser = function(id) {
        if (confirm('Yakin hapus akun ini?')) {
            alert('Delete user: ' + id);
        }
    };
    
    // Initialize
    updateStats();
    render();
});