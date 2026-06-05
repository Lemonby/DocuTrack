@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-6 lg:p-8 -mt-8 md:-mt-16 max-w-[1550px] mx-auto w-full">

    <!-- Modern Clean SaaS Shell -->
    <div class="bg-white rounded-3xl p-5 md:p-8 border border-slate-200 shadow-xl shadow-slate-100">
        
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">User Management</h1>
                    <p class="text-slate-500 text-xs font-medium mt-1">Manage system access, roles, and account security</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto justify-end">
                <div class="relative flex-1 lg:w-72">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="search-users" placeholder="Search by name, email, or role..." class="w-full pl-11 pr-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all outline-none">
                </div>
                <div class="flex gap-2">
                    <button class="h-12 px-4 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 transition-all font-bold text-xs flex items-center gap-2">
                        <i class="fas fa-file-export text-slate-400"></i>
                        <span>Export</span>
                    </button>
                    <button id="btn-tambah-user" class="h-12 px-6 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center justify-center gap-3 shadow-lg shadow-blue-100 font-bold text-xs uppercase tracking-wider">
                        <i class="fas fa-plus"></i>
                        <span>Create New User</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Quick Bar -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="relative">
                <select id="filter-jurusan" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 appearance-none focus:border-blue-400 transition-all outline-none">
                    <option value="">All Departments</option>
                    @foreach($list_jurusan as $jurusan)
                        <option value="{{ $jurusan }}">{{ $jurusan }}</option>
                    @endforeach
                </select>
                <i class="fas fa-university absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[11px]"></i>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>
            <div class="relative">
                <select id="filter-role" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 appearance-none focus:border-blue-400 transition-all outline-none">
                    <option value="">All User Roles</option>
                    @foreach($list_roles as $role)
                        <option value="{{ $role['namaRole'] }}">{{ $role['namaRole'] }}</option>
                    @endforeach
                </select>
                <i class="fas fa-id-badge absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[11px]"></i>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>
            <div class="relative">
                <select id="filter-status" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 appearance-none focus:border-blue-400 transition-all outline-none">
                    <option value="">All Statuses</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Non-Aktif">Non-Aktif</option>
                </select>
                <i class="fas fa-circle-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[11px]"></i>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>
            <button id="reset-filter" class="px-6 py-3 bg-white border border-slate-200 text-slate-500 rounded-xl text-[10px] font-bold uppercase tracking-wider hover:bg-slate-50 transition-all">
                Reset Filters
            </button>
        </div>

        <!-- Bulk Action Floating Bar (Hidden by default) -->
        <div id="bulk-action-bar" class="hidden fixed bottom-10 left-1/2 -translate-x-1/2 z-[150] bg-slate-800 text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-8 animate-zoom-in">
            <div class="flex items-center gap-3">
                <span id="selected-count" class="bg-blue-500 text-white px-2 py-0.5 rounded text-xs font-bold">0</span>
                <span class="text-xs font-bold text-slate-300">Users selected</span>
            </div>
            <div class="h-6 w-px bg-slate-700"></div>
            <div class="flex items-center gap-3">
                <button class="text-xs font-bold hover:text-blue-400 transition-colors flex items-center gap-2"><i class="fas fa-user-check"></i> Activate</button>
                <button class="text-xs font-bold hover:text-amber-400 transition-colors flex items-center gap-2"><i class="fas fa-user-slash"></i> Deactivate</button>
                <button class="text-xs font-bold hover:text-red-400 transition-colors flex items-center gap-2"><i class="fas fa-trash-alt"></i> Delete</button>
            </div>
            <button id="cancel-bulk" class="ml-4 text-slate-500 hover:text-white transition-colors"><i class="fas fa-times"></i></button>
        </div>

        <!-- Main Data Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 w-10">
                                <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">User Profile</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">System Role</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-center">Account Status</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-users" class="divide-y divide-slate-100">
                        {{-- JS Populated --}}
                    </tbody>
                </table>
            </div>

            <!-- Footer & Pagination -->
            <div class="px-6 py-4 bg-slate-50 flex flex-col md:flex-row items-center justify-between gap-4 border-t border-slate-200">
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    Showing <span id="showing-users" class="text-slate-800">0-0</span> of <span id="total-users" class="text-slate-800">0</span> users
                </div>
                <div id="pagination-users" class="flex items-center gap-1.5">
                    {{-- JS Populated --}}
                </div>
            </div>
        </div>
    </div>

</main>

<!-- Modals -->

<!-- Add User Modal -->
<div id="modal-add-user" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden animate-zoom-in relative">
            <div class="bg-slate-50 p-8 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Create New User</h2>
                    <p class="text-slate-500 text-[11px] font-medium mt-1">Register a new member to the system</p>
                </div>
                <button class="close-modal w-10 h-10 flex items-center justify-center hover:bg-slate-100 rounded-xl transition-all">
                    <i class="fas fa-times text-slate-400"></i>
                </button>
            </div>
            <form id="form-add-user" class="p-8 space-y-5">
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Full Name</label>
                    <input type="text" name="nama" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                        <input type="email" name="email" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">User Role</label>
                        <select name="role" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none appearance-none">
                            @foreach($list_roles as $role)
                                <option value="{{ $role['namaRole'] }}">{{ $role['namaRole'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Department</label>
                    <select name="jurusan" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none appearance-none">
                        @foreach($list_jurusan as $jurusan)
                            <option value="{{ $jurusan }}">{{ $jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Password</label>
                    <input type="password" name="password" required placeholder="Masukkan password user..." class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none">
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" class="close-modal px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all">Cancel</button>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="modal-edit-user" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden animate-zoom-in relative">
            <div class="bg-slate-50 p-8 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Edit User Profile</h2>
                    <p class="text-slate-500 text-[11px] font-medium mt-1">Update user account information</p>
                </div>
                <button class="close-modal w-10 h-10 flex items-center justify-center hover:bg-slate-100 rounded-xl transition-all">
                    <i class="fas fa-times text-slate-400"></i>
                </button>
            </div>
            <form id="form-edit-user" class="p-8 space-y-5">
                <input type="hidden" name="id">
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Full Name</label>
                    <input type="text" name="nama" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                        <input type="email" name="email" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">User Role</label>
                        <select name="role" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none appearance-none">
                            @foreach($list_roles as $role)
                                <option value="{{ $role['namaRole'] }}">{{ $role['namaRole'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Department</label>
                    <select name="jurusan" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none appearance-none">
                        @foreach($list_jurusan as $jurusan)
                            <option value="{{ $jurusan }}">{{ $jurusan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Password (Kosongkan jika tidak ingin diubah)</label>
                    <input type="password" name="password" placeholder="Masukkan password baru..." class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none">
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" class="close-modal px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all">Cancel</button>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="modal-delete-user" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden animate-zoom-in relative">
            <div class="p-8 text-center space-y-6">
                <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto text-2xl">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Delete User?</h3>
                    <p class="text-sm text-slate-500 mt-2">This action cannot be undone. All data associated with this user will be removed.</p>
                </div>
                <div class="flex flex-col gap-2">
                    <button id="confirm-delete-btn" class="w-full py-3 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 shadow-lg shadow-red-100 transition-all">Delete Account</button>
                    <button class="close-modal w-full py-3 bg-slate-50 text-slate-500 rounded-xl text-xs font-bold hover:bg-slate-100 transition-all">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Notification -->
<div id="notif-success" class="fixed bottom-8 right-8 z-[200] hidden transform translate-y-10 opacity-0 transition-all duration-300">
    <div class="bg-slate-800 text-white px-6 py-4 rounded-2xl shadow-xl flex items-center gap-3 border-l-4 border-emerald-500">
        <i class="fas fa-check-circle text-emerald-500"></i>
        <span id="notif-text" class="text-xs font-bold">Action completed successfully</span>
    </div>
</div>

<style>
    @keyframes zoom-in { from { opacity: 0; transform: scale(0.98) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    .animate-zoom-in { animation: zoom-in 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    
    /* Toggle Switch */
    .switch { position: relative; display: inline-block; width: 36px; height: 20px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e2e8f0; transition: .4s; border-radius: 20px; }
    .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #3b82f6; }
    input:checked + .slider:before { transform: translateX(16px); }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.dataUsers = @json($list_users);
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let dataUsers = [...window.dataUsers].map(u => ({
        ...u, 
        last_login: ['2 mins ago', '1 hour ago', 'Yesterday', '3 days ago'][Math.floor(Math.random() * 4)],
        join_date: 'Oct 2023'
    }));

    const ITEMS_PER_PAGE = 8;
    
    const tbody = document.getElementById('tbody-users');
    const paginationContainer = document.getElementById('pagination-users');
    const showingSpan = document.getElementById('showing-users');
    const totalSpan = document.getElementById('total-users');
    const searchInput = document.getElementById('search-users');
    const filterJurusan = document.getElementById('filter-jurusan');
    const filterRole = document.getElementById('filter-role');
    const filterStatus = document.getElementById('filter-status');
    const resetBtn = document.getElementById('reset-filter');
    const selectAll = document.getElementById('select-all');
    const bulkBar = document.getElementById('bulk-action-bar');
    const selectedCountSpan = document.getElementById('selected-count');

    const modalAdd = document.getElementById('modal-add-user');
    const modalEdit = document.getElementById('modal-edit-user');
    const modalDelete = document.getElementById('modal-delete-user');
    const btnAdd = document.getElementById('btn-tambah-user');
    const notif = document.getElementById('notif-success');
    
    let filteredData = [...dataUsers];
    let currentPage = 1;
    let deleteId = null;
    let selectedIds = new Set();

    function render() {
        const start = (currentPage - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageData = filteredData.slice(start, end);
        
        if (pageData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium italic">No users found in this view</td></tr>`;
        } else {
            tbody.innerHTML = pageData.map(item => createRow(item)).join('');
        }
        
        renderPagination();
        updateInfo();
        attachActionListeners();
        updateBulkBar();
    }

    function getRoleColor(role) {
        const colors = {
            'SuperAdmin': 'bg-indigo-50 text-indigo-600 border-indigo-100',
            'Admin': 'bg-blue-50 text-blue-600 border-blue-100',
            'Verifikator': 'bg-purple-50 text-purple-600 border-purple-100',
            'PPK': 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'Bendahara': 'bg-amber-50 text-amber-600 border-amber-100',
            'Wadir': 'bg-cyan-50 text-cyan-600 border-cyan-100',
            'Direktur': 'bg-rose-50 text-rose-600 border-rose-100',
            'Pengusul': 'bg-slate-50 text-slate-600 border-slate-100'
        };
        return colors[role] || 'bg-slate-50 text-slate-600 border-slate-100';
    }

    function createRow(item) {
        const isSelected = selectedIds.has(item.id.toString());
        return `
            <tr class="hover:bg-slate-50/50 transition-colors group ${isSelected ? 'bg-blue-50/30' : ''}">
                <td class="px-6 py-4">
                    <input type="checkbox" class="user-checkbox w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer" value="${item.id}" ${isSelected ? 'checked' : ''}>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 font-bold text-xs group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm overflow-hidden">
                            ${item.nama.substring(0, 1)}
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-800">${item.nama}</div>
                            <div class="text-[10px] text-slate-400 font-medium">Last active: ${item.last_login}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-block px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border ${getRoleColor(item.role)} shadow-sm">
                        ${item.role}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="text-[11px] font-semibold text-slate-600">${item.jurusan}</div>
                    <div class="text-[9px] text-slate-400">Joined ${item.join_date}</div>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex flex-col items-center gap-1">
                        <label class="switch">
                            <input type="checkbox" class="status-toggle" data-id="${item.id}" ${item.status === 'Aktif' ? 'checked' : ''}>
                            <span class="slider"></span>
                        </label>
                        <span class="text-[9px] font-bold ${item.status === 'Aktif' ? 'text-blue-500' : 'text-slate-400'} uppercase">${item.status}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button class="btn-edit w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-all" data-id="${item.id}" title="Edit Profile">
                            <i class="fas fa-edit text-[10px]"></i>
                        </button>
                        <button class="btn-delete w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-red-600 hover:border-red-200 transition-all" data-id="${item.id}" title="Delete User">
                            <i class="fas fa-trash-alt text-[10px]"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / ITEMS_PER_PAGE);
        if (totalPages <= 1) { paginationContainer.innerHTML = ''; return; }
        
        let html = `
            <button onclick="window.goToPage(${currentPage - 1})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:bg-slate-50 ${currentPage === 1 ? 'opacity-30 pointer-events-none' : ''}">
                <i class="fas fa-chevron-left text-[10px]"></i>
            </button>
        `;
        
        for (let i = 1; i <= totalPages; i++) {
            html += `
                <button onclick="window.goToPage(${i})" class="w-8 h-8 rounded-lg text-[10px] font-bold border transition-all ${i === currentPage ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-slate-400 border-slate-200 hover:border-blue-400 hover:text-blue-600'}">
                    ${i}
                </button>
            `;
        }

        html += `
            <button onclick="window.goToPage(${currentPage + 1})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:bg-slate-50 ${currentPage === totalPages ? 'opacity-30 pointer-events-none' : ''}">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </button>
        `;
        
        paginationContainer.innerHTML = html;
    }

    function updateInfo() {
        const start = filteredData.length > 0 ? (currentPage - 1) * ITEMS_PER_PAGE + 1 : 0;
        const end = Math.min(start + ITEMS_PER_PAGE - 1, filteredData.length);
        showingSpan.textContent = `${start}-${end}`;
        totalSpan.textContent = filteredData.length;
    }

    function updateBulkBar() {
        if (selectedIds.size > 0) {
            bulkBar.classList.remove('hidden');
            selectedCountSpan.textContent = selectedIds.size;
        } else {
            bulkBar.classList.add('hidden');
        }
    }

    window.goToPage = function(page) {
        currentPage = page;
        render();
    };

    function applyFilters() {
        const search = searchInput.value.toLowerCase();
        const jurusan = filterJurusan.value;
        const role = filterRole.value;
        const status = filterStatus.value;
        
        filteredData = dataUsers.filter(item => {
            const matchSearch = !search || item.nama.toLowerCase().includes(search) || item.email.toLowerCase().includes(search) || item.role.toLowerCase().includes(search);
            const matchJurusan = !jurusan || item.jurusan === jurusan;
            const matchRole = !role || item.role === role;
            const matchStatus = !status || item.status === status;
            return matchSearch && matchJurusan && matchRole && matchStatus;
        });
        
        currentPage = 1;
        render();
    }

    searchInput.addEventListener('input', applyFilters);
    filterJurusan.addEventListener('change', applyFilters);
    filterRole.addEventListener('change', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
    resetBtn.addEventListener('click', () => {
        searchInput.value = '';
        filterJurusan.value = '';
        filterRole.value = '';
        filterStatus.value = '';
        applyFilters();
    });

    // Bulk Select Logic
    selectAll.addEventListener('change', function() {
        const currentDataIds = filteredData.slice((currentPage - 1) * ITEMS_PER_PAGE, currentPage * ITEMS_PER_PAGE).map(u => u.id.toString());
        if (this.checked) {
            currentDataIds.forEach(id => selectedIds.add(id));
        } else {
            currentDataIds.forEach(id => selectedIds.delete(id));
        }
        render();
    });

    document.getElementById('cancel-bulk').addEventListener('click', () => {
        selectedIds.clear();
        selectAll.checked = false;
        render();
    });

    btnAdd.addEventListener('click', () => modalAdd.classList.remove('hidden'));
    
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            modalAdd.classList.add('hidden');
            modalEdit.classList.add('hidden');
            modalDelete.classList.add('hidden');
        });
    });

    function showNotif(text) {
        document.getElementById('notif-text').textContent = text;
        notif.classList.remove('hidden');
        setTimeout(() => {
            notif.classList.remove('translate-y-10', 'opacity-0');
        }, 10);
        setTimeout(() => {
            notif.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => notif.classList.add('hidden'), 300);
        }, 3000);
    }

    function attachActionListeners() {
        // Checkboxes
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                if (this.checked) selectedIds.add(this.value);
                else selectedIds.delete(this.value);
                updateBulkBar();
            });
        });

        // Status Toggles
        document.querySelectorAll('.status-toggle').forEach(tg => {
            tg.addEventListener('change', function() {
                const id = this.dataset.id;
                const checkbox = this;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/superadmin/kelola-akun/toggle-status/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const user = dataUsers.find(u => u.id == id);
                        if (user) {
                            user.status = data.status;
                            showNotif(data.message);
                            render();
                        }
                    } else {
                        checkbox.checked = !checkbox.checked;
                        Swal.fire({
                            title: 'Error',
                            text: data.message || 'Gagal mengubah status.',
                            icon: 'error',
                            confirmButtonText: 'Tutup'
                        });
                    }
                })
                .catch(err => {
                    checkbox.checked = !checkbox.checked;
                    console.error(err);
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi kesalahan jaringan.',
                        icon: 'error',
                        confirmButtonText: 'Tutup'
                    });
                });
            });
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const user = dataUsers.find(u => u.id == id);
                if (user) {
                    const form = document.getElementById('form-edit-user');
                    form.id.value = user.id;
                    form.nama.value = user.nama;
                    form.email.value = user.email;
                    form.role.value = user.role;
                    form.jurusan.value = user.jurusan;
                    modalEdit.classList.remove('hidden');
                }
            });
        });

        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                deleteId = this.dataset.id;
                modalDelete.classList.remove('hidden');
            });
        });
    }

    document.getElementById('form-add-user').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const formData = new FormData(this);
        const data = {
            nama: formData.get('nama'),
            email: formData.get('email'),
            role: formData.get('role'),
            jurusan: formData.get('jurusan'),
            password: formData.get('password')
        };

        fetch('/superadmin/kelola-akun/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resData => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;

            if (resData.success) {
                const newUser = {
                    ...resData.user,
                    last_login: 'Just now',
                    join_date: 'Oct 2023'
                };
                dataUsers.unshift(newUser);
                modalAdd.classList.add('hidden');
                this.reset();
                applyFilters();
                
                Swal.fire({
                    title: 'User Dibuat',
                    text: 'User baru berhasil disimpan ke database.',
                    icon: 'success',
                    confirmButtonText: 'Selesai'
                });
            } else {
                let errMsg = resData.message || 'Gagal membuat user.';
                if (resData.errors) {
                    errMsg = Object.values(resData.errors).flat().join('<br>');
                }
                Swal.fire({
                    title: 'Validasi Gagal',
                    html: errMsg,
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            console.error(err);
            Swal.fire({
                title: 'Error',
                text: 'Terjadi kesalahan sistem atau jaringan.',
                icon: 'error',
                confirmButtonText: 'Tutup'
            });
        });
    });

    document.getElementById('form-edit-user').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const formData = new FormData(this);
        const data = {
            id: formData.get('id'),
            nama: formData.get('nama'),
            email: formData.get('email'),
            role: formData.get('role'),
            jurusan: formData.get('jurusan'),
            password: formData.get('password')
        };

        fetch('/superadmin/kelola-akun/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resData => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;

            if (resData.success) {
                const index = dataUsers.findIndex(u => u.id == data.id);
                if (index !== -1) {
                    dataUsers[index].nama = resData.user.nama;
                    dataUsers[index].email = resData.user.email;
                    dataUsers[index].role = resData.user.role;
                    dataUsers[index].jurusan = resData.user.jurusan;
                }
                modalEdit.classList.add('hidden');
                this.reset();
                applyFilters();
                showNotif(resData.message);
            } else {
                let errMsg = resData.message || 'Gagal memperbarui profil.';
                if (resData.errors) {
                    errMsg = Object.values(resData.errors).flat().join('<br>');
                }
                Swal.fire({
                    title: 'Validasi Gagal',
                    html: errMsg,
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            console.error(err);
            Swal.fire({
                title: 'Error',
                text: 'Terjadi kesalahan sistem atau jaringan.',
                icon: 'error',
                confirmButtonText: 'Tutup'
            });
        });
    });

    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (deleteId) {
            const submitBtn = this;
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`/superadmin/kelola-akun/destroy/${deleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(res => res.json())
            .then(resData => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;

                if (resData.success) {
                    dataUsers = dataUsers.filter(u => u.id != deleteId);
                    modalDelete.classList.add('hidden');
                    applyFilters();
                    showNotif(resData.message);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: resData.message || 'Gagal menghapus user.',
                        icon: 'error',
                        confirmButtonText: 'Tutup'
                    });
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                console.error(err);
                Swal.fire({
                    title: 'Error',
                    text: 'Terjadi kesalahan sistem atau jaringan.',
                    icon: 'error',
                    confirmButtonText: 'Tutup'
                });
            });
        }
    });

    render();
});
</script>
@endpush

@endsection
