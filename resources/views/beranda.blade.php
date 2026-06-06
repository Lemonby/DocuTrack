<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes floatDelay {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes floatCenter {
            0%, 100% { transform: translate(-50%, 0px); }
            50% { transform: translate(-50%, -20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        .float-delay {
            animation: floatDelay 3s ease-in-out 0.5s infinite;
        }
        .float-center {
            animation: floatCenter 3s ease-in-out 1s infinite;
        }
        .custom-shadow {
            filter: drop-shadow(0 6px 25px rgba(34, 211, 238, 0.7)) 
                    drop-shadow(0 12px 45px rgba(39, 75, 143, 0.6))
                    drop-shadow(0 2px 15px rgba(51, 171, 160, 0.5));
        }
        
        /* Cards Hover Effect */
        .feature-card {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        /* Mobile Menu Animation */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .mobile-menu.active {
            max-height: 500px;
        }

        /* Hamburger Icon Animation */
        .hamburger span {
            display: block;
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 5px 0;
            transition: 0.3s;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        /* Responsive Background Visibility - Only Hero Section */
        @media (max-width: 768px) {
            #home .bg-decoration {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navigation - Fully Responsive -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-in-out py-3 md:py-4" id="mainNavbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            
            <div id="navbarInner" class="transition-all duration-500 ease-in-out px-4 sm:px-6 lg:px-8 py-3 md:py-4 
                bg-transparent rounded-none border-transparent">
                
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <a href="/docutrack/public/">
                            <svg width="195" height="47" viewBox="0 0 195 47" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-32 h-auto sm:w-40 md:w-48 lg:w-[195px]">     
                                <g filter="url(#filter0_d_194_31)"><path d="M179.658 26.2678C174.418 24.2673 168.504 26.2678 166.917 27.1013C172.632 26.625 173.585 27.1013 178.586 29.3638C185.139 32.3283 189.104 26.1884 190.136 24.0054C188.787 25.2358 184.897 28.2682 179.658 26.2678Z" fill="white" stroke="white" stroke-width="0.341658"/><path d="M176.085 30.555C171.227 28.2687 168.305 27.7368 167.393 28.0543C169.536 29.7214 174.085 33.4127 177.99 34.3653C181.896 35.3179 186.405 32.5395 188.231 30.0786C185.968 31.6663 180.943 32.8412 176.085 30.555Z" fill="white" stroke="white" stroke-width="0.341658"/><path d="M180.149 21.1713C174.482 19.2149 168.93 23.8971 166.95 26.1927C168.071 24.8985 171.909 22.6991 178.292 24.2551C184.675 25.8111 188.749 22.1279 189.998 20.2702C189.304 20.6662 186.106 23.2277 180.149 21.1713Z" fill="white" stroke="white" stroke-width="0.341658"/><ellipse cx="183.468" cy="13.527" rx="4.64394" ry="4.64394" fill="#FF0000"/><path d="M4.39161 10.9131H13.753C16.3724 10.9131 18.6274 11.3914 20.5179 12.3481C22.4084 13.3047 23.8547 14.6827 24.8569 16.4821C25.8591 18.2815 26.3602 20.434 26.3602 22.9395C26.3602 25.445 25.8591 27.6088 24.8569 29.431C23.8775 31.2304 22.4425 32.6084 20.552 33.565C18.6615 34.5217 16.3952 35 13.753 35H4.39161V10.9131ZM13.3772 30.6951C18.5021 30.6951 21.0645 28.1099 21.0645 22.9395C21.0645 17.7918 18.5021 15.218 13.3772 15.218H9.72147V30.6951H13.3772ZM38.1029 35.2733C36.3262 35.2733 34.766 34.9203 33.4221 34.2142C32.0783 33.4853 31.0419 32.4717 30.313 31.1734C29.5842 29.8524 29.2197 28.3035 29.2197 26.5269C29.2197 24.7503 29.5842 23.2128 30.313 21.9145C31.0419 20.5934 32.0783 19.5798 33.4221 18.8737C34.766 18.1676 36.3262 17.8146 38.1029 17.8146C39.8795 17.8146 41.4397 18.1676 42.7836 18.8737C44.1274 19.5798 45.1638 20.5934 45.8927 21.9145C46.6215 23.2128 46.986 24.7503 46.986 26.5269C46.986 28.3035 46.6215 29.8524 45.8927 31.1734C45.1638 32.4717 44.1274 33.4853 42.7836 34.2142C41.4397 34.9203 39.8795 35.2733 38.1029 35.2733ZM38.1029 31.3443C40.6083 31.3443 41.8611 29.7385 41.8611 26.5269C41.8611 24.9097 41.5308 23.7025 40.8703 22.9053C40.2325 22.1081 39.31 21.7095 38.1029 21.7095C35.5974 21.7095 34.3446 23.3153 34.3446 26.5269C34.3446 29.7385 35.5974 31.3443 38.1029 31.3443ZM58.1203 35.2733C55.387 35.2733 53.2346 34.4989 51.6629 32.9501C50.0913 31.4012 49.3055 29.2943 49.3055 26.6294C49.3055 24.8755 49.6813 23.3381 50.433 22.017C51.1846 20.6731 52.2438 19.6368 53.6104 18.9079C54.977 18.179 56.56 17.8146 58.3594 17.8146C59.5894 17.8146 60.7738 18.0082 61.9127 18.3954C63.0515 18.7599 63.974 19.2609 64.6801 19.8987L63.3135 23.4178C62.6529 22.8939 61.9241 22.4953 61.1269 22.222C60.3524 21.9259 59.5894 21.7778 58.8378 21.7778C57.4939 21.7778 56.4462 22.1764 55.6945 22.9736C54.9656 23.7708 54.6012 24.9553 54.6012 26.5269C54.6012 28.0985 54.9656 29.2943 55.6945 30.1143C56.4462 30.9115 57.4939 31.3101 58.8378 31.3101C59.5894 31.3101 60.3524 31.1734 61.1269 30.9001C61.9241 30.604 62.6529 30.194 63.3135 29.6701L64.6801 33.2234C63.9285 33.8611 62.9718 34.3622 61.8102 34.7267C60.6486 35.0911 59.4186 35.2733 58.1203 35.2733ZM83.1686 18.2246V35H78.1463V32.6084C77.6224 33.4739 76.9277 34.1345 76.0621 34.59C75.2194 35.0456 74.2627 35.2733 73.1922 35.2733C71.0739 35.2733 69.4909 34.6925 68.4432 33.5309C67.4182 32.3465 66.9057 30.5698 66.9057 28.201V18.2246H72.0647V28.3035C72.0647 29.3057 72.2697 30.046 72.6797 30.5243C73.1125 31.0026 73.7616 31.2418 74.6272 31.2418C75.6294 31.2418 76.438 30.9001 77.0529 30.2168C77.6907 29.5335 78.0096 28.6338 78.0096 27.5177V18.2246H83.1686ZM96.4204 31.4809C96.9443 31.4809 97.491 31.4468 98.0604 31.3784L97.7871 35.1367C97.1265 35.2278 96.466 35.2733 95.8054 35.2733C93.2544 35.2733 91.3867 34.7153 90.2022 33.5992C89.0406 32.4831 88.4598 30.7862 88.4598 28.5085V22.0853H85.2824V18.2246H88.4598V13.3047H93.6188V18.2246H97.8212V22.0853H93.6188V28.4743C93.6188 30.4787 94.5527 31.4809 96.4204 31.4809ZM112.332 22.0512L109.428 22.3587C107.993 22.4953 106.979 22.9053 106.387 23.5886C105.795 24.2492 105.499 25.1375 105.499 26.2536V35H100.34V18.2246H105.294V21.0603C106.137 19.1243 107.879 18.0651 110.521 17.8829L112.025 17.7804L112.332 22.0512ZM121.501 17.8146C124.007 17.8146 125.852 18.4068 127.036 19.5912C128.243 20.7756 128.847 22.6092 128.847 25.0919V35H123.961V32.5059C123.619 33.3714 123.05 34.0547 122.253 34.5558C121.456 35.0342 120.522 35.2733 119.451 35.2733C118.312 35.2733 117.276 35.0456 116.342 34.59C115.431 34.1345 114.702 33.4967 114.155 32.6767C113.632 31.8567 113.37 30.9457 113.37 29.9435C113.37 28.7135 113.677 27.7455 114.292 27.0394C114.93 26.3333 115.943 25.8208 117.333 25.5019C118.722 25.183 120.624 25.0236 123.039 25.0236H123.927V24.4086C123.927 23.4064 123.71 22.7003 123.278 22.2903C122.845 21.8803 122.093 21.6753 121.023 21.6753C120.203 21.6753 119.292 21.8234 118.289 22.1195C117.287 22.4156 116.331 22.8256 115.42 23.3495L114.053 19.8987C115.01 19.3065 116.183 18.8168 117.572 18.4296C118.984 18.0196 120.294 17.8146 121.501 17.8146ZM120.579 31.7201C121.581 31.7201 122.389 31.3898 123.004 30.7293C123.619 30.046 123.927 29.169 123.927 28.0985V27.5177H123.346C121.501 27.5177 120.203 27.6657 119.451 27.9618C118.722 28.2579 118.358 28.7932 118.358 29.5676C118.358 30.1826 118.563 30.6951 118.973 31.1051C119.406 31.5151 119.941 31.7201 120.579 31.7201ZM140.699 35.2733C137.966 35.2733 135.813 34.4989 134.241 32.9501C132.67 31.4012 131.884 29.2943 131.884 26.6294C131.884 24.8755 132.26 23.3381 133.011 22.017C133.763 20.6731 134.822 19.6368 136.189 18.9079C137.556 18.179 139.139 17.8146 140.938 17.8146C142.168 17.8146 143.352 18.0082 144.491 18.3954C145.63 18.7599 146.553 19.2609 147.259 19.8987L145.892 23.4178C145.231 22.8939 144.503 22.4953 143.705 22.222C142.931 21.9259 142.168 21.7778 141.416 21.7778C140.072 21.7778 139.025 22.1764 138.273 22.9736C137.544 23.7708 137.18 24.9553 137.18 26.5269C137.18 28.0985 137.544 29.2943 138.273 30.1143C139.025 30.9115 140.072 31.3101 141.416 31.3101C142.168 31.3101 142.931 31.1734 143.705 30.9001C144.503 30.604 145.231 30.194 145.892 29.6701L147.259 33.2234C146.507 33.8611 145.55 34.3622 144.389 34.7267C143.227 35.0911 141.997 35.2733 140.699 35.2733ZM161.169 35L154.746 27.5177V35H149.587V10.9131H154.746V25.3994L160.93 18.2588H167.08L160.041 26.2536L167.49 35H161.169Z" fill="#33ABA0"/></g><defs><filter id="filter0_d_194_31" x="0.391602" y="8.88306" width="193.899" height="34.3904" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4"/><feGaussianBlur stdDeviation="2"/><feComposite in2="hardAlpha" operator="out"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_194_31"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_194_31" result="shape"/></filter></defs></svg>
                        </a>
                    </div>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden lg:flex items-center gap-6 xl:gap-8">
                        <a href="#home" class="text-white hover:text-teal-200 transition font-medium tracking-wide text-sm xl:text-base">HOME</a>
                        <a href="#about" class="text-white hover:text-teal-200 transition font-medium tracking-wide text-sm xl:text-base">ABOUT</a>
                        <a href="#detail" class="text-white hover:text-teal-200 transition font-medium tracking-wide text-sm xl:text-base">DETAIL</a>
                        <a href="#proses" class="text-white hover:text-teal-200 transition font-medium tracking-wide text-sm xl:text-base">PROSES</a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="lg:hidden hamburger text-white p-2 focus:outline-none">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>

                <!-- Mobile Menu -->
                <div id="mobileMenu" class="mobile-menu lg:hidden">
                    <div class="flex flex-col gap-4 pt-4 pb-2">
                        <a href="#home" class="text-white hover:text-teal-200 transition font-medium tracking-wide text-sm py-2 border-b border-white/10">HOME</a>
                        <a href="#about" class="text-white hover:text-teal-200 transition font-medium tracking-wide text-sm py-2 border-b border-white/10">ABOUT</a>
                        <a href="#detail" class="text-white hover:text-teal-200 transition font-medium tracking-wide text-sm py-2 border-b border-white/10">DETAIL</a>
                        <a href="#proses" class="text-white hover:text-teal-200 transition font-medium tracking-wide text-sm py-2">PROSES</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main><!-- Hero Section - Fully Responsive -->
<section id="home" class="relative min-h-screen flex items-center pb-12 sm:pb-16 md:pb-20 overflow-hidden pt-20 md:pt-0">
    
    <div class="absolute inset-0 bg-[linear-gradient(225deg,#014565_0%,#014565_35%,#00FFBC_100%)] z-0"></div>

    <!-- Background SVG - Hidden on Mobile -->
    <div class="bg-decoration absolute inset-0 z-[1] pointer-events-none overflow-hidden">
        <img src="/assets/images/background/hero-sec.svg" 
            alt="Hero Background" 
            class="w-full h-full object-cover opacity-50">
    </div>

    <!-- PNJ Logo - Hidden on Mobile -->
    <div class="bg-decoration absolute left-0 top-0 bottom-0 w-1/2 z-[2] opacity-20 pointer-events-none">
    <div class="w-full h-full bg-gradient-to-r from-black/50 to-transparent flex items-start justify-start">
        <img src="/assets/images/logo/pnj.png" 
            alt="logo-pnj" 
            class="w-[700px] max-w-full h-auto -ml-4 -mt-4">
    </div>
</div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            
            <!-- Text Content -->
            <div class="text-white text-center lg:text-left order-2 lg:order-1">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 sm:mb-6 leading-tight drop-shadow-lg">
                    Sistem<br>
                    Pengajuan TOR<br>
                    & Kegiatan PNJ
                </h1>
                <p class="text-base sm:text-lg text-gray-100 mb-6 sm:mb-8 max-w-md mx-auto lg:mx-0 leading-relaxed">
                    Ajukan, Pantau dan kelola dokumen TOR Anda secara online, cepat, transparant, dan efesien.
                </p>
                <button onclick="openLoginPopup()" class="group bg-gradient-to-tl from-[#3B82F6] to-[#22D3EE] text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold shadow-[0_10px_20px_rgba(0,0,0,0.2)] transition-all duration-300 transform hover:-translate-y-1 text-sm sm:text-base">
                    Log In
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>

            <!-- Image Content -->
            <div class="relative perspective-1000 order-1 lg:order-2">
                <div class="p-2 sm:p-4 relative z-10">
                    <img src="/assets/images/icon/orang-main-laptop.png" 
                        alt="Document Management" 
                        class="w-full rounded-2xl max-w-md mx-auto lg:max-w-full">
                </div>

                <!-- Floating Icons - Hidden on mobile, visible on tablet+ -->
                <div class="hidden sm:block absolute float-animation top-4 left-4 z-20">
                    <img src="/assets/images/icon/kiri-atas-hero.png" 
                        alt="kiri-atas-hero" 
                        class="w-16 md:w-20 lg:w-[100px]">
                </div>

                <div class="hidden md:block absolute float-animation top-1/2 -translate-y-1/2 -left-4 lg:-left-8 z-20">
                    <img src="/assets/images/icon/kiri-hero.png" 
                        alt="kiri-hero" 
                        class="w-16 lg:w-[100px]">
                </div>

                <div class="hidden sm:block absolute float-animation bottom-1 left-4 z-20">
                    <img src="/assets/images/icon/kiri-bawah-hero.png" 
                        alt="kiri-bawah-hero" 
                        class="w-16 md:w-20 lg:w-[100px]">
                </div>

                <div class="hidden sm:block absolute float-animation bottom-1 -right-2 lg:-right-4 z-20">
                    <img src="/assets/images/icon/kanan-bawah-hero.png" 
                        alt="kanan-bawah-hero" 
                        class="w-16 md:w-20 lg:w-[100px]">
                </div>

                <div class="hidden sm:block absolute float-animation top-4 right-4 z-20">
                    <img src="/assets/images/icon/kanan-atas-hero.png" 
                        alt="kanan-atas-hero" 
                        class="w-16 md:w-20 lg:w-[100px]">
                </div>
                
                <!-- Status Cards - Adjusted for mobile -->
                <div class="absolute top-4 left-1/4 -translate-x-1/4 sm:left-1/3 sm:-translate-x-1/3 bg-[#014565] text-white rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xl border border-white/10 z-20 text-xs sm:text-sm">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="bg-white/20 p-1.5 sm:p-2 rounded-full">
                            <i class="fas fa-file-alt text-base sm:text-xl text-[#00FFBC]"></i>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-xs font-light text-gray-300">Status</p>
                            <p class="text-xs sm:text-sm font-bold">Approved</p>
                        </div>
                    </div>
                </div>
                
                <div class="absolute bottom-20 sm:bottom-32 -right-2 sm:-right-4 bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xl z-20 text-xs sm:text-sm max-w-[140px] sm:max-w-none">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="bg-yellow-100 p-1.5 sm:p-2 rounded-full flex-shrink-0">
                            <i class="fas fa-check-circle text-yellow-500 text-base sm:text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-xs font-bold text-gray-800">Total Pengajuan</p>
                            <p class="text-[10px] sm:text-xs text-gray-500">1,234 Dokumen</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Bottom -->
    <div class="absolute bottom-0 left-0 right-0 z-[5]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-auto block">
            <path fill="#f9fafb" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,197.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</section>

<!-- About Section - Fully Responsive with Corner-to-Corner Content -->
<section id="about" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gray-50 relative overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute inset-0 z-0 pointer-events-none hidden md:block">
            <img src="/assets/images/background/about-sec.svg" 
                alt="About Background" 
                class="w-full h-full object-cover opacity-20">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Main Laptop Image with Floating Cards -->
            <div class="relative h-[500px] sm:h-[600px] lg:h-[700px] mb-0 lg:mb-2">
                
                <!-- Top Left Card -->
                <div class="absolute sm:top[15%] lg:top-[-10%] left-[20%] z-20 float-animation ">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-xl sm:rounded-2xl lg:rounded-3xl p-3 sm:p-7 lg:p-6 w-20 sm:w-28 lg:w-36">
                            <img src="/assets/images/icon/kiri-about.png" 
                                alt="Chart" 
                                class="w-10 sm:w-14 lg:w-18 mx-auto drop-shadow-md">
                        </div>
                        <div class="absolute -bottom-1.5 sm:-bottom-2 left-4 sm:left-6 lg:left-8 w-5 sm:w-6 lg:w-8 h-5 sm:h-6 lg:h-8 bg-[#274B8F] rotate-45 rounded-sm -z-10"></div>
                    </div>
                </div>

                <!-- Top Right Card -->
                <div class="absolute sm:top[15%] lg:top-[-10%] right-[20%] z-20 float-delay">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-xl sm:rounded-2xl lg:rounded-3xl p-3 sm:p-4 lg:p-6 w-20 sm:w-28 lg:w-36">
                            <img src="/assets/images/icon/kanan-about.svg" 
                                alt="Team" 
                                class="w-12 sm:w-16 lg:w-20 h-auto mx-auto drop-shadow-md">
                        </div>
                        <div class="absolute -bottom-1.5 sm:-bottom-2 left-4 sm:left-6 lg:left-8 w-5 sm:w-6 lg:w-8 h-5 sm:h-6 lg:h-8 bg-[#274B8F] rotate-45 rounded-sm -z-10"></div>
                    </div>
                </div>

                <!-- Center Top Card -->
                <div class="absolute sm:top[15%] lg:top-[-10%] left-[50%] z-20 float-center">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-xl sm:rounded-2xl lg:rounded-3xl p-3 sm:p-4 lg:p-6 w-20 sm:w-28 lg:w-36">
                            <img src="/assets/images/icon/tengah-about.png" 
                                alt="Document" 
                                class="w-10 sm:w-14 lg:w-18 mx-auto drop-shadow-md">
                        </div>
                        <div class="absolute -bottom-1.5 sm:-bottom-2 left-4 sm:left-6 lg:left-8 w-5 sm:w-6 lg:w-8 h-5 sm:h-6 lg:h-8 bg-[#274B8F] rotate-45 rounded-sm -z-10"></div>
                    </div>
                </div>

                <!-- Main Laptop Image - Larger and Center positioned -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[450px] sm:max-w-[600px] lg:max-w-[800px] z-10">
                    <img src="/assets/images/icon/laptop-about.svg" 
                        alt="Laptop About" 
                        class="w-full h-auto drop-shadow-2xl">
                </div>
            </div>

            <!-- Content Section -->
            <div class="relative z-10 max-w-4xl mx-auto">
                <div class="text-center p-6 sm:p-8 lg:p-12">

                    <!-- Content -->
                    <div class="relative z-10">
                        <div class="mb-4 sm:mb-6 inline-block custom-shadow">
                            <img src="/assets/images/logo/docutrack-about.svg" 
                            alt="About Background" 
                            class="h-12 sm:h-16 lg:h-20">
                        </div>
                         

                        <p class="text-base sm:text-lg lg:text-xl xl:text-2xl leading-relaxed bg-[#274B8F] bg-clip-text text-transparent custom-shadow max-w-2xl mx-auto">
                            DocuTrack adalah platform digital yang mempermudah pengajuan dan pelacakan ToR (Term of Reference) untuk proyek kegiatan di lingkungan kampus yang terintegrasi. Semua proses pengajuan, verifikasi, hingga persetujuan dilakukan secara online, efisien, dan transparan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- Features Section - Fully Responsive with Better Text Spacing -->
<section id="detail" class="relative w-full min-h-screen bg-gray-50 overflow-hidden flex items-center py-12 sm:py-16 md:py-20">
    
    <!-- Background -->
    <div class="absolute top-0 right-0 h-full w-full lg:w-1/2 z-0 pointer-events-none opacity-30 lg:opacity-100">
        <img src="/assets/images/background/detail-sec.svg" 
            alt="Detail Background" 
            class="w-full h-full object-cover">
    </div>

    <div class="relative z-10 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
                
                <!-- Feature Cards -->
                <div class="w-full lg:w-3/5 order-2 lg:order-1">
                    
                    <div class="grid sm:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                        
                        <div class="relative bg-gradient-to-tl from-[#274B8F] to-[#22D3EE] text-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-2xl overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                            <div class="relative z-10">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 mb-3 sm:mb-4 bg-white/70 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-paper-plane text-xl sm:text-2xl text-black"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Pengajuan TOR Online</h3>
                                <p class="text-white/95 text-xs sm:text-sm leading-relaxed">
                                    Ajukan TOR kapan saja tanpa kertas. Cukup isi form dan upload dokumen.
                                </p>
                            </div>
                            <div class="absolute -bottom-8 sm:-bottom-12 -right-8 sm:-right-12 w-32 sm:w-40 h-32 sm:h-40 bg-white/10 rounded-full"></div>
                        </div>

                        <div class="relative bg-gradient-to-tl from-[#274B8F] to-[#22D3EE] text-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-2xl overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                            <div class="relative z-10">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 mb-3 sm:mb-4 bg-white/70 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-search text-xl sm:text-2xl text-black"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Tracking Status</h3>
                                <p class="text-white/95 text-xs sm:text-sm leading-relaxed">
                                    Pantau proses pengajuan TOR anda secara real-time dan transparant.
                                </p>
                            </div>
                            <div class="absolute -bottom-8 sm:-bottom-12 -right-8 sm:-right-12 w-32 sm:w-40 h-32 sm:h-40 bg-white/10 rounded-full"></div>
                        </div>

                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 sm:gap-6">
                        
                        <div class="relative bg-white border border-gray-100 rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                            <div class="relative z-10">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 mb-3 sm:mb-4 bg-gray-100 rounded-xl flex items-center justify-center text-gray-700">
                                    <i class="fas fa-clock text-xl sm:text-2xl"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-800">Hemat Waktu</h3>
                                <p class="text-gray-600 text-xs sm:text-sm leading-relaxed">
                                    Dengan adanya sistem digital untuk pengajuan TOR dan LPJ, proses manual dapat dipangkas signifikan. Pengajuan lebih cepat tanpa tatap muka.
                                </p>
                            </div>
                            <div class="absolute -bottom-8 sm:-bottom-12 -right-8 sm:-right-12 w-32 sm:w-40 h-32 sm:h-40 bg-gray-50 rounded-full"></div>
                        </div>

                        <div class="relative bg-white border border-gray-100 rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                            <div class="relative z-10">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 mb-3 sm:mb-4 bg-gray-100 rounded-xl flex items-center justify-center text-gray-700">
                                    <i class="fas fa-database text-xl sm:text-2xl"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-800">Data Terstruktur</h3>
                                <p class="text-gray-600 text-xs sm:text-sm leading-relaxed">
                                    Semua data pengajuan TOR/LPJ tersimpan rapi. Riwayat pengajuan terdokumentasi dengan baik untuk audit dan pelacakan.
                                </p>
                            </div>
                            <div class="absolute -bottom-8 sm:-bottom-12 -right-8 sm:-right-12 w-32 sm:w-40 h-32 sm:h-40 bg-gray-50 rounded-full"></div>
                        </div>

                    </div>
                </div>

                <!-- Title Section - Moved further right with better responsive control -->
                <div class="w-full lg:w-2/5 text-center lg:text-right order-1 lg:order-2 lg:pr-8 xl:pr-12">
                    <div class="lg:max-w-none lg:ml-auto lg:pl-8 xl:pl-16 2xl:pl-24">
                        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-[38px] xl:text-[42px] 2xl:text-[46px] font-bold text-gray-800 lg:text-white leading-[1.2] drop-shadow-2xl">
                            Other features &<br>advantages
                        </h2>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Process Section - Wind Flow Theme -->
<section id="proses" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white relative overflow-hidden">
    
    <!-- Animated Wind Background -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <!-- Decorative Circles -->
        <div class="absolute top-10 left-10 w-64 h-64 bg-gradient-to-br from-teal-100/30 to-cyan-100/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 right-20 w-96 h-96 bg-gradient-to-br from-blue-100/25 to-teal-100/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-1/4 w-80 h-80 bg-gradient-to-br from-cyan-100/30 to-blue-100/25 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 right-1/3 w-72 h-72 bg-gradient-to-br from-teal-100/20 to-cyan-100/30 rounded-full blur-3xl"></div>
        
        <!-- Floating Clouds -->
        <div class="cloud cloud-1 absolute w-32 h-16 bg-teal-200/15 rounded-full blur-2xl"></div>
        <div class="cloud cloud-2 absolute w-24 h-12 bg-cyan-200/12 rounded-full blur-2xl"></div>
        <div class="cloud cloud-3 absolute w-40 h-20 bg-blue-200/15 rounded-full blur-2xl"></div>
        
        <!-- Wind Lines -->
        <svg class="absolute inset-0 w-full h-full opacity-20" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="windGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#0d9488;stop-opacity:0" />
                    <stop offset="50%" style="stop-color:#0891b2;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#0e7490;stop-opacity:0" />
                </linearGradient>
            </defs>
            <path class="wind-line wind-line-1" d="M-100 100 Q 200 120, 500 100 T 1100 100" stroke="url(#windGradient)" stroke-width="2" fill="none"/>
            <path class="wind-line wind-line-2" d="M-100 300 Q 200 280, 500 300 T 1100 300" stroke="url(#windGradient)" stroke-width="2" fill="none"/>
            <path class="wind-line wind-line-3" d="M-100 500 Q 200 520, 500 500 T 1100 500" stroke="url(#windGradient)" stroke-width="2" fill="none"/>
        </svg>
        
        <!-- Decorative Dots Pattern -->
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, #0d9488 1px, transparent 1px); background-size: 30px 30px;"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center mb-12 sm:mb-16 md:mb-20">
            <div class="inline-block mb-4 floating">
            </div>
            <div class="inline-block mb-4">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold px-8 py-4 text-transparent bg-clip-text bg-gradient-to-r from-teal-700 via-cyan-700 to-blue-700 custom-shadow">
                    Tahapan Pengajuan
                </h2>
            </div>
        </div>

        <!-- Flowing Path Container -->
        <div class="relative">
            
            <!-- Curved SVG Path for Desktop -->
            <svg class="hidden lg:block absolute inset-0 w-full h-full pointer-events-none" style="height: 2800px;">
                <defs>
                    <linearGradient id="pathGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#0d9488;stop-opacity:0.4" />
                        <stop offset="50%" style="stop-color:#0e7490;stop-opacity:0.6" />
                        <stop offset="100%" style="stop-color:#0891b2;stop-opacity:0.4" />
                    </linearGradient>
                </defs>
                <!-- Flowing S-curve path -->
                <path class="flowing-path" 
                      d="M 100 50 
                         Q 300 150, 500 250 
                         Q 700 350, 500 450 
                         Q 300 550, 500 650
                         Q 700 750, 500 850
                         Q 300 950, 500 1050
                         Q 700 1150, 500 1250
                         Q 300 1350, 500 1450
                         Q 700 1550, 500 1650
                         Q 300 1750, 500 1850
                         Q 700 1950, 500 2050
                         Q 300 2150, 500 2250
                         Q 700 2350, 500 2450
                         Q 300 2550, 400 2650" 
                      stroke="url(#pathGradient)" 
                      stroke-width="3" 
                      fill="none" 
                      stroke-dasharray="10 5"
                      opacity="0.5"/>
            </svg>

            <!-- Mobile Curved Path -->
            <div class="lg:hidden absolute left-12 top-0 bottom-0 w-0.5">
                <div class="w-full h-full bg-gradient-to-b from-teal-400 via-cyan-500 to-blue-500 opacity-40 rounded-full"></div>
            </div>

            <!-- Process Steps with Wind Flow Animation -->
            <div class="space-y-8 sm:space-y-12 md:space-y-16 lg:space-y-24">
                
                <!-- Step 1 - Pengajuan Kegiatan -->
                <div class="relative lg:ml-0" style="animation-delay: 0.1s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-teal-600 to-teal-700 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    1
                                </div>
                                <div class="absolute inset-0 bg-teal-500/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-teal-500/30 to-teal-600/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-teal-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-600 to-teal-700 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-paper-plane text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pengajuan Kegiatan</h3>
                                            <span class="px-3 py-1 bg-teal-50 rounded-full text-teal-700 text-xs font-semibold border border-teal-200">
                                                <i class="far fa-user mr-1"></i>Pengusul
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pengusul membuat dan mengajukan proposal kegiatan melalui sistem secara online
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2 - Verifikasi -->
                <div class="relative lg:ml-auto lg:mr-0 lg:max-w-3xl" style="animation-delay: 0.2s;">
                    <div class="flex items-start gap-4 lg:gap-6 flex-row-reverse lg:flex-row">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-teal-700 to-cyan-700 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    2
                                </div>
                                <div class="absolute inset-0 bg-teal-600/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-teal-600/30 to-cyan-700/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-teal-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-700 to-cyan-700 rounded-2xl flex items-center justify-center shadow-lg order-2 sm:order-1">
                                        <i class="fas fa-check-double text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1 order-1 sm:order-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Verifikasi Pengajuan</h3>
                                            <span class="px-3 py-1 bg-teal-50 rounded-full text-teal-700 text-xs font-semibold border border-teal-200">
                                                <i class="fas fa-user-check mr-1"></i>Verifikator
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Verifikator memeriksa kelengkapan dan keabsahan dokumen pengajuan dengan teliti
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 - Komitmen PPK -->
                <div class="relative lg:ml-0" style="animation-delay: 0.3s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-cyan-700 to-cyan-800 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    3
                                </div>
                                <div class="absolute inset-0 bg-cyan-600/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-cyan-600/30 to-cyan-800/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-cyan-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-cyan-700 to-cyan-800 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-handshake text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pembuatan Komitmen</h3>
                                            <span class="px-3 py-1 bg-cyan-50 rounded-full text-cyan-700 text-xs font-semibold border border-cyan-200">
                                                <i class="fas fa-user-tie mr-1"></i>PPK
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pejabat Pembuat Komitmen membuat komitmen anggaran untuk mendukung kegiatan
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4 - Persetujuan Wadir -->
                <div class="relative lg:ml-auto lg:mr-0 lg:max-w-3xl" style="animation-delay: 0.4s;">
                    <div class="flex items-start gap-4 lg:gap-6 flex-row-reverse lg:flex-row">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-cyan-800 to-sky-800 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    4
                                </div>
                                <div class="absolute inset-0 bg-cyan-700/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-cyan-700/30 to-sky-800/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-cyan-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-cyan-800 to-sky-800 rounded-2xl flex items-center justify-center shadow-lg order-2 sm:order-1">
                                        <i class="fas fa-stamp text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1 order-1 sm:order-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Persetujuan Final</h3>
                                            <span class="px-3 py-1 bg-cyan-50 rounded-full text-cyan-700 text-xs font-semibold border border-cyan-200">
                                                <i class="fas fa-user-shield mr-1"></i>Wakil Direktur
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Wakil Direktur memberikan persetujuan akhir untuk pelaksanaan kegiatan
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5 - Penyiapan Dana -->
                <div class="relative lg:ml-0" style="animation-delay: 0.5s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-sky-700 to-blue-800 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    5
                                </div>
                                <div class="absolute inset-0 bg-sky-700/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-sky-700/30 to-blue-800/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-sky-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-sky-700 to-blue-800 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-money-check-alt text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Penyiapan Dana</h3>
                                            <span class="px-3 py-1 bg-sky-50 rounded-full text-sky-700 text-xs font-semibold border border-sky-200">
                                                <i class="fas fa-wallet mr-1"></i>Bendahara
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Bendahara memproses dan menyiapkan dana sesuai anggaran yang telah disetujui
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 6 - Pelaksanaan Kegiatan -->
                <div class="relative lg:ml-auto lg:mr-0 lg:max-w-3xl" style="animation-delay: 0.6s;">
                    <div class="flex items-start gap-4 lg:gap-6 flex-row-reverse lg:flex-row">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-blue-700 to-blue-900 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    6
                                </div>
                                <div class="absolute inset-0 bg-blue-700/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-blue-700/30 to-blue-900/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-blue-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-700 to-blue-900 rounded-2xl flex items-center justify-center shadow-lg order-2 sm:order-1">
                                        <i class="fas fa-tasks text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1 order-1 sm:order-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pelaksanaan Kegiatan</h3>
                                            <span class="px-3 py-1 bg-blue-50 rounded-full text-blue-700 text-xs font-semibold border border-blue-200">
                                                <i class="far fa-user mr-1"></i>Pengusul
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pengusul menerima dana dan melaksanakan kegiatan sesuai dengan rencana yang disetujui
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 7 - Pembuatan LPJ -->
                <div class="relative lg:ml-0" style="animation-delay: 0.7s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-blue-800 to-indigo-900 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    7
                                </div>
                                <div class="absolute inset-0 bg-blue-800/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-blue-800/30 to-indigo-900/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-blue-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-800 to-indigo-900 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-file-invoice text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pembuatan LPJ</h3>
                                            <span class="px-3 py-1 bg-blue-50 rounded-full text-blue-700 text-xs font-semibold border border-blue-200">
                                                <i class="far fa-user mr-1"></i>Pengusul
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pengusul menyusun laporan pertanggungjawaban atas kegiatan yang telah dilaksanakan
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 8 - Verifikasi LPJ -->
                <div class="relative lg:ml-auto lg:mr-0 lg:max-w-3xl" style="animation-delay: 0.8s;">
                    <div class="flex items-start gap-4 lg:gap-6 flex-row-reverse lg:flex-row">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-indigo-800 to-cyan-900 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    8
                                </div>
                                <div class="absolute inset-0 bg-indigo-800/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-indigo-800/30 to-cyan-900/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-indigo-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-800 to-cyan-900 rounded-2xl flex items-center justify-center shadow-lg order-2 sm:order-1">
                                        <i class="fas fa-clipboard-check text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1 order-1 sm:order-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pemeriksaan LPJ</h3>
                                            <span class="px-3 py-1 bg-indigo-50 rounded-full text-indigo-700 text-xs font-semibold border border-indigo-200">
                                                <i class="fas fa-wallet mr-1"></i>Bendahara
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Bendahara memeriksa kelengkapan dan kesesuaian LPJ dengan realisasi anggaran
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 9 - Penyerahan Hard Copy -->
                <div class="relative lg:ml-0" style="animation-delay: 0.9s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-teal-700 to-cyan-800 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    9
                                </div>
                                <div class="absolute inset-0 bg-teal-700/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-teal-700/30 to-cyan-800/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-teal-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-700 to-cyan-800 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-box-open text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Penyerahan Dokumen</h3>
                                            <span class="px-3 py-1 bg-teal-50 rounded-full text-teal-700 text-xs font-semibold border border-teal-200">
                                                <i class="far fa-user mr-1"></i>Pengusul
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pengusul menyerahkan hard copy LPJ sebagai dokumentasi dan arsip fisik
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Completion Badge with Wind Effect -->
            <div class="mt-16 sm:mt-20 lg:mt-24 flex justify-center">
                <div class="relative floating">
                    <div class="inline-flex items-center gap-3 sm:gap-4 px-6 sm:px-8 lg:px-10 py-4 sm:py-5 lg:py-6 bg-gradient-to-r from-teal-700 via-cyan-700 to-teal-700 rounded-3xl shadow-2xl text-white">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-2xl sm:text-3xl lg:text-4xl"></i>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-medium opacity-90">Proses Selesai</p>
                            <p class="text-base sm:text-lg lg:text-xl font-bold">Kegiatan Tuntas</p>
                        </div>
                    </div>
                    <!-- Glow effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-teal-700 via-cyan-700 to-teal-700 rounded-3xl blur-xl opacity-50 -z-10"></div>
                </div>
            </div>

        </div>

    </div>
</section>

<style>
/* Custom Shadow for Title */
.custom-shadow {
    filter: drop-shadow(0 6px 25px rgba(34, 211, 238, 0.7)) 
            drop-shadow(0 12px 45px rgba(39, 75, 143, 0.6))
            drop-shadow(0 2px 15px rgba(51, 171, 160, 0.5));
}

/* Wind Flow Animations */
@keyframes cloudFloat {
    0%, 100% {
        transform: translateX(-100px) translateY(0);
    }
    50% {
        transform: translateX(calc(100vw + 100px)) translateY(-20px);
    }
}

@keyframes windFlow {
    0% {
        stroke-dashoffset: 1000;
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        stroke-dashoffset: 0;
        opacity: 0;
    }
}

@keyframes floating {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes ping-slow {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

/* Cloud animations */
.cloud-1 {
    top: 10%;
    animation: cloudFloat 40s infinite linear;
}

.cloud-2 {
    top: 30%;
    animation: cloudFloat 50s infinite linear 5s;
}

.cloud-3 {
    top: 60%;
    animation: cloudFloat 45s infinite linear 10s;
}

/* Wind line animations */
.wind-line-1 {
    animation: windFlow 8s infinite ease-in-out;
}

.wind-line-2 {
    animation: windFlow 8s infinite ease-in-out 2s;
}

.wind-line-3 {
    animation: windFlow 8s infinite ease-in-out 4s;
}

/* Flowing path animation */
.flowing-path {
    stroke-dasharray: 20 10;
    animation: dashFlow 3s linear infinite;
}

@keyframes dashFlow {
    to {
        stroke-dashoffset: -30;
    }
}

/* Card float animations */
.wind-card-float {
    animation: floating 3s ease-in-out infinite;
}

.wind-card-float-content {
    animation: floating 3s ease-in-out infinite 0.2s;
}

.floating {
    animation: floating 4s ease-in-out infinite;
}

/* Ping animation */
.animate-ping-slow {
    animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 500ms;
}

/* Hover effects */
.group:hover .group-hover\:translate-x-1 {
    transform: translateX(0.25rem);
}

.group:hover .group-hover\:-translate-y-1 {
    transform: translateY(-0.25rem);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .wind-card-float,
    .wind-card-float-content {
        animation: none;
    }
}
</style><!-- popup_login.php - RESPONSIVE VERSION FIXED -->
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
                <img src="/assets/images/icon/kiri-login.svg" alt="Login Illustration" class="w-auto h-auto max-w-full max-h-full object-contain" style="filter: drop-shadow(0 4px 20px rgba(0,0,0,0.1));">
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
                    <div class="mb-4 rounded bg-red-100 p-3 text-center text-sm text-red-700 font-semibold shadow-sm">
                        {{ session('login_error') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="mb-4 rounded bg-red-100 p-3 text-center text-sm text-red-700 font-semibold shadow-sm">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <form action="{{ url('login') }}" method="POST" class="space-y-4 md:space-y-6">
                    @csrf
                    
                    <!-- Email Input -->
                    <div class="input-group">
                        <div class="relative">
                            <input 
                                type="email" 
                                id="login-email" 
                                name="email" 
                                value="{{ old('email') }}"
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
                                name="password" 
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
                                    src="<?= url('captcha') ?>?t=<?= time() ?>" 
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
                captchaImage.src = '<?= url("captcha") ?>?t=' + new Date().getTime();
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
</style></main>

    <footer class="relative w-full text-white bg-white">
    
    <div class="absolute inset-0 w-full h-full z-0 overflow-hidden pointer-events-none">
    <div class="absolute top-0 h-full w-full min-w-[1200px] left-1/2 -translate-x-1/2 lg:w-full lg:min-w-0 lg:left-0 lg:translate-x-0">
        <!-- SVG untuk Desktop (lg ke atas) -->
        <svg class="hidden lg:block w-full h-full" viewBox="0 0 1440 534" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M916.136 106.928C531.374 -88.8183 240.667 34.5848 0 83.8337V534H1440V83.8337C1304.74 133.083 1065.74 183.038 916.136 106.928Z" fill="url(#paint_footer_gradient_desktop)"/>
            <defs>
                <linearGradient id="paint_footer_gradient_desktop" x1="720" y1="0" x2="720" y2="534" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#114177"/>
                    <stop offset="0.5" stop-color="#006A9A"/>
                    <stop offset="1" stop-color="#17A18A"/>
                </linearGradient>
            </defs>
        </svg>
        
        <!-- SVG untuk Mobile (di bawah lg) - Full dengan Lengkungan Kecil -->
        <svg class="block lg:hidden w-full h-full" viewBox="0 0 1440 534" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0 80C240 50 480 40 720 50C960 60 1200 70 1440 80V534H0V80Z" fill="url(#paint_footer_gradient_mobile)"/>
            <defs>
                <linearGradient id="paint_footer_gradient_mobile" x1="720" y1="40" x2="720" y2="534" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#114177"/>
                    <stop offset="0.5" stop-color="#006A9A"/>
                    <stop offset="1" stop-color="#17A18A"/>
                </linearGradient>
            </defs>
        </svg>
    </div>
</div>

    <div class="relative z-10 w-full px-6 pt-44 pb-10 sm:px-10 sm:pt-48 sm:pb-12 lg:px-20 lg:pt-52">
        
        <div class="max-w-7xl w-full mx-auto">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-y-12 sm:gap-x-8 lg:gap-x-12 mb-12">

                <div class="sm:col-span-2 lg:col-span-5 flex flex-col items-center sm:items-start text-center sm:text-left space-y-6">
                    <a href="/docutrack/public/" class="block hover:opacity-90 transition-opacity">
                        <svg width="195" height="47" viewBox="0 0 195 47" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 sm:w-48 lg:w-[195px] h-auto">
                           <g filter="url(#filter_logo_shadow)">
                                <path d="M179.658 26.2678C174.418 24.2673 168.504 26.2678 166.917 27.1013C172.632 26.625 173.585 27.1013 178.586 29.3638C185.139 32.3283 189.104 26.1884 190.136 24.0054C188.787 25.2358 184.897 28.2682 179.658 26.2678Z" fill="white" stroke="white" stroke-width="0.341658"/>
                                <path d="M176.085 30.555C171.227 28.2687 168.305 27.7368 167.393 28.0543C169.536 29.7214 174.085 33.4127 177.99 34.3653C181.896 35.3179 186.405 32.5395 188.231 30.0786C185.968 31.6663 180.943 32.8412 176.085 30.555Z" fill="white" stroke="white" stroke-width="0.341658"/>
                                <path d="M180.149 21.1713C174.482 19.2149 168.93 23.8971 166.95 26.1927C168.071 24.8985 171.909 22.6991 178.292 24.2551C184.675 25.8111 188.749 22.1279 189.998 20.2702C189.304 20.6662 186.106 23.2277 180.149 21.1713Z" fill="white" stroke="white" stroke-width="0.341658"/>
                                <ellipse cx="183.468" cy="13.527" rx="4.64394" ry="4.64394" fill="#FF0000"/>
                                <path d="M4.39161 10.9131H13.753C16.3724 10.9131 18.6274 11.3914 20.5179 12.3481C22.4084 13.3047 23.8547 14.6827 24.8569 16.4821C25.8591 18.2815 26.3602 20.434 26.3602 22.9395C26.3602 25.445 25.8591 27.6088 24.8569 29.431C23.8775 31.2304 22.4425 32.6084 20.552 33.565C18.6615 34.5217 16.3952 35 13.753 35H4.39161V10.9131ZM13.3772 30.6951C18.5021 30.6951 21.0645 28.1099 21.0645 22.9395C21.0645 17.7918 18.5021 15.218 13.3772 15.218H9.72147V30.6951H13.3772ZM38.1029 35.2733C36.3262 35.2733 34.766 34.9203 33.4221 34.2142C32.0783 33.4853 31.0419 32.4717 30.313 31.1734C29.5842 29.8524 29.2197 28.3035 29.2197 26.5269C29.2197 24.7503 29.5842 23.2128 30.313 21.9145C31.0419 20.5934 32.0783 19.5798 33.4221 18.8737C34.766 18.1676 36.3262 17.8146 38.1029 17.8146C39.8795 17.8146 41.4397 18.1676 42.7836 18.8737C44.1274 19.5798 45.1638 20.5934 45.8927 21.9145C46.6215 23.2128 46.986 24.7503 46.986 26.5269C46.986 28.3035 46.6215 29.8524 45.8927 31.1734C45.1638 32.4717 44.1274 33.4853 42.7836 34.2142C41.4397 34.9203 39.8795 35.2733 38.1029 35.2733ZM38.1029 31.3443C40.6083 31.3443 41.8611 29.7385 41.8611 26.5269C41.8611 24.9097 41.5308 23.7025 40.8703 22.9053C40.2325 22.1081 39.31 21.7095 38.1029 21.7095C35.5974 21.7095 34.3446 23.3153 34.3446 26.5269C34.3446 29.7385 35.5974 31.3443 38.1029 31.3443ZM58.1203 35.2733C55.387 35.2733 53.2346 34.4989 51.6629 32.9501C50.0913 31.4012 49.3055 29.2943 49.3055 26.6294C49.3055 24.8755 49.6813 23.3381 50.433 22.017C51.1846 20.6731 52.2438 19.6368 53.6104 18.9079C54.977 18.179 56.56 17.8146 58.3594 17.8146C59.5894 17.8146 60.7738 18.0082 61.9127 18.3954C63.0515 18.7599 63.974 19.2609 64.6801 19.8987L63.3135 23.4178C62.6529 22.8939 61.9241 22.4953 61.1269 22.222C60.3524 21.9259 59.5894 21.7778 58.8378 21.7778C57.4939 21.7778 56.4462 22.1764 55.6945 22.9736C54.9656 23.7708 54.6012 24.9553 54.6012 26.5269C54.6012 28.0985 54.9656 29.2943 55.6945 30.1143C56.4462 30.9115 57.4939 31.3101 58.8378 31.3101C59.5894 31.3101 60.3524 31.1734 61.1269 30.9001C61.9241 30.604 62.6529 30.194 63.3135 29.6701L64.6801 33.2234C63.9285 33.8611 62.9718 34.3622 61.8102 34.7267C60.6486 35.0911 59.4186 35.2733 58.1203 35.2733ZM83.1686 18.2246V35H78.1463V32.6084C77.6224 33.4739 76.9277 34.1345 76.0621 34.59C75.2194 35.0456 74.2627 35.2733 73.1922 35.2733C71.0739 35.2733 69.4909 34.6925 68.4432 33.5309C67.4182 32.3465 66.9057 30.5698 66.9057 28.201V18.2246H72.0647V28.3035C72.0647 29.3057 72.2697 30.046 72.6797 30.5243C73.1125 31.0026 73.7616 31.2418 74.6272 31.2418C75.6294 31.2418 76.438 30.9001 77.0529 30.2168C77.6907 29.5335 78.0096 28.6338 78.0096 27.5177V18.2246H83.1686ZM96.4204 31.4809C96.9443 31.4809 97.491 31.4468 98.0604 31.3784L97.7871 35.1367C97.1265 35.2278 96.466 35.2733 95.8054 35.2733C93.2544 35.2733 91.3867 34.7153 90.2022 33.5992C89.0406 32.4831 88.4598 30.7862 88.4598 28.5085V22.0853H85.2824V18.2246H88.4598V13.3047H93.6188V18.2246H97.8212V22.0853H93.6188V28.4743C93.6188 30.4787 94.5527 31.4809 96.4204 31.4809ZM112.332 22.0512L109.428 22.3587C107.993 22.4953 106.979 22.9053 106.387 23.5886C105.795 24.2492 105.499 25.1375 105.499 26.2536V35H100.34V18.2246H105.294V21.0603C106.137 19.1243 107.879 18.0651 110.521 17.8829L112.025 17.7804L112.332 22.0512ZM121.501 17.8146C124.007 17.8146 125.852 18.4068 127.036 19.5912C128.243 20.7756 128.847 22.6092 128.847 25.0919V35H123.961V32.5059C123.619 33.3714 123.05 34.0547 122.253 34.5558C121.456 35.0342 120.522 35.2733 119.451 35.2733C118.312 35.2733 117.276 35.0456 116.342 34.59C115.431 34.1345 114.702 33.4967 114.155 32.6767C113.632 31.8567 113.37 30.9457 113.37 29.9435C113.37 28.7135 113.677 27.7455 114.292 27.0394C114.93 26.3333 115.943 25.8208 117.333 25.5019C118.722 25.183 120.624 25.0236 123.039 25.0236H123.927V24.4086C123.927 23.4064 123.71 22.7003 123.278 22.2903C122.845 21.8803 122.093 21.6753 121.023 21.6753C120.203 21.6753 119.292 21.8234 118.289 22.1195C117.287 22.4156 116.331 22.8256 115.42 23.3495L114.053 19.8987C115.01 19.3065 116.183 18.8168 117.572 18.4296C118.984 18.0196 120.294 17.8146 121.501 17.8146ZM120.579 31.7201C121.581 31.7201 122.389 31.3898 123.004 30.7293C123.619 30.046 123.927 29.169 123.927 28.0985V27.5177H123.346C121.501 27.5177 120.203 27.6657 119.451 27.9618C118.722 28.2579 118.358 28.7932 118.358 29.5676C118.358 30.1826 118.563 30.6951 118.973 31.1051C119.406 31.5151 119.941 31.7201 120.579 31.7201ZM140.699 35.2733C137.966 35.2733 135.813 34.4989 134.241 32.9501C132.67 31.4012 131.884 29.2943 131.884 26.6294C131.884 24.8755 132.26 23.3381 133.011 22.017C133.763 20.6731 134.822 19.6368 136.189 18.9079C137.556 18.179 139.139 17.8146 140.938 17.8146C142.168 17.8146 143.352 18.0082 144.491 18.3954C145.63 18.7599 146.553 19.2609 147.259 19.8987L145.892 23.4178C145.231 22.8939 144.503 22.4953 143.705 22.222C142.931 21.9259 142.168 21.7778 141.416 21.7778C140.072 21.7778 139.025 22.1764 138.273 22.9736C137.544 23.7708 137.18 24.9553 137.18 26.5269C137.18 28.0985 137.544 29.2943 138.273 30.1143C139.025 30.9115 140.072 31.3101 141.416 31.3101C142.168 31.3101 142.931 31.1734 143.705 30.9001C144.503 30.604 145.231 30.194 145.892 29.6701L147.259 33.2234C146.507 33.8611 145.55 34.3622 144.389 34.7267C143.227 35.0911 141.997 35.2733 140.699 35.2733ZM161.169 35L154.746 27.5177V35H149.587V10.9131H154.746V25.3994L160.93 18.2588H167.08L160.041 26.2536L167.49 35H161.169Z" fill="#33ABA0"/>
                                </g>
                                <defs>
                                    <filter id="filter_logo_shadow" x="0.391602" y="8.88306" width="193.899" height="34.3904" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                                        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                                        <feOffset dy="4"/>
                                        <feGaussianBlur stdDeviation="2"/>
                                        <feComposite in2="hardAlpha" operator="out"/>
                                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
                                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_logo"/>
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_logo" result="shape"/>
                                    </filter>
                                </defs>
                        </svg>
                    </a>
                    
                    <p class="text-white/90 text-sm leading-relaxed font-light max-w-sm sm:max-w-md lg:pr-8">
                        Platform digital untuk pengelolaan pengajuan kegiatan, anggaran, dan laporan secara terstruktur dan efisien di lingkungan kampus.
                    </p>

                    <div class="flex items-center gap-2 pt-1">
                        <i class="fab fa-instagram text-xl"></i>
                        <span class="text-sm font-medium hover:text-white/80 transition-colors">@politekniknegerijakarta</span>
                    </div>
                </div>

                <div class="sm:col-span-1 lg:col-span-4 flex flex-col items-center sm:items-start text-center sm:text-left">
                    <h3 class="text-lg font-bold mb-5 relative inline-block">
                        Contact Us
                        <span class="block h-0.5 w-1/2 bg-white/40 mt-1 rounded-full mx-auto sm:mx-0"></span>
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-start justify-center sm:justify-start gap-3 group">
                            <div class="mt-1 w-5 flex justify-center flex-shrink-0">
                                <i class="fas fa-phone-alt text-sm group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="text-white/90 text-sm hover:text-white transition-colors cursor-default">+62 821-356-278</span>
                        </li>
                        <li class="flex items-start justify-center sm:justify-start gap-3 group">
                            <div class="mt-1 w-5 flex justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-sm group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="text-white/90 text-sm break-all hover:text-white transition-colors cursor-default">humas@pnj.sch.id</span>
                        </li>
                        <li class="flex items-start justify-center sm:justify-start gap-3 group">
                            <div class="mt-1 w-5 flex justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-sm group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="text-white/90 text-sm leading-relaxed hover:text-white transition-colors cursor-default">
                                Jl. Prof. DR. G.A. Siwabessy, Kampus<br>
                                Universitas Indonesia Depok 16425
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="sm:col-span-1 lg:col-span-3 flex flex-col items-center sm:items-start text-center sm:text-left">
                    <h3 class="text-lg font-bold mb-5 relative inline-block">
                        Landing Page
                        <span class="block h-0.5 w-1/2 bg-white/40 mt-1 rounded-full mx-auto sm:mx-0"></span>
                    </h3>
                    <ul class="space-y-3 w-full sm:w-auto">
                        <li><a href="#home" class="text-white/90 text-sm hover:text-white hover:translate-x-1 transition-all inline-block py-1">Home</a></li>
                        <li><a href="#about" class="text-white/90 text-sm hover:text-white hover:translate-x-1 transition-all inline-block py-1">About</a></li>
                        <li><a href="#detail" class="text-white/90 text-sm hover:text-white hover:translate-x-1 transition-all inline-block py-1">Detail</a></li>
                        <li><a href="#proses" class="text-white/90 text-sm hover:text-white hover:translate-x-1 transition-all inline-block py-1">Proses</a></li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-white/20 pt-6 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-white/80">
                <p class="text-center md:text-left order-2 md:order-1">
                    © 2025 Docutrack. All rights reserved.
                </p>
                <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 order-1 md:order-2">
                    <a href="#" class="hover:text-white transition-colors hover:underline">Cookie Policy</a>
                    <a href="#" class="hover:text-white transition-colors hover:underline">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors hover:underline">Terms of Service</a>
                </div>
            </div>
            
        </div>
    </div>
</footer>

    

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- 1. Navbar Scroll Effect ---
        const navbar = document.getElementById('navbarInner');
        if (navbar) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    navbar.classList.add(
                        'bg-[linear-gradient(90deg,#114177b3_0%,#006A9Ab3_50%,#17A18Ab3_100%)]',
                        'backdrop-blur-3xl',
                        'rounded-2xl',
                        'border',
                        'border-white/5',
                        'shadow-[0_8px_32px_0_rgba(0,0,0,0.3),inset_0_1px_0_0_rgba(255,255,255,0.2)]'
                    );
                    navbar.classList.remove('bg-transparent', 'border-transparent', 'rounded-none');
                } else {
                    navbar.classList.remove(
                        'bg-[linear-gradient(90deg,#114177b3_0%,#006A9Ab3_50%,#17A18Ab3_100%)]',
                        'backdrop-blur-3xl',
                        'rounded-2xl',
                        'border',
                        'border-white/5',
                        'shadow-[0_8px_32px_0_rgba(0,0,0,0.3),inset_0_1px_0_0_rgba(255,255,255,0.2)]'
                    );
                    navbar.classList.add('bg-transparent', 'border-transparent', 'rounded-none');
                }
            });
        }

        // --- 2. Mobile Menu Toggle ---
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('active');
                mobileMenuBtn.classList.toggle('active');
            });

            // Close menu when clicking menu items
            const menuLinks = mobileMenu.querySelectorAll('a');
            menuLinks.forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenu.classList.remove('active');
                    mobileMenuBtn.classList.remove('active');
                });
            });
        }

        // --- 3. Smooth Scroll ---
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const headerHeight = 80;
                    const targetPosition = target.offsetTop - headerHeight;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

    }); // End DOMContentLoaded

    // --- 4. POPUP FUNCTIONS (Global) ---
    function openLoginPopup() {
        const loginPopup = document.getElementById('popup-login');
        if (loginPopup) {
            loginPopup.classList.remove('hidden');
            loginPopup.classList.add('flex');
        }
    }

    function closeLoginPopup() {
        const loginPopup = document.getElementById('popup-login');
        if (loginPopup) {
            loginPopup.classList.add('hidden');
            loginPopup.classList.remove('flex');
        }
    }

    function openRegisterPopup() {
        const registerPopup = document.getElementById('popup-register');
        if (registerPopup) {
            registerPopup.classList.remove('hidden');
            registerPopup.classList.add('flex');
        }
    }

    function closeRegisterPopup() {
        const registerPopup = document.getElementById('popup-register');
        if (registerPopup) {
            registerPopup.classList.add('hidden');
            registerPopup.classList.remove('flex');
        }
    }

    function switchToRegister() {
        closeLoginPopup();
        openRegisterPopup();
    }

    function switchToLogin() {
        closeRegisterPopup();
        openLoginPopup();
    }

    // Close popup when clicking outside
    window.addEventListener('click', function(event) {
        const loginPopup = document.getElementById('popup-login');
        const registerPopup = document.getElementById('popup-register');
        
        if (event.target === loginPopup) {
            closeLoginPopup();
        }
        if (event.target === registerPopup) {
            closeRegisterPopup();
        }
    });

    // Close popup with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLoginPopup();
            closeRegisterPopup();
        }
    });
    </script>

</body>
</html>

