@extends('layouts.app')

@section('title', 'Detail Rincian Kegiatan')

@section('content')
@php
    $isEditable = (in_array(strtolower($status), ['telah diverifikasi', 'disetujui', 'revisi']));
    $statusColor = match(strtolower($status)) {
        'disetujui', 'selesai', 'lpj disetujui' => 'emerald',
        'revisi' => 'amber',
        'telah diverifikasi', 'review', 'menunggu' => 'blue',
        default => 'slate'
    };
@endphp

<main class="main-content font-poppins p-4 sm:p-6 lg:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full relative z-10 animate-fade-in">

    @if(strtolower($status) === 'revisi')
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-6 rounded-r-xl shadow-sm">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle text-amber-500 mt-1"></i>
                <div>
                    <h3 class="text-amber-800 font-bold text-sm">Perlu Perbaikan Data</h3>
                    <p class="text-amber-700 text-xs mt-1">Dokumen pendukung atau data penanggung jawab tidak valid. Mohon periksa kembali dan kirim ulang.</p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl shadow-sm flex items-center gap-3">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <p class="text-red-800 font-bold text-sm">Terdapat kesalahan pada form. Periksa field yang ditandai merah di bawah.</p>
        </div>
    @endif

    <section class="bg-white rounded-xl sm:rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
        
        <div class="px-6 sm:px-10 py-8 border-b border-slate-50 bg-gradient-to-br from-white to-slate-50/50 flex justify-between items-center">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-lg bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 text-[10px] font-black uppercase tracking-wider border border-{{ $statusColor }}-200">
                        {{ $status }}
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">
                    {{ $isEditable ? 'Lengkapi Rincian Kegiatan' : 'Detail Rincian Kegiatan' }}
                </h1>
                <p class="text-xs text-slate-400 mt-1 font-medium italic">ID KEGIATAN: #KGT-{{ str_pad($id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            <a href="{{ route('admin.kegiatan.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center hover:bg-slate-200 hover:text-slate-600 transition-all">
                <i class="fas fa-times"></i>
            </a>
        </div>
        
        @if($isEditable)
        <form id="rincian-kegiatan-form" action="{{ route('admin.kegiatan.store-rincian') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="kegiatan_id" value="{{ $id }}">

            <div class="px-6 sm:px-10 py-8 space-y-8">
                
                <!-- Penanggung Jawab Section -->
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-5 flex items-center gap-2">
                        <i class="fas fa-user-shield"></i> 
                        <span>Penanggung Jawab Kegiatan</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" name="penanggung_jawab"
                                value="{{ old('penanggung_jawab', $detail_data['penanggung_jawab'] ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl focus:ring-4 outline-none transition-all font-medium text-sm
                                    {{ $errors->has('penanggung_jawab') ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-50/50' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-50/50' }}">
                            @error('penanggung_jawab')
                                <p class="flex items-center gap-1.5 text-xs text-red-600 font-semibold mt-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700">NIM / NIP <span class="text-rose-500">*</span></label>
                            <input type="text" name="nim_nip_pj"
                                value="{{ old('nim_nip_pj', $detail_data['nim_nip_pj'] ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl focus:ring-4 outline-none transition-all font-mono text-sm
                                    {{ $errors->has('nim_nip_pj') ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-50/50' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-50/50' }}">
                            @error('nim_nip_pj')
                                <p class="flex items-center gap-1.5 text-xs text-red-600 font-semibold mt-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-50"></div>

                <!-- Waktu Section -->
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-5 flex items-center gap-2">
                        <i class="fas fa-calendar-day"></i> 
                        <span>Waktu Pelaksanaan</span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700">Tanggal Mulai <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', $detail_data['tanggal_mulai'] ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl focus:ring-4 outline-none transition-all text-sm
                                    {{ $errors->has('tanggal_mulai') ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-50/50' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-50/50' }}">
                            @error('tanggal_mulai')
                                <p class="flex items-center gap-1.5 text-xs text-red-600 font-semibold mt-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700">Tanggal Selesai <span class="text-rose-500">*</span></label>
                            <input type="date" name="tanggal_selesai"
                                value="{{ old('tanggal_selesai', $detail_data['tanggal_selesai'] ?? '') }}"
                                class="w-full px-4 py-3 bg-slate-50 border rounded-xl focus:ring-4 outline-none transition-all text-sm
                                    {{ $errors->has('tanggal_selesai') ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-50/50' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-50/50' }}">
                            @error('tanggal_selesai')
                                <p class="flex items-center gap-1.5 text-xs text-red-600 font-semibold mt-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-50"></div>

                <!-- File Section -->
                <div>
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-5 flex items-center gap-2">
                        <i class="fas fa-file-upload"></i> 
                        <span>Dokumen Pendukung</span>
                    </h3>
                    <div class="p-8 border-2 border-dashed rounded-2xl bg-slate-50 hover:bg-white transition-all cursor-pointer group relative
                        {{ $errors->has('surat_pengantar') ? 'border-red-400 hover:border-red-500' : 'border-slate-200 hover:border-blue-400' }}" id="dropzone">
                        <input type="file" name="surat_pengantar" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center group-hover:scale-110 transition-transform
                                {{ $errors->has('surat_pengantar') ? 'text-red-500' : 'text-blue-500' }}">
                                <i class="fas fa-cloud-upload-alt text-xl"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-bold text-slate-700" id="file-label">Unggah Surat Pengantar / Undangan</p>
                                <p class="text-[10px] text-slate-400 font-bold mt-1 uppercase tracking-widest">Format: PDF (Maks. 5MB)</p>
                            </div>
                        </div>
                    </div>
                    @error('surat_pengantar')
                        <p class="flex items-center gap-1.5 text-xs text-red-600 font-semibold mt-2">
                            <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="bg-slate-50 px-10 py-6 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ route('admin.kegiatan.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Batal</a>
                <button type="submit" class="px-10 py-3.5 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-100 hover:bg-blue-700 hover:-translate-y-0.5 transition-all text-sm">
                    SIMPAN & KIRIM <i class="fas fa-paper-plane ml-2 text-xs"></i>
                </button>
            </div>
        </form>
        @else
        {{-- Read Only View --}}
        <div class="px-6 sm:px-10 py-10 space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Penanggung Jawab</label>
                        <p class="text-lg font-black text-slate-800">{{ $detail_data['penanggung_jawab'] }}</p>
                        <p class="text-sm font-bold text-slate-500 mt-1">ID: {{ $detail_data['nim_nip_pj'] }}</p>
                    </div>
                    <div class="pt-6 border-t border-slate-50">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Waktu Pelaksanaan</label>
                        <div class="flex items-center gap-3">
                            <div class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-600">
                                <i class="far fa-calendar-alt mr-2"></i> {{ date('d M Y', strtotime($detail_data['tanggal_mulai'])) }}
                            </div>
                            <i class="fas fa-arrow-right text-slate-300 text-xs"></i>
                            <div class="px-4 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-600">
                                <i class="far fa-calendar-check mr-2"></i> {{ date('d M Y', strtotime($detail_data['tanggal_selesai'])) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center text-rose-500 mb-4">
                        <i class="fas fa-file-pdf text-3xl"></i>
                    </div>
                    <h4 class="text-sm font-black text-slate-800 mb-1">Surat Pengantar</h4>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">DOKUMEN_PENDUKUNG.PDF</p>
                    <button class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-colors shadow-sm">
                        <i class="fas fa-eye mr-1"></i> LIHAT BERKAS
                    </button>
                </div>
            </div>

            <div class="pt-10 border-t border-slate-50 flex justify-between items-center">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">"Data ini telah diverifikasi dan bersifat final."</p>
                <a href="{{ route('admin.kegiatan.index') }}" class="px-8 py-3 bg-slate-800 text-white text-xs font-black rounded-xl hover:bg-slate-900 transition-all shadow-lg shadow-slate-100">
                    KEMBALI KE LIST
                </a>
            </div>
        </div>
        @endif
    </section>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.querySelector('input[type="file"]');
    const dropzone = document.getElementById('dropzone');
    const fileLabelDefault = document.getElementById('file-label');

    if(fileInput && fileLabelDefault) {
        fileInput.addEventListener('change', (e) => {
            if (fileInput.files.length > 0) {
                fileLabelDefault.innerHTML = `<span class="text-emerald-600 font-semibold">${fileInput.files[0].name} siap diunggah</span>`;
                if(dropzone) dropzone.classList.add('border-emerald-400', 'bg-emerald-50/20');
            }
        });
    }

    const form = document.getElementById('rincian-kegiatan-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); 
            Swal.fire({
                title: 'Konfirmasi Simpan',
                text: "Apakah data rincian kegiatan sudah sesuai?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Cek Kembali',
                confirmButtonColor: '#2563eb'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }
});
</script>
<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.5s ease-out forwards; }
</style>
@endpush
