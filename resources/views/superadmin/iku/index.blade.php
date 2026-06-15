@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-6 lg:p-8 -mt-8 md:-mt-16 max-w-[1550px] mx-auto w-full">

    <!-- Modern Clean SaaS Shell -->
    <div class="bg-white rounded-3xl p-5 md:p-8 border border-slate-200 shadow-xl shadow-slate-100">
        
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">IKU & Renstra Management</h1>
                    <p class="text-slate-500 text-xs font-medium mt-1">Unified performance metrics for system-wide strategic planning</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto justify-end">
                <div class="relative flex-1 lg:w-72">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="search-iku" placeholder="Search by name, period, or type..." class="w-full pl-11 pr-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all outline-none">
                </div>
                <button id="btn-tambah-iku" class="h-12 px-6 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center justify-center gap-3 shadow-lg shadow-blue-100 font-bold text-xs uppercase tracking-wider">
                    <i class="fas fa-plus"></i>
                    <span>Create Indicator</span>
                </button>
            </div>
        </div>

        <!-- Info Card -->
        <div class="mb-8 bg-slate-50 border border-slate-200 rounded-2xl p-6 flex items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-blue-600 shadow-sm">
                    <i class="fas fa-info-circle"></i>
                </div>
                <p class="text-[11px] text-slate-500 font-medium leading-relaxed max-w-2xl">
                    All indicators listed here are unified for the **Kerangka Acuan Kerja (KAK)** selection process. 
                    You can manage both **Main Performance (IKU)** and **Strategic Plans (Renstra)** in this single view for better efficiency.
                </p>
            </div>
            <div class="hidden sm:flex items-center gap-8 pr-4">
                <div class="text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Active</p>
                    <p id="stat-active" class="text-xl font-bold text-slate-800">0</p>
                </div>
                <div class="w-px h-8 bg-slate-200"></div>
                <div class="text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Indicators</p>
                    <p id="stat-total" class="text-xl font-bold text-slate-800">0</p>
                </div>
            </div>
        </div>

        <!-- Indicators Grid -->
        <div id="iku-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            {{-- JS Populated --}}
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden py-20 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-3xl">
                <i class="fas fa-folder-open"></i>
            </div>
            <h3 class="text-slate-800 font-bold">Indicator Registry is Empty</h3>
            <p class="text-slate-500 text-sm mt-1">Start by creating a new performance metric or strategic plan</p>
        </div>
    </div>

</main>

<!-- Modals -->

<!-- Add/Edit IKU Modal -->
<div id="modal-iku" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl overflow-hidden animate-zoom-in relative border border-white/20">
            <div class="bg-slate-50 p-8 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h2 id="modal-title" class="text-xl font-bold text-slate-800">Create Indicator</h2>
                    <p class="text-slate-500 text-[11px] font-medium mt-1">Configure performance metric for the entire system</p>
                </div>
                <button class="close-modal w-10 h-10 flex items-center justify-center hover:bg-slate-100 rounded-xl transition-all">
                    <i class="fas fa-times text-slate-400"></i>
                </button>
            </div>
            <form id="form-iku" class="p-8 space-y-5">
                <input type="hidden" name="id">
                
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Indicator Classification</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="IKU" checked class="hidden peer">
                            <div class="w-full py-3.5 text-center border-2 border-slate-100 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 text-xs font-bold text-slate-400 peer-checked:text-blue-600 transition-all">IKU</div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="RENSTRA" class="hidden peer">
                            <div class="w-full py-3.5 text-center border-2 border-slate-100 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 text-xs font-bold text-slate-400 peer-checked:text-blue-600 transition-all">RENSTRA</div>
                        </label>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Name / Title</label>
                    <input type="text" name="nama" required placeholder="Enter metric name" class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Year / Period</label>
                        <input type="text" name="tahun_periode" placeholder="e.g. 2024" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">System Target (%)</label>
                        <input type="text" name="target" placeholder="e.g. 80%" required class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Indicator Description</label>
                    <textarea name="deskripsi" rows="3" required placeholder="Describe what this indicator measures..." class="w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 transition-all outline-none resize-none"></textarea>
                </div>

                <div class="pt-6 flex justify-end gap-3">
                    <button type="button" class="close-modal px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all">Cancel</button>
                    <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">Save Indicator</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="modal-delete-iku" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden animate-zoom-in relative">
            <div class="p-8 text-center space-y-6">
                <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto text-2xl">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Delete Permanently?</h3>
                    <p class="text-sm text-slate-500 mt-2">This indicator will be removed from all selection menus. This cannot be undone.</p>
                </div>
                <div class="flex flex-col gap-2">
                    <button id="confirm-delete-btn" class="w-full py-3 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 shadow-lg shadow-red-100 transition-all">Confirm Deletion</button>
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
    
    /* Toggle Switch */
    .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #e2e8f0; transition: .4s; border-radius: 24px; }
    .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #3b82f6; }
    input:checked + .slider:before { transform: translateX(20px); }
</style>

@push('scripts')
<script>
    window.dataIku = @json($list_iku);
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let dataIku = [...window.dataIku];
    
    const container = document.getElementById('iku-container');
    const emptyState = document.getElementById('empty-state');
    const searchInput = document.getElementById('search-iku');
    const statActive = document.getElementById('stat-active');
    const statTotal = document.getElementById('stat-total');
    
    const modalIku = document.getElementById('modal-iku');
    const modalDelete = document.getElementById('modal-delete-iku');
    const modalTitle = document.getElementById('modal-title');
    const formIku = document.getElementById('form-iku');
    const btnAdd = document.getElementById('btn-tambah-iku');
    const notif = document.getElementById('notif-success');
    
    let filteredData = [...dataIku];
    let deleteId = null;

    function render() {
        if (filteredData.length === 0) {
            container.innerHTML = '';
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
            container.innerHTML = filteredData.map(item => createCard(item)).join('');
        }
        updateStats();
        attachListeners();
    }

    function updateStats() {
        if (!statActive || !statTotal) return;
        statActive.textContent = dataIku.filter(i => i.status === 'Aktif').length;
        statTotal.textContent = dataIku.length;
    }

    function createCard(item) {
        const capaianNum = parseFloat(item.capaian);
        const targetNum = parseFloat(item.target);
        const progress = Math.min(100, (capaianNum / targetNum) * 100);
        
        return `
            <div class="bg-white rounded-2xl border border-slate-200 p-6 hover:shadow-lg transition-all group relative overflow-hidden">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 ${item.type === 'IKU' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-purple-50 text-purple-600 border-purple-100'} rounded-lg text-[9px] font-black uppercase tracking-widest border">
                            ${item.type}
                        </span>
                        <span class="text-[10px] font-bold text-slate-400">${item.tahun_periode}</span>
                    </div>
                    <label class="switch">
                        <input type="checkbox" class="status-toggle" data-id="${item.id}" ${item.status === 'Aktif' ? 'checked' : ''}>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="mb-6">
                    <h3 class="text-sm font-bold text-slate-800 leading-snug h-10 overflow-hidden line-clamp-2">${item.nama}</h3>
                    <p class="text-[11px] text-slate-400 mt-2 line-clamp-2 leading-relaxed">${item.deskripsi}</p>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Global Target</p>
                            <p class="text-xl font-bold text-slate-800">${item.capaian} <span class="text-xs text-slate-400 font-medium">/ ${item.target}</span></p>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] font-black text-slate-700">${Math.round(progress)}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-slate-50 h-1.5 rounded-full overflow-hidden border border-slate-50">
                        <div class="h-full bg-blue-600 transition-all duration-1000 shadow-[0_0_10px_rgba(59,130,246,0.2)]" style="width: ${progress}%"></div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-50 flex justify-end gap-2">
                    <button class="btn-edit h-9 px-4 rounded-xl bg-slate-50 text-slate-600 text-[11px] font-bold hover:bg-slate-800 hover:text-white transition-all flex items-center gap-2" data-id="${item.id}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-delete h-9 w-9 rounded-xl bg-slate-50 text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all flex items-center justify-center" data-id="${item.id}">
                        <i class="fas fa-trash-alt text-[10px]"></i>
                    </button>
                </div>
            </div>
        `;
    }

    function applyFilters() {
        const search = searchInput.value.toLowerCase();
        filteredData = dataIku.filter(item => 
            item.nama.toLowerCase().includes(search) || 
            item.type.toLowerCase().includes(search) ||
            item.tahun_periode.toLowerCase().includes(search)
        );
        render();
    }

    function showNotif(text) {
        document.getElementById('notif-text').textContent = text;
        notif.classList.remove('hidden');
        setTimeout(() => notif.classList.remove('translate-y-10', 'opacity-0'), 10);
        setTimeout(() => {
            notif.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => notif.classList.add('hidden'), 300);
        }, 3000);
    }

    function attachListeners() {
        document.querySelectorAll('.status-toggle').forEach(tg => {
            tg.addEventListener('change', function() {
                const id = this.dataset.id;
                const iku = dataIku.find(i => i.id == id);
                if (iku) {
                    const originalStatus = iku.status;
                    const newStatus = this.checked ? 'Aktif' : 'Non-Aktif';
                    
                    fetch(`{{ url('superadmin/buat-iku/toggle-status') }}/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            iku.status = data.status;
                            showNotif(data.message);
                            updateStats();
                        } else {
                            this.checked = originalStatus === 'Aktif';
                            showNotif('Failed to toggle status');
                        }
                    })
                    .catch(err => {
                        this.checked = originalStatus === 'Aktif';
                        showNotif('An error occurred');
                        console.error(err);
                    });
                }
            });
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const iku = dataIku.find(i => i.id == id);
                if (iku) {
                    modalTitle.textContent = `Update Indicator Profile`;
                    formIku.id.value = iku.id;
                    formIku.nama.value = iku.nama;
                    formIku.tahun_periode.value = iku.tahun_periode;
                    formIku.target.value = iku.target.replace('%', ''); // strip % if editing to avoid double-appending
                    formIku.deskripsi.value = iku.deskripsi;
                    const radio = formIku.querySelector(`input[name="type"][value="${iku.type}"]`);
                    if(radio) radio.checked = true;
                    modalIku.classList.remove('hidden');
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

    btnAdd.addEventListener('click', () => {
        modalTitle.textContent = 'Create New Indicator';
        formIku.reset();
        formIku.id.value = '';
        modalIku.classList.remove('hidden');
    });

    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            modalIku.classList.add('hidden');
            modalDelete.classList.add('hidden');
        });
    });

    formIku.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = formData.get('id');
        const type = formData.get('type');
        
        const url = id ? '{{ route("superadmin.iku.update") }}' : '{{ route("superadmin.iku.store") }}';
        const bodyData = {
            type: type,
            nama: formData.get('nama'),
            tahun_periode: formData.get('tahun_periode'),
            target: formData.get('target'),
            deskripsi: formData.get('deskripsi')
        };
        if (id) {
            bodyData.id = id;
        }

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(bodyData)
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            
            if (data.success) {
                const updatedIku = data.iku;
                if (id) {
                    const index = dataIku.findIndex(i => i.id == id);
                    if (index !== -1) {
                        dataIku[index] = updatedIku;
                    }
                } else {
                    dataIku.unshift(updatedIku);
                }
                modalIku.classList.add('hidden');
                applyFilters();
                showNotif(data.message);
            } else {
                showNotif(data.message || 'Validation failed or error occurred');
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            showNotif('An error occurred while saving');
            console.error(err);
        });
    });

    document.getElementById('confirm-delete-btn').addEventListener('click', function() {
        if (deleteId) {
            const deleteBtn = this;
            const originalText = deleteBtn.textContent;
            deleteBtn.disabled = true;
            deleteBtn.textContent = 'Deleting...';

            fetch(`{{ url('superadmin/buat-iku/destroy') }}/${deleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                deleteBtn.disabled = false;
                deleteBtn.textContent = originalText;
                
                if (data.success) {
                    dataIku = dataIku.filter(i => i.id != deleteId);
                    modalDelete.classList.add('hidden');
                    applyFilters();
                    showNotif(data.message);
                } else {
                    showNotif('Failed to delete indicator');
                }
            })
            .catch(err => {
                deleteBtn.disabled = false;
                deleteBtn.textContent = originalText;
                showNotif('An error occurred while deleting');
                console.error(err);
            });
        }
    });

    searchInput.addEventListener('input', applyFilters);

    render();
});
</script>
@endpush

@endsection
