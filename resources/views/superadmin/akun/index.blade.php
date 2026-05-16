@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-2xl mx-auto w-full">

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="p-8 text-center bg-indigo-900 text-white">
            <div class="w-24 h-24 rounded-full bg-white/10 backdrop-blur-md mx-auto mb-4 border-4 border-white/30 flex items-center justify-center">
                <i class="fas fa-shield-alt text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold">{{ session('user_name') }}</h3>
            <p class="text-indigo-200 text-sm uppercase tracking-widest font-semibold mt-1">Super Administrator</p>
        </div>
        
        <div class="p-8 space-y-6">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email Admin</label>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                    <i class="fas fa-envelope text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-700">{{ session('email') }}</span>
                </div>
            </div>
            
            <div class="pt-4">
                <button class="w-full py-3.5 bg-indigo-50 text-indigo-600 rounded-xl font-bold hover:bg-indigo-100 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-key"></i> Pengaturan Keamanan
                </button>
            </div>
        </div>
    </div>

</main>
@endsection
