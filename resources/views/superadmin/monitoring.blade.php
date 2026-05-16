@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-6 lg:p-8 -mt-8 md:-mt-20 max-w-[1400px] mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-8 p-6 rounded-2xl bg-white/70 backdrop-blur-xl border border-white shadow-sm relative overflow-hidden">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-indigo-600 text-white rounded-xl shadow-lg">
                <i class="fas fa-desktop text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Monitoring Sistem</h1>
                <p class="text-sm text-gray-500">Pantau seluruh aktivitas pengajuan di DocuTrack</p>
            </div>
        </div>
    </div>

    <!-- Monitoring Content (Placeholder for identical legacy design) -->
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 text-center">
        <div class="mb-4">
            <i class="fas fa-tools text-5xl text-indigo-200"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-800">Halaman Monitoring Sedang Diintegrasikan</h2>
        <p class="text-gray-500 mt-2">Halaman ini akan menampilkan dashboard monitoring real-time seperti pada versi legacy.</p>
    </div>
</main>
@endsection
