<!-- popup_login.blade.php - RESPONSIVE VERSION FIXED -->
<div id="popup-login" class="popup-container fixed inset-0 z-[1000] {{ session()->has('login_error') || $errors->any() ? 'flex' : 'hidden' }} items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
    
    <!-- Main Container dengan Background SVG -->
    <div class="relative w-full max-w-[893px] rounded-[20px] md:rounded-[30px] overflow-hidden shadow-2xl bg-white" style="min-height: 400px; max-height: 90vh;">
        
        <!-- Background SVG - Di belakang konten -->
        <div class="absolute inset-0 w-full h-full z-0 overflow-hidden">
            <svg class="w-full h-full" viewBox="0 0 893 546" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
                <path d="M99.062 127.6C83.8525 75.875 26.6834 70.2949 0 73.9704V516.583C0 533.144 13.4198 546.573 29.9808 546.583L783.99 546C783.99 546 786.992 506.467 686.429 466.872C612.863 437.906 526.329 504.462 490.307 413.242C454.284 322.022 318.199 401.714 278.674 247.341C253.744 149.968 118.074 212.256 99.062 127.6Z" fill="url(#paint0_linear_3759_3896)"/>
                <path opacity="0.5" d="M104.495 71.1676C88.4515 12.2649 28.1469 5.91039 0 10.096V515.515C0 532.076 13.4189 545.504 29.9793 545.515L826.99 546.065C826.99 546.065 830.156 499.834 724.078 454.743C646.477 421.758 555.197 497.551 517.199 393.672C479.2 289.793 335.651 380.544 293.959 204.749C267.661 93.8643 124.55 164.796 104.495 71.1676Z" fill="url(#paint1_linear_3759_3896)"/>
                <path d="M3 26.9529C229.414 80.5364 205.578 200.456 247.998 253.676C301.023 320.202 615.667 411.042 494.305 270.382C372.943 129.723 383.718 -76.4871 486.082 53.8327C588.446 184.153 659.051 380.998 746.386 320.13C833.722 259.262 814.723 354.076 891 346.467" stroke="#18ADD8" stroke-opacity="0.5" stroke-width="24.7785"/>
                <path d="M3 53.8321C209.591 113.301 173.366 200.357 215.786 253.676C268.811 320.325 611.171 433.461 489.809 292.541C368.447 151.62 379.222 -54.9711 481.586 75.5901C583.95 206.151 654.555 403.361 741.89 342.381C829.226 281.4 810.227 376.389 886.504 368.767" stroke="#18ADD8" stroke-opacity="0.5" stroke-width="6.19463"/>
                <defs>
                    <linearGradient id="paint0_linear_3759_3896" x1="460.788" y1="355.749" x2="-0.200066" y2="546.623" gradientUnits="userSpaceOnUse">
                        <stop offset="0.066255" stop-color="#17A18A"/>
                        <stop offset="1" stop-color="#014565"/>
                    </linearGradient>
                    <linearGradient id="paint1_linear_3759_3896" x1="486.061" y1="330.976" x2="-8.69519" y2="520.852" gradientUnits="userSpaceOnUse">
                        <stop offset="0.066255" stop-color="#17A18A"/>
                        <stop offset="1" stop-color="#014565"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
        
        <!-- Ilustrasi Kiri - Hidden di mobile, visible di tablet+ -->
        <div class="absolute left-0 top-0 h-full w-[55%] hidden md:flex items-center justify-center p-8 z-10 overflow-hidden">
            <div class="w-full h-full flex items-center justify-center">
                <img src="{{ asset('assets/images/icon/kiri-login.svg') }}" alt="Login Illustration" class="w-auto h-auto max-w-full max-h-full object-contain" style="filter: drop-shadow(0 4px 20px rgba(0,0,0,0.1));">
            </div>
        </div>
        
        <!-- Login Card - Full width di mobile, 45% di desktop -->
        <div id="login-card" class="relative h-full w-full md:w-[45%] md:ml-auto p-6 sm:p-8 md:p-12 flex flex-col justify-center z-30 overflow-y-auto">
            
            <!-- Close Button -->
            <button onclick="closeLoginPopup()" 
                    class="absolute top-3 right-3 md:top-4 md:right-4 w-8 h-8 md:w-10 md:h-10 bg-red-500 hover:bg-red-600 rounded-full flex items-center justify-center text-white transition-all duration-300 hover:rotate-90 shadow-lg z-50">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            
            <!-- Content Form -->
            <div class="relative z-10">
                <h2 class="mb-6 md:mb-8 text-center text-3xl md:text-4xl font-bold text-[#0A2540]">Log In</h2>
                
                @if(session('login_error'))
                    <div class="mb-4 rounded bg-red-100 p-3 text-center text-sm text-red-700">
                        {{ session('login_error') }}
                    </div>
                @endif
                
                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="mb-4 rounded bg-red-100 p-3 text-center text-sm text-red-700">
                            {{ $error }}
                        </div>
                    @endforeach
                @endif
                
                <form action="{{ url('login') }}" method="POST" class="space-y-4 md:space-y-6">
                    @csrf
                    <!-- Email Input -->
                    <div class="input-group">
                        <div class="relative">
                            <input 
                                type="email" 
                                id="login-email" 
                                name="login_email" 
                                required 
                                class="w-full px-4 py-2.5 md:py-3 pr-12 text-base md:text-lg text-[#0A2540] bg-white border-2 border-[#E2E8F0] rounded-lg outline-none transition-all duration-300 focus:border-[#4299E1] focus:ring-4 focus:ring-blue-100" 
                                placeholder="Email"
                            >
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Input -->
                    <div class="input-group">
                        <div class="relative">
                            <input 
                                type="password" 
                                id="login-password" 
                                name="login_password" 
                                required 
                                class="w-full px-4 py-2.5 md:py-3 pr-12 text-base md:text-lg text-[#0A2540] bg-white border-2 border-[#E2E8F0] rounded-lg outline-none transition-all duration-300 focus:border-[#4299E1] focus:ring-4 focus:ring-blue-100" 
                                placeholder="Password"
                            >
                            <button 
                                type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 focus:outline-none z-10"
                            >
                                <svg id="eye-icon-login" class="w-4 h-4 md:w-5 md:h-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- CAPTCHA Input -->
                    <div class="input-group">
                        <label class="block text-sm font-medium text-[#0A2540] mb-2">
                            Kode Keamanan
                        </label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input 
                                    type="text" 
                                    id="captcha-code" 
                                    name="captcha_code" 
                                    required 
                                    maxlength="6"
                                    class="w-full px-4 py-2.5 md:py-3 text-base md:text-lg text-[#0A2540] bg-white border-2 border-[#E2E8F0] rounded-lg outline-none transition-all duration-300 focus:border-[#4299E1] focus:ring-4 focus:ring-blue-100 uppercase" 
                                    placeholder="Masukkan kode"
                                    autocomplete="off"
                                >
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <div class="relative bg-gray-100 rounded-lg overflow-hidden border-2 border-[#E2E8F0]">
                                <img 
                                    id="captcha-image" 
                                    src="{{ url('captcha') }}?t={{ time() }}" 
                                    alt="CAPTCHA" 
                                    class="h-[60px] w-[200px] object-cover"
                                >
                            </div>
                            <button 
                                type="button"
                                onclick="refreshCaptcha()"
                                class="p-2.5 bg-[#4299E1] hover:bg-[#3182CE] text-white rounded-lg transition-all duration-300 hover:shadow-lg"
                                title="Refresh CAPTCHA"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Masukkan kode yang terlihat pada gambar</p>
                    </div>
                    
                    <!-- Login Button dengan gradient dari SVG -->
                    <button 
                        type="submit" 
                        class="btn w-full rounded-full border-none p-3 md:p-4 text-base md:text-lg font-semibold text-white transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, #22D3EE 0%, #3B82F6 100%);"
                    >
                        Log in
                    </button>
                    
                    <!-- Lupa Password Link -->
                    <div class="text-center mt-4 md:mt-6">
                        <a href="#" class="text-xs md:text-sm text-[#0A2540] hover:text-[#22D3EE] transition-colors font-medium">
                            Lupa Password?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loginPasswordInput = document.getElementById('login-password');
        const eyeIconLogin = document.getElementById('eye-icon-login');
        const popupLogin = document.getElementById('popup-login');
        
        // Toggle Show Password
        window.togglePasswordVisibility = function() {
            if (loginPasswordInput && eyeIconLogin) {
                if (loginPasswordInput.type === 'password') {
                    loginPasswordInput.type = 'text';
                    eyeIconLogin.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    `;
                } else {
                    loginPasswordInput.type = 'password';
                    eyeIconLogin.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    `;
                }
            }
        };
        
        // Refresh CAPTCHA function
        window.refreshCaptcha = function() {
            const captchaImage = document.getElementById('captcha-image');
            const captchaInput = document.getElementById('captcha-code');
            if (captchaImage) {
                captchaImage.src = '{{ url("captcha") }}?t=' + new Date().getTime();
            }
            if (captchaInput) {
                captchaInput.value = '';
                captchaInput.focus();
            }
        };
        
        // Auto uppercase CAPTCHA input
        const captchaInput = document.getElementById('captcha-code');
        if (captchaInput) {
            captchaInput.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            });
        }
        
        // Function to open popup
        window.openLoginPopup = function() {
            if (popupLogin) {
                popupLogin.classList.remove('hidden');
                popupLogin.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        };
        
        // Function to close popup
        window.closeLoginPopup = function() {
            if (popupLogin) {
                popupLogin.classList.add('hidden');
                popupLogin.classList.remove('flex');
                document.body.style.overflow = '';
            }
        };
        
        // Close popup when clicking outside
        if (popupLogin) {
            popupLogin.addEventListener('click', function(e) {
                if (e.target === popupLogin) {
                    closeLoginPopup();
                }
            });
        }
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && popupLogin && !popupLogin.classList.contains('hidden')) {
                closeLoginPopup();
            }
        });
    });
</script>

<style>
/* Custom scrollbar untuk mobile */
#login-card::-webkit-scrollbar {
    width: 4px;
}

#login-card::-webkit-scrollbar-track {
    background: transparent;
}

#login-card::-webkit-scrollbar-thumb {
    background: rgba(66, 153, 225, 0.3);
    border-radius: 10px;
}

/* Smooth scroll behavior */
#login-card {
    scroll-behavior: smooth;
}

/* Memastikan input visible di atas background */
#login-card input,
#login-card button[type="submit"] {
    position: relative;
    z-index: 10;
}
</style>
