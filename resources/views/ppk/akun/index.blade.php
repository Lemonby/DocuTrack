@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <div class="max-w-4xl mx-auto">

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 mb-6 rounded-r-xl shadow-sm animate-fade-in">
                <p class="text-emerald-700 text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-6 rounded-r-xl shadow-sm animate-fade-in">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li class="text-rose-700 text-sm font-semibold">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Profile Header --}}
        <div class="bg-gradient-to-r from-[#17A18A] via-[#006A9A] to-[#114177] rounded-2xl shadow-xl p-6 md:p-8 mb-6 text-white">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                {{-- Avatar --}}
                <div class="relative group flex-shrink-0">
                    @php
                    $defaultImage = 'https://ui-avatars.com/api/?name=' . urlencode($user->nama ?? 'User') . '&background=0D8ABC&color=fff&size=150';
                    $avatarUrl = $user->foto_profil ? asset('storage/' . $user->foto_profil) : $defaultImage;
                    @endphp
                    <div class="w-24 h-24 rounded-full ring-4 ring-white/30 shadow-lg overflow-hidden bg-cover bg-center"
                         style="background-image: url('{{ $avatarUrl }}')"></div>
                    
                    <form id="avatar-form" action="{{ url('/' . ($userRole ?? 'ppk') . '/akun/update') }}" method="POST" enctype="multipart/form-data" class="absolute inset-0 opacity-0 cursor-pointer">
                        @csrf
                        @method('PATCH')
                        <input type="file" name="foto_profil" accept="image/*" class="w-full h-full cursor-pointer" onchange="document.getElementById('avatar-form').submit()">
                    </form>
                    
                    <div class="absolute bottom-0 right-0 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md text-teal-600 pointer-events-none">
                        <i class="fas fa-camera text-xs"></i>
                    </div>
                </div>
                {{-- Info --}}
                <div class="text-center sm:text-left">
                    <h1 class="text-2xl md:text-3xl font-bold">{{ $user->nama ?? 'Pengguna' }}</h1>
                    <p class="text-white/80 mt-1 capitalize">{{ str_replace('_', ' ', $userRole ?? 'ppk') }} &bull; DocuTrack PNJ</p>
                    <div class="mt-3 flex flex-wrap gap-2 justify-center sm:justify-start">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-full text-xs font-medium backdrop-blur-sm">
                            <i class="fas fa-shield-alt"></i> Akun Aktif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Cards Row --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Nama Pengguna</p>
                    <p class="text-gray-800 font-semibold text-sm mt-0.5">{{ $user->nama ?? '-' }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user-tag text-teal-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Role</p>
                    <p class="text-gray-800 font-semibold text-sm mt-0.5 capitalize">{{ str_replace('_', ' ', $userRole ?? '-') }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-md border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-circle text-green-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Status</p>
                    <p class="text-green-700 font-semibold text-sm mt-0.5">Aktif</p>
                </div>
            </div>
        </div>

        {{-- Edit Profile Form --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-user-edit text-blue-600 text-sm"></i>
                </div>
                <h2 class="text-base font-bold text-gray-800">Informasi Profil</h2>
            </div>
            <div class="p-6">
                <form action="{{ url('/' . ($userRole ?? 'ppk') . '/akun/update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ $user->nama }}" required
                                class="w-full px-4 py-3 text-sm text-gray-800 bg-white rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                placeholder="Nama lengkap Anda">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" required
                                class="w-full px-4 py-3 text-sm text-gray-800 bg-white rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                placeholder="email@pnj.ac.id">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold text-sm shadow-md hover:from-blue-600 hover:to-blue-700 hover:-translate-y-0.5 transition-all duration-300">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fas fa-lock text-red-600 text-sm"></i>
                </div>
                <h2 class="text-base font-bold text-gray-800">Ubah Password</h2>
            </div>
            <div class="p-6">
                <form action="{{ url('/' . ($userRole ?? 'ppk') . '/akun/password') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Lama</label>
                            <div class="relative">
                                <input type="password" id="old_password" name="old_password" required
                                    class="w-full px-4 py-3 pr-10 text-sm rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePassword('old_password', 'eye-old')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i id="eye-old" class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                            <div class="relative">
                                <input type="password" id="new_password" name="new_password" required
                                    class="w-full px-4 py-3 pr-10 text-sm rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePassword('new_password', 'eye-new')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i id="eye-new" class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" id="confirm_password" name="confirm_password" required
                                    class="w-full px-4 py-3 pr-10 text-sm rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"
                                    placeholder="••••••••">
                                <button type="button" onclick="togglePassword('confirm_password', 'eye-confirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <i id="eye-confirm" class="fas fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-semibold text-sm shadow-md hover:from-red-600 hover:to-red-700 hover:-translate-y-0.5 transition-all duration-300">
                            <i class="fas fa-key"></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash text-sm';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye text-sm';
    }
}
</script>
@endsection
