@extends('layouts.app')

@push('styles')
<style>
    select { color: #1f2937 !important; background-color: #ffffff !important; -webkit-appearance: none; appearance: none; }
    select option[value=""], select option[disabled] { color: #9ca3af !important; }
    select option:not([value=""]):not([disabled]) { color: #1f2937 !important; }
    @-moz-document url-prefix() { select { color: #1f2937 !important; } }
    .form-step { display: none; }
    .form-step.active { display: block; animation: fadeSlide 0.3s ease; }
    @keyframes fadeSlide { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
</style>
@endpush

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    @if(session('success_message'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700 font-medium">{{ session('success_message') }}</p>
        </div>
    </div>
    @endif

    @if(session('error_message'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 font-medium">{{ session('error_message') }}</p>
        </div>
    </div>
    @endif

    <section id="form-section">
        <form id="kak-form-element" action="{{ url('/admin/pengajuan-usulan/store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Stepper --}}
            <div id="stepper-container" class="bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden mb-8">
                <nav aria-label="Progress">
                    <ol class="flex items-center justify-between gap-2">
                        @foreach(['Data Pengusul', 'Strategi', 'IKU & Renstra', 'RAB'] as $i => $step)
                        <li class="flex flex-col items-center relative flex-1">
                            <div id="step-indicator-{{ $i+1 }}" class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold {{ $i === 0 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-gray-200 text-gray-500' }} transition-all duration-300">
                                <span>{{ $i+1 }}</span>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="text-xs font-semibold {{ $i === 0 ? 'text-blue-700' : 'text-gray-400' }}">{{ $step }}</span>
                            </div>
                            @if($i < 3)
                            <div class="absolute top-5 left-[55%] right-[-45%] h-0.5 bg-gray-200 -z-0"></div>
                            @endif
                        </li>
                        @endforeach
                    </ol>
                </nav>
            </div>

            <div class="form-content-wrapper relative min-h-[500px]">

                {{-- TAHAP 1: Data Pengusul --}}
                <div id="form-tahap-1" class="form-step active">
                    <section class="bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden">
                        <div class="mb-8">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 text-center">Input Data Pengusul / Pelaksana</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">

                                <div class="relative">
                                    <label for="nama_pengusul_step1" class="block text-sm font-medium text-gray-700 mb-2">Nama Pengusul</label>
                                    <input type="text" id="nama_pengusul_step1" name="nama_pengusul_step1" required
                                        class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600"
                                        placeholder="Masukkan nama pengusul">
                                </div>

                                <div class="relative">
                                    <label for="nim_nip" class="block text-sm font-medium text-gray-700 mb-2">NIM/NIP</label>
                                    <input type="text" id="nim_nip" name="nim_nip" required
                                        class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600"
                                        placeholder="Masukkan NIM atau NIP">
                                </div>

                                <div class="relative">
                                    <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                                    <div class="relative">
                                        <select id="jurusan" name="jurusan" required class="block w-full px-4 py-3.5 pr-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600 cursor-pointer">
                                            <option value="" disabled selected class="text-gray-500">Pilih Jurusan</option>
                                            <option value="Teknik Sipil">Teknik Sipil</option>
                                            <option value="Teknik Mesin">Teknik Mesin</option>
                                            <option value="Teknik Elektro">Teknik Elektro</option>
                                            <option value="Teknik Informatika dan Komputer">Teknik Informatika dan Komputer</option>
                                            <option value="Teknik Grafika dan Penerbitan">Teknik Grafika dan Penerbitan</option>
                                            <option value="Akuntansi">Akuntansi</option>
                                            <option value="Administrasi Niaga">Administrasi Niaga</option>
                                            <option value="Pascasarjana">Pascasarjana</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <label for="prodi" class="block text-sm font-medium text-gray-700 mb-2">Prodi</label>
                                    <div class="relative">
                                        <select id="prodi" name="prodi" required disabled class="block w-full px-4 py-3.5 pr-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600 disabled:bg-gray-100 disabled:text-gray-400 cursor-pointer">
                                            <option value="" disabled selected class="text-gray-500">Pilih Jurusan Terlebih Dahulu</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <label for="nama_kegiatan_step1" class="block text-sm font-medium text-gray-700 mb-2">Nama Kegiatan</label>
                                    <input type="text" id="nama_kegiatan_step1" name="nama_kegiatan_step1" required
                                        class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600"
                                        placeholder="Masukkan nama kegiatan">
                                </div>

                                <div class="md:col-span-2 relative">
                                    <label for="wadir_tujuan" class="block text-sm font-medium text-gray-700 mb-2">Wadir Tujuan</label>
                                    <div class="relative">
                                        <select id="wadir_tujuan" name="wadir_tujuan" required class="block w-full px-4 py-3.5 pr-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600 cursor-pointer">
                                            <option value="">Pilih Wadir Tujuan</option>
                                            <option value="1">Wadir 1 - Akademik</option>
                                            <option value="2">Wadir 2 - Umum & Keuangan</option>
                                            <option value="3">Wadir 3 - Kemahasiswaan</option>
                                            <option value="4">Wadir 4 - Kerjasama & Hubungan Luar</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="flex justify-end items-center mt-10 pt-6 border-t border-gray-200">
                            <button type="button" class="btn-nav btn-lanjut inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 hover:-translate-y-0.5 focus:outline-none transition-all duration-300" data-target-step="2" data-direction="next">
                                <span class="btn-text">Lanjut</span> <i class="fas fa-arrow-right btn-icon text-xs"></i>
                            </button>
                        </div>
                    </section>
                </div>

                {{-- TAHAP 2: Informasi Kegiatan --}}
                <div id="form-tahap-2" class="form-step">
                    <section class="bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden">
                        <h2 class="text-lg md:text-xl font-semibold text-gray-800 pb-3 mb-5 border-b border-gray-200">Informasi Dasar Kegiatan</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pengusul</label>
                                <input type="text" id="nama_pengusul" name="nama_pengusul" readonly
                                    class="block w-full px-4 py-3.5 text-sm text-gray-600 bg-gray-100 rounded-lg border border-gray-300 cursor-not-allowed">
                            </div>
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kegiatan</label>
                                <input type="text" id="nama_kegiatan_kak" name="nama_kegiatan" readonly
                                    class="block w-full px-4 py-3.5 text-sm text-gray-600 bg-gray-100 rounded-lg border border-gray-300 cursor-not-allowed">
                            </div>
                            <div class="md:col-span-2 relative">
                                <label for="gambaran_umum_kak" class="block text-sm font-medium text-gray-700 mb-2">Gambaran Umum</label>
                                <textarea id="gambaran_umum_kak" name="gambaran_umum" rows="5" required
                                    class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600"
                                    placeholder="Jelaskan gambaran umum kegiatan ini..."></textarea>
                            </div>
                            <div class="md:col-span-2 relative">
                                <label for="penerima_manfaat" class="block text-sm font-medium text-gray-700 mb-2">Penerima Manfaat</label>
                                <textarea id="penerima_manfaat" name="penerima_manfaat" rows="3" required
                                    class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600"
                                    placeholder="Siapa yang mendapat manfaat dari kegiatan ini?"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-10 pt-6 border-t border-gray-200">
                            <button type="button" class="btn-nav btn-kembali inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all" data-target-step="1" data-direction="prev">
                                <i class="fas fa-arrow-left btn-icon text-xs"></i> <span class="btn-text">Kembali</span>
                            </button>
                            <button type="button" class="btn-nav btn-lanjut inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 hover:-translate-y-0.5 transition-all duration-300" data-target-step="3" data-direction="next">
                                <span class="btn-text">Lanjut</span> <i class="fas fa-arrow-right btn-icon text-xs"></i>
                            </button>
                        </div>
                    </section>
                </div>

                {{-- TAHAP 3: IKU & Renstra --}}
                <div id="form-tahap-3" class="form-step">
                    <div class="bg-white rounded-lg shadow-lg p-4 md:p-10">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Indikator Kinerja Utama & Renstra</h2>
                        <div class="mb-6">
                            <label for="metode_pelaksanaan_kak" class="block text-sm font-medium text-gray-700 mb-2">Metode Pelaksanaan</label>
                            <textarea id="metode_pelaksanaan_kak" name="metode_pelaksanaan" rows="4" required
                                class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600"
                                placeholder="Jelaskan metode pelaksanaan kegiatan..."></textarea>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">Tahapan Pelaksanaan</label>
                            <div id="tahapan-container">
                                <!-- Repeater rows will be added here -->
                            </div>
                            <button type="button" id="tambah-tahapan" class="mt-2 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-plus"></i> Tambah Tahapan
                            </button>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">Indikator Keberhasilan per Bulan</label>
                            <div id="indikator-container">
                                <!-- Repeater rows will be added here -->
                            </div>
                            <button type="button" id="tambah-indikator" class="mt-2 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-plus"></i> Tambah Indikator
                            </button>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <label class="text-sm font-medium text-gray-700">IKU (Indikator Kinerja Utama) yang Dipilih:</label>
                            <div id="indicator-display-area" class="mt-2 flex flex-wrap items-center gap-2 p-3 min-h-[60px] w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 transition-colors">
                                <span id="indicator-tags-container" class="contents"></span>
                                <button type="button" id="open-indicator-modal-btn" class="ml-auto inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800 flex-shrink-0">
                                    <i class="fas fa-plus-circle"></i> Tambah atau Ubah
                                </button>
                            </div>
                        </div>

                        <input type="hidden" id="indikator_kinerja_hidden" name="indikator_kinerja" value="">
                        <div class="flex justify-between items-center mt-10 pt-6 border-t border-gray-200">
                            <button type="button" class="btn-nav btn-kembali inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all" data-target-step="2" data-direction="prev">
                                <i class="fas fa-arrow-left btn-icon text-xs"></i> <span class="btn-text">Kembali</span>
                            </button>
                            <button type="button" class="btn-nav btn-lanjut inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 hover:-translate-y-0.5 transition-all duration-300" data-target-step="4" data-direction="next">
                                <span class="btn-text">Lanjut</span> <i class="fas fa-arrow-right btn-icon text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- TAHAP 4: RAB --}}
                <div id="form-tahap-4" class="form-step">
                    <div class="bg-white rounded-lg shadow-lg p-4 md:p-10">
                        <div class="rab-header flex flex-col sm:flex-row justify-between items-start mb-6 gap-4">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800 flex-shrink-0">Rincian Anggaran Biaya (RAB)</h2>
                            <div class="rab-actions-wrapper relative w-full sm:w-auto self-end sm:self-center">
                                <div class="rab-actions flex justify-end gap-4">
                                    <button type="button" class="inline-flex items-center gap-2 px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-medium text-center text-white rounded-lg transition-all bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg" id="add-category-toggle-btn">
                                        <i class="fas fa-plus"></i> Tambah kategori
                                    </button>
                                </div>
                                <div class="category-popup absolute top-full right-0 mt-2 p-4 bg-white border border-gray-200 rounded-lg shadow-xl w-60 md:w-64 z-10 opacity-0 invisible -translate-y-2 transition-all" id="category-popup">
                                    <input type="text" id="new-category-name" placeholder="Tulis Kategori Baru" class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" class="w-full mt-2 px-4 py-2 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all focus:outline-none focus:ring-2 focus:ring-blue-300" id="create-category-btn">Buat Kategori</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="rab-main flex flex-col md:flex-row gap-4 md:gap-6">
                            <div class="category-sidebar flex-shrink-0 w-full md:w-60 bg-gray-50 rounded-lg p-2.5 overflow-x-auto whitespace-nowrap md:overflow-visible md:whitespace-normal" id="category-sidebar">
                                <div class="flex md:flex-col gap-2 md:gap-0">
                                    <!-- Sidebar categories will be rendered here -->
                                </div>
                            </div>
                            <div class="rab-content flex-grow" id="rab-content">
                                <!-- RAB items will be rendered here -->
                            </div>
                        </div>

                        <div class="grand-total-container justify-between items-center bg-gray-50 p-4 md:p-6 rounded-lg border-2 border-dashed border-gray-200 mt-8 flex">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-calculator text-blue-500 text-2xl"></i>
                                <h3 class="text-lg md:text-xl font-bold text-gray-800">Grand Total</h3>
                            </div>
                            <span class="text-xl md:text-2xl font-bold text-blue-600" id="grand-total-display">Rp 0</span>
                        </div>
                        <input type="hidden" name="rab_data" id="rab_data_input" value="[]">
                        <div class="flex justify-between items-center mt-10 pt-6 border-t border-gray-200">
                            <button type="button" class="btn-nav btn-kembali inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all" data-target-step="3" data-direction="prev">
                                <i class="fas fa-arrow-left btn-icon text-xs"></i> <span class="btn-text">Kembali</span>
                            </button>
                            <button type="submit" class="btn-nav inline-flex items-center gap-2 px-6 py-2.5 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition-all">
                                <i class="fas fa-check-circle"></i> Simpan & Ajukan
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <div id="indicator-modal-backdrop" class="fixed inset-0 bg-black/60 z-[1010] hidden opacity-0 transition-opacity duration-300"></div>
    <div id="indicator-modal-content" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl z-[1020] w-[95%] max-w-lg hidden opacity-0 scale-95 transition-all duration-300">
        <div class="flex justify-between items-center p-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-bullseye text-blue-600"></i> Pilih Indikator Kinerja Utama
            </h3>
            <button id="close-indicator-modal-btn" class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 hover:bg-gray-100 rounded-lg"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-5">
            <div class="relative mb-4">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="search" id="indicator-search-input" placeholder="Cari indikator..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div id="indicator-list-container" class="max-h-[350px] overflow-y-auto pr-2 space-y-2 custom-scrollbar">
                <!-- Indicators will be rendered here -->
            </div>
        </div>
        <div class="flex justify-end p-5 bg-gray-50 border-t border-gray-100 rounded-b-xl gap-3">
            <button id="cancel-indicator-modal-btn" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">Batal</button>
            <button id="done-indicator-modal-btn" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-md shadow-blue-100 transition-all">Simpan Pilihan</button>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.allIndicators = {!! json_encode($all_ikus->pluck('indikator_kinerja')->toArray()) !!};
</script>
<script src="{{ asset('assets/js/admin/pengajuan-usulan.js') }}"></script>
@endpush
