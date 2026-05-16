@extends('layouts.app')

@section('content')
<main class="main-content font-poppins px-4 py-8 md:p-10 -mt-10 md:-mt-24 max-w-5xl mx-auto w-full">

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Sidebar Profil -->
        <div class="w-full md:w-1/3">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 sticky top-4">
                <div class="h-24 bg-gradient-to-r from-blue-600 to-indigo-700"></div>
                <div class="px-6 pb-6">
                    <div class="relative flex justify-center">
                        <div class="absolute -top-12 w-24 h-24 rounded-2xl bg-white p-1 shadow-lg">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=0D8ABC&color=fff&size=128" 
                                 alt="Avatar" class="w-full h-full rounded-xl object-cover">
                        </div>
                    </div>
                    <div class="mt-14 text-center">
                        <h3 class="text-xl font-bold text-gray-900">{{ $userName }}</h3>
                        <p class="text-sm text-blue-600 font-medium capitalize mt-1">{{ str_replace('_', ' ', $userRole) }}</p>
                        <div class="mt-4 flex items-center justify-center gap-2 px-3 py-1.5 bg-blue-50 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                            <span class="text-xs font-semibold text-blue-700">Akun Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Edit -->
        <div class="w-full md:w-2/3 space-y-6">
            {{-- Form Profil --}}
            <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-100">
                <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                        <i class="fas fa-user-edit text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Informasi Profil</h3>
                </div>

                <form action="{{ route('verifikator.akun.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ $userName }}" required
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none text-sm">
                        </div>
                        <div class="space-y-1 opacity-60">
                            <label class="text-sm font-semibold text-gray-700 ml-1">Role</label>
                            <input type="text" value="{{ $userRole }}" disabled
                                   class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl text-sm cursor-not-allowed">
                        </div>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 shadow-md transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Form Password --}}
            <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-100">
                <div class="flex items-center gap-3 mb-6 border-b border-gray-50 pb-4">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-red-600">
                        <i class="fas fa-lock text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Ubah Kata Sandi</h3>
                </div>

                <form action="{{ route('verifikator.akun.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Kata Sandi Baru</label>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700 ml-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none text-sm">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-red-600 text-white rounded-xl font-semibold text-sm hover:bg-red-700 shadow-md transition-all">
                            Ubah Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</main>
@endsection
