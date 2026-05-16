<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Docutrack PNJ - Sistem Pengajuan TOR & Kegiatan' }}</title>
    
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
                        <a href="{{ url('/') }}">
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

    <main>
        @yield('content')
    </main>

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
                    <a href="{{ url('/') }}" class="block hover:opacity-90 transition-opacity">
                        <svg width="195" height="47" viewBox="0 0 195 47" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-40 sm:w-48 lg:w-[195px] h-auto">
                           <g filter="url(#filter_logo_shadow)">
                                <path d="M179.658 26.2678C174.418 24.2673 168.504 26.2678 166.917 27.1013C172.632 26.625 173.585 27.1013 178.586 29.3638C185.139 32.3283 189.104 26.1884 190.136 24.0054C188.787 25.2358 184.897 28.2682 179.658 26.2678Z" fill="white" stroke="white" stroke-width="0.341658"/>
                                <path d="M176.085 30.555C171.227 28.2687 168.305 27.7368 167.393 28.0543C169.536 29.7214 174.085 33.4127 177.99 34.3653C181.896 35.3179 186.405 32.5395 188.231 30.0786C185.968 31.6663 180.943 32.8412 176.085 30.555Z" fill="white" stroke="white" stroke-width="0.341658"/>
                                <path d="M180.149 21.1713C174.482 19.2149 168.93 23.8971 166.95 26.1927C168.071 24.8985 171.909 22.6991 178.292 24.2551C184.675 25.8111 188.749 22.1279 189.998 20.2702C189.304 20.6662 186.106 23.2277 180.149 21.1713Z" fill="white" stroke="white" stroke-width="0.341658"/>
                                <ellipse cx="183.468" cy="13.527" rx="4.64394" ry="4.64394" fill="#FF0000"/>
                                <path d="M4.39161 10.9131H13.753C16.3724 10.9131 18.6274 11.3914 20.5179 12.3481C22.4084 13.3047 23.8547 14.6827 24.8569 16.4821C25.8591 18.2815 26.3602 20.434 26.3602 22.9395C26.3602 25.445 25.8591 27.6088 24.8569 29.431C23.8775 31.2304 22.4425 32.6084 20.552 33.565C18.6615 34.5217 16.3952 35 13.753 35H4.39161V10.9131ZM13.3772 30.6951C18.5021 30.6951 21.0645 28.1099 21.0645 22.9395C21.0645 17.7918 18.5021 15.218 13.3772 15.218H9.72147V30.6951H13.3772ZM38.1029 35.2733C36.3262 35.2733 34.766 34.9203 33.4221 34.2142C32.0783 33.4853 31.0419 32.4717 30.313 31.1734C29.5842 29.8524 29.2197 28.3035 29.2197 26.5269C29.2197 24.7503 29.5842 23.2128 30.313 21.9145C31.0419 20.5934 32.0783 19.5798 33.4221 18.8737C34.766 18.1676 36.3262 17.8146 38.1029 17.8146C39.8795 17.8146 41.4397 18.1676 42.7836 18.8737C44.1274 19.5798 45.1638 20.5934 45.8927 21.9145C46.6215 23.2128 46.986 24.7503 46.986 26.5269C46.986 28.3035 46.6215 29.8524 45.8927 31.1734C45.1638 32.4717 44.1274 33.4853 42.7836 34.2142C41.4397 34.9203 39.8795 35.2733 38.1029 35.2733ZM38.1029 31.3443C40.6083 31.3443 41.8611 29.7385 41.8611 26.5269C41.8611 24.9097 41.5308 23.7025 40.8703 22.9053C40.2325 22.1081 39.31 21.7095 38.1029 21.7095C35.5974 21.7095 34.3446 23.3153 34.3446 26.5269C34.3446 29.7385 35.5974 31.3443 38.1029 31.3443ZM58.1203 35.2733C55.387 35.2733 53.2346 34.4989 51.6629 32.9501C50.0913 31.4012 49.3055 29.2943 49.3055 26.6294C49.3055 24.8755 49.6813 23.3381 50.433 22.017C51.1846 20.6731 52.2438 19.6368 53.6104 18.9079C54.977 18.179 56.56 17.8146 58.3594 17.8146C59.5894 17.8146 60.7738 18.0082 61.9127 18.3954C63.0515 18.7599 63.974 19.2609 64.6801 19.8987L63.3135 23.4178C62.6529 22.8939 61.9241 22.4953 61.1269 22.222C60.3524 21.9259 59.5894 21.7778 58.8378 21.7778C57.4939 21.7778 56.4462 22.1764 55.6945 22.9736C54.9656 23.7708 54.6012 24.9553 54.6012 26.5269C54.6012 28.0985 54.9656 29.2943 55.6945 30.1143C56.4462 30.9115 57.4939 31.3101 58.8378 31.3101C59.5894 31.3101 60.3524 31.1734 61.1269 30.9001C61.9241 30.604 62.6529 30.194 63.3135 29.6701L64.6801 33.2234C63.9285 33.8611 62.9718 34.3622 61.8102 34.7267C60.6486 35.0911 59.4186 35.2733 58.1203 35.2733ZM83.1686 18.2246V35H78.1463V32.6084C77.6224 33.4739 76.9277 34.1345 76.0621 34.59C75.2194 35.0456 74.2627 35.2733 73.1922 35.2733C71.0739 35.2733 69.4909 34.6925 68.4432 33.5309C67.4182 32.3465 66.9057 30.5698 66.9057 28.201V18.2246H72.0647V28.3035C72.0647 29.3057 72.2697 30.046 72.6797 30.5243C73.1125 31.0026 73.7616 31.2418 74.6272 31.2418C75.6294 31.2418 76.438 30.9001 77.0529 30.2168C77.6907 29.5335 78.0096 28.6338 78.0096 27.5177V18.2246H83.1686ZM96.4204 31.4809C96.9443 31.4809 97.491 31.4468 98.0604 31.3784L97.7871 35.1367C97.1265 35.2278 96.466 35.2733 95.8054 35.2733C93.2544 35.2733 91.3867 34.7153 90.2022 33.5992C89.0406 32.4831 88.4598 30.7862 88.4598 28.5085V22.0853H85.2824V18.2246H88.4598V13.3047H93.6188V18.2246H97.8212V22.0853H93.6188V28.4743C93.6188 30.4787 94.5527 31.4809 96.4204 31.4809ZM112.332 22.0512L109.428 22.3587C107.993 22.4953 106.979 22.9053 106.387 23.5886C105.795 24.2492 105.499 25.1375 105.499 26.2536V35H100.34V18.2246H105.294V21.0603C106.137 19.1243 107.879 18.0651 110.521 17.8829L112.025 17.7804L112.332 22.0512ZM121.501 17.8146C124.007 17.8146 125.852 18.4068 127.036 19.5912C128.243 20.7756 128.847 22.6092 128.847 25.0919V35H123.961V32.5059C123.619 33.3714 123.05 34.0547 122.253 34.5558C121.456 35.0342 120.522 35.2733 119.451 35.2733C118.312 35.2733 117.276 35.0456 116.342 34.59C115.431 34.1345 114.702 33.4967 114.155 32.6767C113.632 31.8567 113.37 30.9457 113.37 29.9435C113.37 28.7135 113.677 27.7455 114.292 27.0394C114.93 26.3333 115.943 25.8208 117.333 25.5019C118.722 25.183 120.624 25.0236 123.039 25.0236H123.927V24.4086C123.927 23.4064 123.71 22.7003 123.278 22.2903C122.845 21.8803 122.093 21.6753 121.023 21.6753C120.203 21.6753 119.292 21.8234 118.289 22.1195C117.287 22.4156 116.331 22.8256 115.42 23.3495L114.053 19.8987C115.01 19.3065 116.183 18.8168 117.572 18.4296C118.984 18.0196 120.294 17.8146 121.501 17.8146ZM120.579 31.7201C121.581 31.7201 122.389 31.3898 123.004 30.7293C123.619 30.046 123.927 29.169 123.927 28.0985V27.5177H123.346C121.501 27.5177 120.203 27.6657 119.451 27.9618C118.722 28.2579 118.358 28.7932 118.358 29.5676C118.358 30.1826 118.563 30.6951 118.973 31.1051C119.406 31.5151 119.941 31.7201 120.579 31.7201ZM140.699 35.2733C137.966 35.2733 135.813 34.4989 134.241 32.9501C132.67 31.4012 131.884 29.2943 131.884 26.6294C131.884 24.8755 132.26 23.3381 133.011 22.017C133.763 20.6731 134.822 19.6368 136.189 18.9079C137.556 18.179 139.139 17.8146 140.938 17.8146C142.168 17.8146 143.352 18.0082 144.491 18.3954C145.63 18.7599 146.553 19.2609 147.259 19.8987L145.892 23.4178C145.231 22.8939 144.503 22.4953 143.705 22.222C142.931 21.9259 142.168 21.7778 141.416 21.7778C140.072 21.7778 139.025 22.1764 138.273 22.9736C137.544 23.7708 137.18 24.9553 137.18 26.5269C137.18 28.0985 137.544 29.2943 138.273 30.1143C139.025 30.9115 140.072 31.3101 141.416 31.3101C142.168 31.3101 142.931 31.1734 143.705 30.9001C144.503 30.604 145.231 30.194 145.892 29.6701L147.259 33.2234C146.507 33.8611 145.55 34.3622 144.389 34.7267C143.227 35.0911 141.997 35.2733 140.699 35.2733ZM161.169 35L154.746 27.5177V35H149.587V10.9131H154.746V25.3994L160.93 18.2588H167.08L160.041 26.2536L167.49 35H161.169Z" fill="#33ABA0"/></g><defs><filter id="filter_logo_shadow" x="0.391602" y="8.88306" width="193.899" height="34.3904" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4"/><feGaussianBlur stdDeviation="2"/><feComposite in2="hardAlpha" operator="out"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_logo"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_logo" result="shape"/></filter></defs>
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
                    Â© 2025 Docutrack. All rights reserved.
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

    <!-- POPUPS WILL BE INCLUDED IN WELCOME BLADE -->
    @stack('modals')

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
    
    @stack('scripts')

</body>
</html>

