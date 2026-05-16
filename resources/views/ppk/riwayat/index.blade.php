@extends('layouts.app')

@section('title', 'Riwayat Persetujuan (PPK)')

@section('content')
<main class="main-content font-poppins p-3 sm:p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="riwayat-section" class="bg-white rounded-2xl shadow-sm overflow-hidden mb-8 border border-slate-100 flex flex-col">
        
        {{-- Header --}}
        <div class="p-5 border-b border-slate-50 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-slate-50/50">
            <div>
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-history text-blue-600"></i> Riwayat Persetujuan
                </h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-1 italic">Daftar kegiatan yang telah diproses</p>
            </div>
            
            <div class="relative group">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-300 text-xs"></i>
                <input type="text" id="search-riwayat" placeholder="Cari kegiatan..."
                       class="pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all w-full sm:w-64">
            </div>
        </div>
        
        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">No</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Usulan & Pengusul</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Tgl. Proses</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Catatan</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody id="riwayat-table-body" class="divide-y divide-slate-100">
                    @forelse($riwayat_list as $index => $item)
                        @php
                            $status_lower = strtolower($item['status']);
                            $status_badge = match ($status_lower) {
                                'disetujui' => 'text-emerald-700 bg-emerald-100 border-emerald-200',
                                'ditolak'   => 'text-rose-700 bg-rose-100 border-rose-200',
                                'revisi'    => 'text-amber-700 bg-amber-100 border-amber-200',
                                default     => 'text-blue-700 bg-blue-100 border-blue-200',
                            };
                        @endphp
                        <tr class="riwayat-row hover:bg-slate-50 transition-colors" data-nama="{{ strtolower($item['nama'] . ' ' . $item['pengusul']) }}">
                            <td class="px-6 py-6 text-xs font-black text-slate-400">{{ $index + 1 }}.</td>
                            <td class="px-6 py-6">
                                <div class="text-xs font-black text-slate-800 uppercase tracking-tight">{{ $item['nama'] }}</div>
                                <div class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-widest italic">{{ $item['pengusul'] }} &bull; {{ $item['nim'] ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-6 text-[10px] font-bold text-slate-500 uppercase whitespace-nowrap">
                                <i class="far fa-calendar-check text-emerald-400 mr-2"></i>{{ \Carbon\Carbon::parse($item['tanggal_proses'])->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-6 text-[10px] font-bold text-slate-400 italic max-w-xs truncate">
                                "{{ $item['catatan'] ?: 'Tanpa catatan' }}"
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $status_badge }}">
                                    {{ $item['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <a href="/ppk/kegiatan/show/{{ $item['id'] }}" class="inline-flex items-center gap-2 bg-slate-100 text-slate-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all shadow-sm active:scale-95">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="6" class="text-center py-20 text-[10px] font-black text-slate-300 uppercase tracking-widest italic">
                                Belum ada riwayat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-riwayat');
    const rows = document.querySelectorAll('.riwayat-row');
    const empty = document.getElementById('empty-row');

    searchInput?.addEventListener('input', (e) => {
        const val = e.target.value.toLowerCase().trim();
        let visible = 0;

        rows.forEach(row => {
            const match = row.dataset.nama.includes(val);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        if (empty) empty.style.display = visible === 0 ? '' : 'none';
    });
});
</script>
@endsection
