<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Terlalu Banyak Permintaan | DocuTrack</title>
    <!-- Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .glass {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .glow {
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-[#114177] via-[#006A9A] to-[#17A18A] min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Decorative background elements -->
    <div class="absolute w-[350px] h-[350px] rounded-full bg-white/5 blur-[80px] -top-10 -left-10 animate-pulse"></div>
    <div class="absolute w-[450px] h-[450px] rounded-full bg-[#17A18A]/20 blur-[100px] -bottom-20 -right-20 animate-pulse" style="animation-delay: 2s;"></div>

    <div class="glass glow max-w-lg w-full rounded-3xl p-8 md:p-10 text-center relative z-10 text-white">
        <!-- Shield & Hourglass Icon -->
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-amber-500/20 text-amber-200 border border-amber-500/30 mb-8 animate-pulse" style="animation-duration: 1.5s;">
            <i class="fa-solid fa-user-shield text-5xl"></i>
        </div>

        <!-- Heading -->
        <h1 class="text-6xl font-extrabold tracking-tight mb-2 text-amber-200">429</h1>
        <h2 class="text-2xl font-bold mb-4">Terlalu Banyak Permintaan</h2>

        <!-- Message -->
        <p class="text-white/80 mb-8 leading-relaxed text-sm md:text-base">
            Sistem kami mendeteksi aktivitas masuk (login) atau lalu lintas data yang tidak wajar dari perangkat Anda dalam waktu singkat. Demi menjaga keamanan aplikasi dari serangan <strong>Brute-Force</strong> atau <strong>DDoS</strong>, akses Anda sementara dibatasi.
        </p>

        <!-- Information details -->
        <div class="bg-black/25 border border-white/10 rounded-2xl p-5 mb-8 text-left text-xs md:text-sm text-white/90 space-y-3">
            <div class="flex justify-between items-start gap-4">
                <span class="font-medium text-white/60">Target Rute:</span>
                <span class="text-[#17A18A] bg-white/90 px-2.5 py-0.5 rounded-md font-mono break-all text-right max-w-[240px]">{{ request()->getRequestUri() }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-medium text-white/60">Batas Sistem:</span>
                <span class="text-amber-300 font-semibold flex items-center gap-1.5">
                    <i class="fa-solid fa-clock text-xs"></i> Maks. 5 Percobaan / Menit
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-medium text-white/60">Alamat IP Anda:</span>
                <span class="font-mono text-white/50">{{ request()->ip() }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-medium text-white/60">Status Keamanan:</span>
                <span class="text-emerald-400 font-bold flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-xs"></i> Sistem Proteksi Aktif
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
            <button onclick="window.location.reload()" class="w-full sm:w-auto px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-medium rounded-xl border border-white/20 transition-all duration-300 flex items-center justify-center gap-2">
                <i class="fa-solid fa-rotate-right"></i> Coba Lagi / Refresh
            </button>
            <a href="/" class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-[#17A18A] to-[#006A9A] hover:from-[#1bc0a5] hover:to-[#0081bb] text-white font-medium rounded-xl shadow-lg border border-white/10 transition-all duration-300 flex items-center justify-center gap-2">
                <i class="fa-solid fa-house"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

</body>
</html>
