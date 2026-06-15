document.addEventListener('DOMContentLoaded', () => {

    // ===================================
    // DATA PRODI PNJ (BARU DITAMBAHKAN)
    // ===================================
    const dataProdiPNJ = {
        "Teknik Sipil": [
            "D3 Konstruksi Gedung",
            "D3 Konstruksi Sipil",
            "D4 Perancangan Jalan dan Jembatan",
            "D4 Manajemen Konstruksi"
        ],
        "Teknik Mesin": [
            "D3 Teknik Mesin",
            "D3 Teknik Konversi Energi",
            "D3 Alat Berat",
            "D4 Pembangkit Tenaga Listrik",
            "D4 Teknologi Rekayasa Manufaktur",
            "D4 Teknologi Rekayasa Konversi Energi",
            "D4 Teknologi Rekayasa Perawatan Alat Berat"
        ],
        "Teknik Elektro": [
            "D3 Teknik Listrik",
            "D3 Teknik Elektronika Industri",
            "D3 Teknik Telekomunikasi",
            "D4 Teknik Instrumentasi dan Kontrol Industri",
            "D4 Teknik Otomasi Listrik Industri",
            "D4 Broadband Multimedia"
        ],
        "Teknik Informatika dan Komputer": [
            "D4 Teknik Informatika",
            "D4 Teknik Multimedia Digital",
            "D4 Teknik Multimedia dan Jaringan",
            "D1 Teknik Komputer dan Jaringan"
        ],
        "Teknik Grafika dan Penerbitan": [
            "D3 Teknik Grafika",
            "D3 Penerbitan (Jurnalistik)",
            "D4 Desain Grafis",
            "D4 Teknologi Industri Cetak Kemasan"
        ],
        "Akuntansi": [
            "D3 Akuntansi",
            "D3 Keuangan dan Perbankan",
            "D4 Akuntansi Keuangan",
            "D4 Keuangan dan Perbankan Syariah",
            "D4 Manajemen Keuangan"
        ],
        "Administrasi Niaga": [
            "D3 Administrasi Bisnis",
            "D4 Administrasi Bisnis Terapan",
            "D4 Meeting, Incentive, Convention, and Exhibition (MICE)",
            "D4 Bahasa Inggris untuk Komunikasi Bisnis dan Profesional"
        ],
        "Pascasarjana": [
            "S2 Magister Terapan Teknik Elektro",
            "S2 Magister Terapan Rekayasa Teknologi Manufaktur"
        ]
    };

    // ===================================
    // TEMPLATE STEPPER (4 STEPS)   
    // ===================================
    const stepperTemplates = {
        1: `<nav aria-label="Progress"><ol role="list" class="relative z-0 flex items-center justify-between w-full max-w-4xl mx-auto"><div class="absolute left-0 top-6 w-full -translate-y-1/2 h-1.5 bg-gray-200 -z-10"></div><div class="stepper-line absolute left-0 top-6 w-0 -translate-y-1/2 h-1.5 -z-10 transition-all duration-700 ease-out bg-gradient-to-r from-blue-500 to-cyan-400 line-flow-animation"></div><li class="relative"><a href="#" class="group" aria-current="step"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-white ring-4 ring-blue-500 shadow-xl shadow-blue-500/50 transition-all duration-300"><span class="font-bold md:font-extrabold text-xl md:text-2xl bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">1</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-bold text-blue-600">Data Pengusul</span><span class="block text-[10px] md:text-xs text-blue-500">Sedang diisi</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300"><span class="font-medium md:font-bold text-lg md:text-xl">2</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700">Kerangka Acuan</span><span class="block text-[10px] md:text-xs text-gray-400">Berikutnya</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300"><span class="font-medium md:font-bold text-lg md:text-xl">3</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700">Indikator Kinerja</span><span class="block text-[10px] md:text-xs text-gray-400">Berikutnya</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300"><span class="font-medium md:font-bold text-lg md:text-xl">4</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700">Rincian Anggaran</span><span class="block text-[10px] md:text-xs text-gray-400">Berikutnya</span></div></div></a></li></ol></nav>`,
        2: `<nav aria-label="Progress"><ol role="list" class="relative z-0 flex items-center justify-between w-full max-w-4xl mx-auto"><div class="absolute left-0 top-6 w-full -translate-y-1/2 h-1.5 bg-gray-200 -z-10"></div><div class="stepper-line absolute left-0 top-6 w-1/3 -translate-y-1/2 h-1.5 -z-10 transition-all duration-700 ease-out bg-gradient-to-r from-blue-500 to-cyan-400 line-flow-animation"></div><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300"><i class="fas fa-check text-lg md:text-xl"></i></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-semibold text-gray-700">Data Pengusul</span><span class="block text-[10px] md:text-xs text-gray-500">Selesai</span></div></div></a></li><li class="relative"><a href="#" class="group" aria-current="step"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-white ring-4 ring-blue-500 shadow-xl shadow-blue-500/50 transition-all duration-300"><span class="font-bold md:font-extrabold text-xl md:text-2xl bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">2</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-bold text-blue-600">Kerangka Acuan</span><span class="block text-[10px] md:text-xs text-blue-500">Sedang diisi</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300"><span class="font-medium md:font-bold text-lg md:text-xl">3</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700">Indikator Kinerja</span><span class="block text-[10px] md:text-xs text-gray-400">Berikutnya</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300"><span class="font-medium md:font-bold text-lg md:text-xl">4</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700">Rincian Anggaran</span><span class="block text-[10px] md:text-xs text-gray-400">Berikutnya</span></div></div></a></li></ol></nav>`,
        3: `<nav aria-label="Progress"><ol role="list" class="relative z-0 flex items-center justify-between w-full max-w-4xl mx-auto"><div class="absolute left-0 top-6 w-full -translate-y-1/2 h-1.5 bg-gray-200 -z-10"></div><div class="stepper-line absolute left-0 top-6 w-2/3 -translate-y-1/2 h-1.5 -z-10 transition-all duration-700 ease-out bg-gradient-to-r from-blue-500 to-cyan-400 line-flow-animation"></div><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300"><i class="fas fa-check text-lg md:text-xl"></i></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-semibold text-gray-700">Data Pengusul</span><span class="block text-[10px] md:text-xs text-gray-500">Selesai</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300"><i class="fas fa-check text-lg md:text-xl"></i></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-semibold text-gray-700">Kerangka Acuan</span><span class="block text-[10px] md:text-xs text-gray-500">Selesai</span></div></div></a></li><li class="relative"><a href="#" class="group" aria-current="step"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-white ring-4 ring-blue-500 shadow-xl shadow-blue-500/50 transition-all duration-300"><span class="font-bold md:font-extrabold text-xl md:text-2xl bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">3</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-bold text-blue-600">Indikator Kinerja</span><span class="block text-[10px] md:text-xs text-blue-500">Sedang diisi</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300"><span class="font-medium md:font-bold text-lg md:text-xl">4</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700">Rincian Anggaran</span><span class="block text-[10px] md:text-xs text-gray-400">Berikutnya</span></div></div></a></li></ol></nav>`,
        4: `<nav aria-label="Progress"><ol role="list" class="relative z-0 flex items-center justify-between w-full max-w-4xl mx-auto"><div class="absolute left-0 top-6 w-full -translate-y-1/2 h-1.5 bg-gray-200 -z-10"></div><div class="stepper-line absolute left-0 top-6 w-full -translate-y-1/2 h-1.5 -z-10 transition-all duration-700 ease-out bg-gradient-to-r from-blue-500 to-cyan-400 line-flow-animation"></div><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300"><i class="fas fa-check text-lg md:text-xl"></i></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-semibold text-gray-700">Data Pengusul</span><span class="block text-[10px] md:text-xs text-gray-500">Selesai</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300"><i class="fas fa-check text-lg md:text-xl"></i></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-semibold text-gray-700">Kerangka Acuan</span><span class="block text-[10px] md:text-xs text-gray-500">Selesai</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300"><i class="fas fa-check text-lg md:text-xl"></i></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-semibold text-gray-700">Indikator Kinerja</span><span class="block text-[10px] md:text-xs text-gray-500">Selesai</span></div></div></a></li><li class="relative"><a href="#" class="group" aria-current="step"><div class="flex flex-col items-center w-20 md:w-32 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-white ring-4 ring-blue-500 shadow-xl shadow-blue-500/50 transition-all duration-300"><span class="font-bold md:font-extrabold text-xl md:text-2xl bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">4</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-bold text-blue-600">Rincian Anggaran</span><span class="block text-[10px] md:text-xs text-blue-500">Sedang diisi</span></div></div></a></li></ol></nav>`
    };

    // ===================================
    // TEMPLATE REPEATER KAK
    // ===================================
    const tahapanTemplateHTML = `<div class="flex items-center gap-2 md:gap-3 repeater-row-tahapan border border-transparent p-3 mb-3"><span class="tahapan-number text-gray-500 font-medium pt-3 flex-shrink-0"></span><div class="relative flex-grow"><i class="fas fa-flag absolute top-3.5 left-3 text-gray-400 transition-colors duration-300 peer-focus:text-blue-600 pointer-events-none"></i><input required type="text" name="tahapan[]" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "><label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Tahapan Pelaksanaan</label></div><button type="button" class="text-red-500 hover:text-red-700 pt-3 remove-row-btn transition-colors flex-shrink-0"><i class="fas fa-trash pointer-events-none"></i></button></div>`;

    const indikatorTemplateHTML = `<div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-start md:items-center repeater-row-indikator border md:border-transparent border-gray-100 rounded-lg p-3 md:p-0 mb-3 md:mb-0"><div class="col-span-1 md:col-span-2 select-wrapper pb-4"><label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Bulan</label><div class="relative"><i class="fas fa-calendar-alt absolute top-1/2 -translate-y-1/2 left-3 text-gray-400 transition-colors duration-300 peer-focus-within:text-blue-600 pointer-events-none z-10"></i><select required name="indikator_bulan[]" class="floating-select block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" onchange="this.setAttribute('filled', this.value !== '' ? 'true' : 'false')"><option value="" selected></option><option value="1">Januari</option><option value="2">Februari</option><option value="3">Maret</option><option value="4">April</option><option value="5">Mei</option><option value="6">Juni</option><option value="7">Juli</option><option value="8">Agustus</option><option value="9">September</option><option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option></select><label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Pilih Bulan</label></div></div><div class="col-span-1 md:col-span-4 pb-4"><label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Indikator</label><div class="relative"><i class="fas fa-clipboard-check absolute top-3.5 left-3 text-gray-400 transition-colors duration-300 peer-focus:text-blue-600 pointer-events-none"></i><input required type="text" name="indikator_nama[]" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "><label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Indikator Keberhasilan</label></div></div><div class="col-span-1 md:col-span-1 pb-4"><label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Target (%)</label><div class="relative"><i class="fas fa-bullseye absolute top-3.5 left-3 text-gray-400 transition-colors duration-300 peer-focus:text-blue-600 pointer-events-none"></i><input required type="number" min="0" max="100" name="indikator_target[]" class="block w-full px-4 py-3.5 pl-10 pr-7 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "><label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Target</label><span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 pointer-events-none text-sm">%</span></div></div><div class="col-span-1 md:col-span-1 text-right md:text-center mt-2 md:mt-0 pt-3 md:pt-0"><button type="button" class="text-red-500 hover:text-red-700 remove-row-btn transition-colors"><i class="fas fa-trash pointer-events-none"></i></button></div></div>`;

    // ===================================
    // ELEMEN DOM UTAMA
    // ===================================
    let currentStep = 1;
    const stepperContainer = document.getElementById('stepper-container');
    const formSteps = document.querySelectorAll('.form-step');
    const kakFormElement = document.getElementById('kak-form-element');

    // Inisialisasi Stepper langsung saat Load
    renderStepper(currentStep);

    // ===================================
    // LOGIKA JURUSAN & PRODI (BARU)
    // ===================================
    function setupJurusanProdi() {
        const jurusanSelect = document.getElementById('jurusan');
        const prodiSelect = document.getElementById('prodi');

        if (!jurusanSelect || !prodiSelect) return;

        jurusanSelect.addEventListener('change', function () {
            const selectedJurusan = this.value;

            // Kosongkan pilihan prodi saat ini
            prodiSelect.innerHTML = '<option value="" disabled selected>Pilih Prodi</option>';

            if (selectedJurusan && dataProdiPNJ[selectedJurusan]) {
                // Aktifkan dropdown prodi
                prodiSelect.disabled = false;
                prodiSelect.classList.remove('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                prodiSelect.classList.add('bg-white', 'text-gray-800', 'cursor-pointer');

                // Isi dropdown dengan prodi yang sesuai
                dataProdiPNJ[selectedJurusan].forEach(function (prodi) {
                    const option = document.createElement("option");
                    option.value = prodi;
                    option.text = prodi;
                    prodiSelect.appendChild(option);
                });
            } else {
                // Jika tidak ada jurusan dipilih, disable prodi
                prodiSelect.disabled = true;
                prodiSelect.innerHTML = '<option value="">Pilih Jurusan Terlebih Dahulu</option>';
                prodiSelect.classList.add('bg-gray-100', 'text-gray-400', 'cursor-not-allowed');
                prodiSelect.classList.remove('bg-white', 'text-gray-800', 'cursor-pointer');
            }
        });
    }

    // Panggil fungsi setup untuk Jurusan/Prodi
    setupJurusanProdi();

    // ===================================
    // LOGIKA STEPPER FORM
    // ===================================
    function renderStepper(step) {
        if (stepperContainer && stepperTemplates[step]) {
            stepperContainer.innerHTML = stepperTemplates[step];
        }
    }

    function goToStep(targetStep) {
        const currentStepEl = document.getElementById(`form-tahap-${currentStep}`);
        const targetStepEl = document.getElementById(`form-tahap-${targetStep}`);
        if (!currentStepEl || !targetStepEl || currentStep === targetStep) return;

        currentStepEl.classList.remove('active');
        currentStepEl.classList.add('inactive');
        targetStepEl.classList.remove('inactive');
        targetStepEl.classList.add('active');

        renderStepper(targetStep);
        currentStep = targetStep;

        const grandTotalContainer = document.querySelector('.grand-total-container');
        if (grandTotalContainer) {
            if (targetStep === 4) {
                grandTotalContainer.classList.remove('hidden');
                grandTotalContainer.classList.add('flex');
            } else {
                grandTotalContainer.classList.add('hidden');
                grandTotalContainer.classList.remove('flex');
            }
        }

        if (targetStep === 4) {
            renderRabSidebar();
            renderRabContent();
            calculateTotals();
        }

        if (window.scrollY > 100 && stepperContainer) {
            window.scrollTo({ top: stepperContainer.offsetTop - 80, behavior: 'smooth' });
        }
    }

    // ===================================
    // FUNGSI TRANSFER DATA dari Step 1 ke Step 2
    // ===================================
    function transferDataToStep2() {
        const namaPengusulStep1 = document.getElementById('nama_pengusul_step1');
        const namaKegiatanStep1 = document.getElementById('nama_kegiatan_step1');

        const namaPengusulStep2 = document.getElementById('nama_pengusul');
        const namaKegiatanStep2 = document.getElementById('nama_kegiatan_kak');

        // Transfer HANYA NAMA PENGUSUL (tanpa NIM, Jurusan, Prodi)
        if (namaPengusulStep1 && namaPengusulStep2) {
            namaPengusulStep2.value = namaPengusulStep1.value.trim();
        }

        // Transfer Nama Kegiatan
        if (namaKegiatanStep1 && namaKegiatanStep2 && namaKegiatanStep1.value.trim()) {
            namaKegiatanStep2.value = namaKegiatanStep1.value.trim();
        }
    }

    // ===================================
    // FUNGSI VALIDASI
    // ===================================
    function validateStep(stepNumber) {
        let isValid = true;
        const activeStepElement = document.getElementById(`form-tahap-${stepNumber}`);
        if (!activeStepElement) return false;

        // 1. Clear all existing error-msg elements and red borders/rings
        activeStepElement.querySelectorAll('.error-msg').forEach(el => el.remove());
        activeStepElement.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'focus:border-red-500');
            el.classList.add('border-gray-300', 'focus:border-blue-600');
        });
        activeStepElement.querySelectorAll('.focus\\:ring-red-200').forEach(el => {
            el.classList.remove('focus:ring-red-200');
        });

        // If step is 4, validate the RAB data
        if (stepNumber === 4) {
            let totalItems = 0;
            let hasInvalidItem = false;
            let errorMessage = '';

            for (const category in budgetData) {
                totalItems += budgetData[category].length;
                for (const item of budgetData[category]) {
                    const uraian = (item.uraian || '').trim();
                    const harga = parseFloat(item.harga) || 0;
                    const vol1 = parseFloat(item.vol1);
                    const vol2 = parseFloat(item.vol2);

                    let itemError = '';
                    let fieldClass = '';
                    let shortError = '';

                    if (!uraian) {
                        itemError = `Setiap item belanja pada kategori '${category}' harus memiliki uraian yang jelas.`;
                        fieldClass = '.uraian';
                        shortError = 'Wajib diisi';
                    } else if (harga <= 0) {
                        itemError = `Harga untuk item '${uraian}' pada kategori '${category}' harus lebih besar dari 0.`;
                        fieldClass = '.harga';
                        shortError = 'Harus > 0';
                    } else if (isNaN(vol1) || vol1 <= 0) {
                        itemError = `Volume 1 untuk item '${uraian}' pada kategori '${category}' harus lebih besar dari 0.`;
                        fieldClass = '.vol1';
                        shortError = 'Harus > 0';
                    } else if (!isNaN(vol2) && vol2 <= 0) {
                        itemError = `Volume 2 untuk item '${uraian}' pada kategori '${category}' harus lebih besar dari 0.`;
                        fieldClass = '.vol2';
                        shortError = 'Harus > 0';
                    }

                    if (itemError) {
                        hasInvalidItem = true;
                        errorMessage = itemError;

                        // Switch to the category containing the invalid item if needed
                        if (activeCategory !== category) {
                            activeCategory = category;
                            renderRabSidebar();
                            renderRabContent();
                        }

                        // Highlight specific input field and append warning below it
                        setTimeout(() => {
                            const row = activeStepElement.querySelector(`tr[data-item-id="${item.id}"]`);
                            if (row) {
                                const inputField = row.querySelector(fieldClass);
                                if (inputField) {
                                    inputField.classList.remove('border-gray-200');
                                    inputField.classList.add('border-red-500', 'focus:border-red-500');
                                    
                                    const td = inputField.parentElement;
                                    td.querySelectorAll('.error-msg').forEach(el => el.remove());
                                    
                                    const errorSpan = document.createElement('span');
                                    errorSpan.className = 'error-msg text-red-500 text-[10px] mt-1 block font-medium';
                                    errorSpan.textContent = shortError;
                                    td.appendChild(errorSpan);

                                    inputField.focus();
                                    inputField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }
                        }, 50);

                        break;
                    }
                }
                if (hasInvalidItem) break;
            }

            if (totalItems === 0) {
                errorMessage = 'Rincian Anggaran Biaya (RAB) minimal harus memiliki 1 item belanja yang valid.';
            }

            if (errorMessage) {
                const grandTotalContainer = activeStepElement.querySelector('.grand-total-container');
                if (grandTotalContainer) {
                    const errorBox = document.createElement('div');
                    errorBox.className = 'error-msg w-full bg-red-50 border border-red-300 text-red-700 p-4 rounded-xl mb-6 shadow-sm animate-fade-in flex items-center gap-3';
                    errorBox.innerHTML = `<i class="fas fa-exclamation-circle text-red-500 text-lg"></i> <span class="text-sm font-semibold">${errorMessage}</span>`;
                    grandTotalContainer.insertAdjacentElement('beforebegin', errorBox);
                    errorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            return true;
        }

        // 2. Find inputs to validate
        const inputs = activeStepElement.querySelectorAll('input, select, textarea');
        let firstErrorElement = null;

        inputs.forEach(input => {
            // Skip disabled/readonly inputs unless it's prodi (which might be disabled initially but is required)
            if (input.readOnly || (input.disabled && input.id !== 'prodi')) return;

            const isRequired = input.getAttribute('data-required') === 'true' || input.hasAttribute('required');
            const val = (input.value || '').trim();
            let hasError = false;
            let errorMsg = '';

            if (isRequired && val === '') {
                hasError = true;
                errorMsg = input.getAttribute('data-msg-required') || 'Bidang ini wajib diisi.';
            } else if (input.getAttribute('data-max-length')) {
                const maxLen = parseInt(input.getAttribute('data-max-length'));
                if (val.length > maxLen) {
                    hasError = true;
                    errorMsg = input.getAttribute('data-msg-max') || `Panjang maksimal ${maxLen} karakter.`;
                }
            }

            if (hasError) {
                isValid = false;

                // Style input border
                input.classList.remove('border-gray-300', 'focus:border-blue-600');
                input.classList.add('border-red-500', 'focus:border-red-500');
                if (input.classList.contains('focus:ring-2')) {
                    input.classList.add('focus:ring-red-200');
                }

                // Create error span
                const errorSpan = document.createElement('span');
                errorSpan.className = 'error-msg text-red-500 text-xs mt-1 block font-medium';
                errorSpan.textContent = errorMsg;

                // Find the appropriate container to append the error span below
                const container = input.closest('.relative:not(.select-wrapper)') || input.parentElement;
                container.appendChild(errorSpan);

                if (!firstErrorElement) {
                    firstErrorElement = input;
                }
            }
        });

        if (!isValid && firstErrorElement) {
            firstErrorElement.focus();
            firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        return isValid;
    }

    // ===================================
    // EVENT LISTENER NAVIGASI STEP
    // ===================================
    document.querySelectorAll('.btn-nav').forEach(button => {
        // --- TOMBOL SUBMIT (Step Terakhir) ---
        if (button.getAttribute('type') === 'submit') {
            button.addEventListener('click', (e) => {
                e.preventDefault();

                if (validateStep(4)) {
                    const namaKegiatanInput = document.getElementById('nama_kegiatan_kak');
                    const namaKegiatan = namaKegiatanInput ? namaKegiatanInput.value.trim() : 'Kegiatan Ini';

                    // Tampilkan Popup Kustom
                    Swal.fire({
                        title: 'Konfirmasi Pengajuan KAK?',
                        html: `Anda akan mengajukan KAK untuk:<br><div class="swal-kegiatan-nama">${namaKegiatan}</div>Data akan dikunci dan dikirim untuk verifikasi.`,
                        icon: 'info',
                        customClass: { popup: 'swal-konfirmasi' },
                        showCancelButton: true,
                        confirmButtonColor: '#3B82F6',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Ya, Ajukan Sekarang!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Memproses...',
                                html: 'Harap tunggu, data sedang disimpan.',
                                customClass: { popup: 'swal-loading' },
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });

                            // ===========================================
                            // TAMBAHANIN: KONVERSI BUDGET DATA KE JSON
                            // ===========================================
                            const rabInput = document.getElementById('rab_data_input');
                            if (rabInput) {
                                // Ubah object JS jadi string JSON
                                rabInput.value = JSON.stringify(budgetData);
                            }

                            if (kakFormElement) kakFormElement.submit();
                        }
                    });
                }
            });
            return;
        }

        // --- TOMBOL NAVIGASI BIASA ---
        button.addEventListener('click', () => {
            const targetStep = parseInt(button.dataset.targetStep);
            const direction = button.dataset.direction;

            // Tombol KEMBALI
            if (direction === 'prev') {
                const btnText = button.querySelector('.btn-text');
                const btnIcon = button.querySelector('.btn-icon');
                const originalText = btnText ? btnText.textContent : '';
                const originalIconClass = btnIcon ? btnIcon.className : '';
                if (btnText) btnText.textContent = 'Memuat...';
                if (btnIcon) btnIcon.className = 'fas fa-spinner fa-spin btn-icon';
                button.disabled = true;
                setTimeout(() => {
                    goToStep(targetStep);
                    if (btnText) btnText.textContent = originalText;
                    if (btnIcon) btnIcon.className = originalIconClass;
                    button.disabled = false;
                }, 300);
                return;
            }

            // Tombol LANJUT - dengan validasi dan transfer data
            if (validateStep(currentStep)) {
                // Transfer data dari Step 1 ke Step 2
                if (currentStep === 1 && targetStep === 2) {
                    transferDataToStep2();
                }

                const btnText = button.querySelector('.btn-text');
                const btnIcon = button.querySelector('.btn-icon');
                const originalText = btnText ? btnText.textContent : '';
                const originalIconClass = btnIcon ? btnIcon.className : '';
                if (btnText) btnText.textContent = 'Memuat...';
                if (btnIcon) btnIcon.className = 'fas fa-spinner fa-spin btn-icon';
                button.disabled = true;
                setTimeout(() => {
                    goToStep(targetStep);
                    if (btnText) btnText.textContent = originalText;
                    if (btnIcon) btnIcon.className = originalIconClass;
                    button.disabled = false;
                }, 300);
            }
        });
    });

    // ===================================
    // LOGIKA REPEATER (Tahapan & Indikator)
    // ===================================
    const tahapanContainer = document.getElementById('tahapan-container');
    const tambahTahapanBtn = document.getElementById('tambah-tahapan');
    const indikatorContainer = document.getElementById('indikator-container');
    const tambahIndikatorBtn = document.getElementById('tambah-indikator');

    function updateTahapanNumbers() {
        tahapanContainer?.querySelectorAll('.repeater-row-tahapan').forEach((row, index) => {
            const numberSpan = row.querySelector('.tahapan-number');
            if (numberSpan) numberSpan.textContent = (index + 1) + '.';
        });
    }

    function addRepeaterRow(container, templateHTML, callback) {
        if (!container) return;
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = templateHTML.trim();
        const newRow = tempDiv.firstChild;
        if (!newRow) return;

        newRow.style.opacity = '0';
        container.appendChild(newRow);
        if (callback) callback();

        void newRow.offsetWidth;
        newRow.classList.add('animate-slide-down-fade-in');
        newRow.style.opacity = '1';

        setTimeout(() => { newRow.classList.remove('animate-slide-down-fade-in'); }, 350);

        newRow.querySelectorAll('.floating-select').forEach(select => {
            select.setAttribute('filled', select.value !== '' ? 'true' : 'false');
            select.addEventListener('change', () => select.setAttribute('filled', select.value !== '' ? 'true' : 'false'));
        });
    }

    function removeRepeaterRow(button, callback) {
        const rowToRemove = button.closest('.repeater-row-tahapan, .repeater-row-indikator');
        if (!rowToRemove) return;

        rowToRemove.classList.add('animate-fade-out-scale');

        setTimeout(() => {
            rowToRemove.remove();
            if (callback) callback();
        }, 300);
    }

    tambahTahapanBtn?.addEventListener('click', () => addRepeaterRow(tahapanContainer, tahapanTemplateHTML, updateTahapanNumbers));
    tambahIndikatorBtn?.addEventListener('click', () => addRepeaterRow(indikatorContainer, indikatorTemplateHTML));

    kakFormElement?.addEventListener('click', function (event) {
        const removeBtn = event.target.closest('.remove-row-btn');
        if (removeBtn) {
            event.preventDefault();
            const isTahapan = removeBtn.closest('.repeater-row-tahapan');
            removeRepeaterRow(removeBtn, isTahapan ? updateTahapanNumbers : null);
        }
    });

    updateTahapanNumbers();

    document.querySelectorAll('.floating-select').forEach(select => {
        if (select.value) select.setAttribute('filled', 'true');
        select.addEventListener('change', () => select.setAttribute('filled', select.value !== '' ? 'true' : 'false'));
    });

    // ===================================
    // FIX DROPDOWN COLOR (Step 1 Dropdowns)
    // ===================================
    const dropdownsStep1 = document.querySelectorAll('#form-tahap-1 select');
    dropdownsStep1.forEach(select => {
        // Set initial color
        if (!select.value || select.value === '') {
            select.style.color = '#9ca3af'; // Gray for placeholder
        } else {
            select.style.color = '#1f2937'; // Dark gray for selected
        }

        // Update color on change
        select.addEventListener('change', function () {
            if (!this.value || this.value === '') {
                this.style.color = '#9ca3af'; // Gray
            } else {
                this.style.color = '#1f2937'; // Dark
            }
        });
    });

    // ===================================
    // LOGIKA MODAL INDIKATOR KINERJA UTAMA
    // ===================================
    const allIndicators = window.allIndicators || ["Mendapat Pekerjaan", "Melanjutkan studi", "Menjadi Wiraswasta", "Kegiatan luar prodi", "Prestasi", "Pengabdian Masyarakat"];
    let selectedIndicators = new Set();
    const openBtn = document.getElementById('open-indicator-modal-btn');
    const closeBtn = document.getElementById('close-indicator-modal-btn');
    const doneBtn = document.getElementById('done-indicator-modal-btn');
    const modalBackdrop = document.getElementById('indicator-modal-backdrop');
    const modalContent = document.getElementById('indicator-modal-content');
    const searchInput = document.getElementById('indicator-search-input');
    const listContainer = document.getElementById('indicator-list-container');
    const tagsContainer = document.getElementById('indicator-tags-container');
    const hiddenInput = document.getElementById('indikator_kinerja_hidden');

    function renderModalList(filter = '') {
        if (!listContainer) return;
        listContainer.innerHTML = '';
        const lowerCaseFilter = filter.toLowerCase();
        allIndicators.forEach(indicator => {
            if (indicator.toLowerCase().includes(lowerCaseFilter)) {
                const isChecked = selectedIndicators.has(indicator);
                listContainer.innerHTML += `<label class="flex items-center w-full p-3 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors duration-150"><input type="checkbox" value="${indicator}" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-offset-0 mr-3" ${isChecked ? 'checked' : ''}><span class="ml-3 text-sm font-medium text-gray-700">${indicator}</span></label>`;
            }
        });
    }

    function renderTags() {
        if (!tagsContainer || !hiddenInput) return;
        tagsContainer.innerHTML = '';
        if (selectedIndicators.size === 0) {
            tagsContainer.innerHTML = '<span class="text-sm text-gray-500 italic">Belum ada indikator dipilih.</span>';
        } else {
            selectedIndicators.forEach(indicator => {
                tagsContainer.innerHTML += `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 animate-reveal" style="animation-duration: 0.3s;">${indicator}<button type="button" class="remove-tag-btn text-blue-500 hover:text-blue-900 leading-none text-lg -mr-1" data-value="${indicator}" aria-label="Hapus ${indicator}">&times;</button></span>`;
            });
        }
        hiddenInput.value = Array.from(selectedIndicators).join(',');
    }

    function openModal() {
        if (!modalBackdrop || !modalContent || !searchInput) return;
        renderModalList(searchInput.value || '');
        modalBackdrop.classList.remove('hidden');
        modalContent.classList.remove('hidden');
        setTimeout(() => { modalBackdrop.classList.add('opacity-100'); modalContent.classList.remove('opacity-0', 'scale-95'); modalContent.classList.add('opacity-100', 'scale-100'); }, 10);
    }

    function closeModal() {
        if (!modalBackdrop || !modalContent) return;
        modalBackdrop.classList.remove('opacity-100');
        modalContent.classList.remove('opacity-100', 'scale-100'); modalContent.classList.add('opacity-0', 'scale-95');
        setTimeout(() => { modalBackdrop.classList.add('hidden'); modalContent.classList.add('hidden'); }, 300);
    }

    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    doneBtn?.addEventListener('click', closeModal);
    modalBackdrop?.addEventListener('click', closeModal);
    searchInput?.addEventListener('input', () => renderModalList(searchInput.value));
    listContainer?.addEventListener('change', (e) => { if (e.target.type === 'checkbox') { const value = e.target.value; if (e.target.checked) { selectedIndicators.add(value); } else { selectedIndicators.delete(value); } renderTags(); } });
    tagsContainer?.addEventListener('click', (e) => { const removeButton = e.target.closest('.remove-tag-btn'); if (removeButton) { const valueToRemove = removeButton.dataset.value; selectedIndicators.delete(valueToRemove); renderTags(); renderModalList(searchInput.value || ''); } });

    renderTags();

    // ===================================
    // LOGIKA FORM RAB (Tahap 4 - MODIFIED)
    // ===================================
    // Data Structure Updated for double volume (vol1, sat1, vol2, sat2)
    let budgetData = {
        "Belanja Barang": [],
        "Belanja Jasa": [],
        "Belanja Perjalanan": []
    };
    let activeCategory = "Belanja Barang";

    const rabSidebar = document.getElementById('category-sidebar');
    const rabContent = document.getElementById('rab-content');
    const addCategoryToggleBtnRAB = document.getElementById('add-category-toggle-btn');
    const categoryPopupRAB = document.getElementById('category-popup');
    const createCategoryBtnRAB = document.getElementById('create-category-btn');
    const newCategoryNameInputRAB = document.getElementById('new-category-name');
    const grandTotalDisplay = document.getElementById('grand-total-display');

    const getIconForCategory = (name) => { const ln = name.toLowerCase(); if (ln.includes('barang')) return 'fa-shopping-bag'; if (ln.includes('jasa')) return 'fa-concierge-bell'; if (ln.includes('perjalanan')) return 'fa-plane-departure'; return 'fa-folder'; };

    function renderRabSidebar() {
        if (!rabSidebar) return;
        const categories = Object.keys(budgetData);
        const sidebarContent = rabSidebar.querySelector('.flex.md\\:flex-col');
        if (!sidebarContent) return;
        sidebarContent.innerHTML = '';
        if (categories.length === 0) { sidebarContent.innerHTML = '<p class="p-3 text-sm text-gray-500 italic text-center w-full">Klik "Tambah Kategori".</p>'; activeCategory = null; return; }
        if (!activeCategory || !budgetData[activeCategory]) { activeCategory = categories[0]; }
        categories.forEach(cn => { const isA = cn === activeCategory ? 'bg-blue-100 text-blue-700 font-semibold shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200 md:border-transparent'; const iC = getIconForCategory(cn); sidebarContent.innerHTML += `<div class="category-sidebar-item flex-shrink-0 flex items-center gap-2 p-2 md:p-3 rounded-lg cursor-pointer ${isA} transition-colors duration-150 md:justify-between" data-category-name="${cn}"><div class="flex items-center gap-2 md:gap-3 overflow-hidden"><i class="fas ${iC} w-4 text-center flex-shrink-0"></i><span class="text-xs md:text-sm truncate" title="${cn}">${cn}</span></div><button type="button" class="actions text-gray-400 hover:text-red-500 ml-1 md:ml-2 flex-shrink-0 p-1 rounded hover:bg-red-100" title="Hapus Kategori"><i class="fas fa-trash-alt text-xs pointer-events-none"></i></button></div>`; });
    }

    function renderRabContent() {
        if (!rabContent) return;
        if (!activeCategory || !budgetData[activeCategory]) { rabContent.innerHTML = '<div class="p-10 text-center text-gray-500 bg-gray-50 rounded-lg italic">Pilih atau buat kategori, lalu klik "Tambah baris".</div>'; calculateTotals(); return; }
        const items = budgetData[activeCategory]; const iC = getIconForCategory(activeCategory); let trHTML = '';

        if (items.length > 0) {
            items.forEach(i => {
                // Using vol1, sat1, vol2, sat2
                trHTML += `<tr data-item-id="${i.id}" class="animate-reveal" style="animation-duration: 0.3s;">
                    <td><input type="text" class="uraian w-full p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.uraian || ''}" placeholder="Uraian"></td>
                    <td><input type="text" class="rincian w-full p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.rincian || ''}" placeholder="Rincian"></td>
                    <td><input type="number" min="0" class="vol1 w-16 p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.vol1 || 1}" placeholder="1"></td>
                    <td><input type="text" class="sat1 w-16 p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.sat1 || ''}" placeholder="Org"></td>
                    <td><input type="number" min="0" class="vol2 w-16 p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.vol2 || 1}" placeholder="1"></td>
                    <td><input type="text" class="sat2 w-16 p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.sat2 || ''}" placeholder="Kali"></td>
                    <td><input type="number" min="0" class="harga w-28 p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.harga || 0}" placeholder="0"></td>
                    <td class="total text-sm font-semibold text-gray-800 whitespace-nowrap px-2"></td>
                    <td class="action-icons text-center"><button type="button" class="delete-row-btn text-gray-400 hover:text-red-500 cursor-pointer p-1 rounded hover:bg-red-100" title="Hapus Baris"><i class="fas fa-trash-alt pointer-events-none"></i></button></td>
                </tr>`;
            });
        } else {
            trHTML = '<tr><td colspan="9"><div class="p-6 text-center text-gray-500 bg-gray-50 rounded-lg italic">Klik "Tambah baris".</div></td></tr>';
        }

        // Updated Table Header with 2 Volumes/Units
        rabContent.innerHTML = `<div class="accordion-item border border-gray-200 rounded-lg overflow-hidden animate-fade-in" style="animation-duration: 0.5s;">
            <header class="flex items-center p-4 bg-white border-b border-gray-200">
                <div class="accordion-icon flex-shrink-0 w-10 h-10 rounded-full grid place-items-center bg-blue-100 text-blue-600 mr-4"><i class="fas ${iC}"></i></div>
                <div class="accordion-title"><h3 class="text-base font-semibold text-gray-900">${activeCategory}</h3><p id="subtotal-header" class="text-xs text-gray-500"></p></div>
            </header>
            <div class="accordion-content p-4">
                <div class="overflow-x-auto">
                    <table class="rab-entry-table w-full min-w-[800px]">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[20%]">Uraian</th>
                                <th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[20%]">Rincian</th>
                                <th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[7%]">Vol 1</th>
                                <th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[7%]">Sat 1</th>
                                <th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[7%]">Vol 2</th>
                                <th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[7%]">Sat 2</th>
                                <th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[15%]">Harga (RP)</th>
                                <th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                                <th class="p-2 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="rab-table-body" class="divide-y divide-gray-100">${trHTML}</tbody>
                    </table>
                </div>
            </div>
            <footer class="accordion-footer flex justify-between items-center p-4 bg-gray-50 border-t border-gray-200">
                <div class="subtotal text-sm font-semibold text-gray-600">Subtotal Kategori: <span id="subtotal-display" class="text-gray-900"></span></div>
                <div class="footer-actions flex items-center gap-4"><button type="button" class="btn-primary inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-center text-white rounded-lg transition-all bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-sm hover:shadow-md" id="add-row-btn"><i class="fas fa-plus"></i> Tambah baris</button></div>
            </footer>
        </div>`;
        calculateTotals();
    }

    function calculateTotals() {
        let grandTotal = 0; let activeSub = 0;
        for (const cn in budgetData) {
            let catTotal = 0;
            budgetData[cn].forEach(i => {
                const v1 = parseFloat(i.vol1) || 0;
                const v2 = parseFloat(i.vol2) || 1; // Default vol2 to 1 if empty so it doesn't zero out results
                const p = parseFloat(i.harga) || 0;
                catTotal += v1 * v2 * p;
            });
            if (cn === activeCategory) activeSub = catTotal;
            grandTotal += catTotal;
        }
        const format = (v) => (typeof formatRupiah === 'function') ? formatRupiah(v) : `RP ${v.toLocaleString('id-ID')}`;
        const subD = document.getElementById('subtotal-display'); if (subD) subD.textContent = format(activeSub);
        const subH = document.getElementById('subtotal-header'); if (subH) subH.textContent = `Subtotal: ${format(activeSub)}`;
        if (grandTotalDisplay) grandTotalDisplay.textContent = format(grandTotal);
        const curRC = document.getElementById('rab-content');
        if (curRC) {
            curRC.querySelectorAll('#rab-table-body tr[data-item-id]').forEach(r => {
                const v1I = r.querySelector('.vol1');
                const v2I = r.querySelector('.vol2');
                const hI = r.querySelector('.harga');
                const tC = r.querySelector('.total');
                if (v1I && v2I && hI && tC) {
                    const v1 = parseFloat(v1I.value) || 0;
                    const v2 = parseFloat(v2I.value) || 1; // Default logic here too for display
                    const h = parseFloat(hI.value) || 0;
                    tC.textContent = format(v1 * v2 * h);
                }
            });
        }
    }

    if (rabSidebar && addCategoryToggleBtnRAB && categoryPopupRAB && createCategoryBtnRAB && newCategoryNameInputRAB && rabContent && grandTotalDisplay) {
        rabSidebar.addEventListener('click', (e) => { const ci = e.target.closest('.category-sidebar-item'); if (!ci) return; const cN = ci.dataset.categoryName; if (e.target.closest('.actions')) { if (confirm(`Hapus kategori "${cN}"?`)) { delete budgetData[cN]; const rK = Object.keys(budgetData); activeCategory = rK.length > 0 ? rK[0] : null; renderRabSidebar(); renderRabContent(); } } else { activeCategory = cN; renderRabSidebar(); renderRabContent(); } });
        addCategoryToggleBtnRAB.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); categoryPopupRAB.classList.toggle('invisible'); categoryPopupRAB.classList.toggle('opacity-0'); categoryPopupRAB.classList.toggle('-translate-y-2'); if (!categoryPopupRAB.classList.contains('invisible')) newCategoryNameInputRAB.focus(); });
        document.addEventListener('click', (e) => { if (categoryPopupRAB && !categoryPopupRAB.classList.contains('invisible') && !categoryPopupRAB.contains(e.target) && !addCategoryToggleBtnRAB.contains(e.target)) categoryPopupRAB.classList.add('invisible', 'opacity-0', '-translate-y-2'); });
        categoryPopupRAB.addEventListener('click', (e) => e.stopPropagation());
        createCategoryBtnRAB.addEventListener('click', (e) => { e.preventDefault(); const n = newCategoryNameInputRAB.value.trim(); if (n && !budgetData[n]) { budgetData[n] = []; activeCategory = n; renderRabSidebar(); renderRabContent(); newCategoryNameInputRAB.value = ''; categoryPopupRAB.classList.add('invisible', 'opacity-0', '-translate-y-2'); } else if (!n) alert("Nama tidak boleh kosong."); else alert("Nama sudah ada."); });
        newCategoryNameInputRAB.addEventListener('keypress', (e) => { if (e.key === 'Enter') createCategoryBtnRAB.click(); });
        rabContent.addEventListener('click', (e) => { if (e.target.closest('#add-row-btn')) { if (!activeCategory) { alert("Pilih atau buat kategori dulu."); return; } budgetData[activeCategory].push({ id: Date.now(), uraian: '', rincian: '', vol1: 1, sat1: '', vol2: 1, sat2: '', harga: 0 }); renderRabContent(); } if (e.target.closest('.delete-row-btn')) { const r = e.target.closest('tr'); if (!r || !r.dataset.itemId) return; const id = parseInt(r.dataset.itemId); budgetData[activeCategory] = budgetData[activeCategory].filter(i => i.id !== id); renderRabContent(); } });
        // Updated Event Listener for mapped inputs
        rabContent.addEventListener('input', (e) => {
            const t = e.target;
            const r = t.closest('tr');
            if (!r || !r.dataset.itemId) return;
            const id = parseInt(r.dataset.itemId);

            // Mapping classes to data properties
            const pM = { 'uraian': 'uraian', 'rincian': 'rincian', 'vol1': 'vol1', 'sat1': 'sat1', 'vol2': 'vol2', 'sat2': 'sat2', 'harga': 'harga' };
            const p = Object.keys(pM).find(k => t.classList.contains(k));

            const i = budgetData[activeCategory]?.find(item => item.id === id);
            if (i && p) {
                // Handle numbers vs text
                if (t.type === 'number') {
                    i[p] = parseFloat(t.value); // Keep empty or 0 logic inside calc
                } else {
                    i[p] = t.value;
                }
                calculateTotals();
            }
        });
    }

    // ===================================
    // LOGIKA INITIALIZATION (EDIT MODE)
    // ===================================
    function initEditMode() {
        // 1. Pre-fill Tahapan
        if (window.initialTahapanData && typeof window.initialTahapanData === 'object' && Object.keys(window.initialTahapanData).length > 0) {
            if (tahapanContainer) {
                tahapanContainer.innerHTML = '';
                Object.values(window.initialTahapanData).forEach(val => {
                    addRepeaterRow(tahapanContainer, tahapanTemplateHTML, () => {
                        const lastInput = tahapanContainer.querySelector('.repeater-row-tahapan:last-child input');
                        if (lastInput) lastInput.value = val;
                        updateTahapanNumbers();
                    });
                });
            }
        } else {
            // Default first row if empty
            if (tahapanContainer && tahapanContainer.children.length === 0) {
                addRepeaterRow(tahapanContainer, tahapanTemplateHTML, updateTahapanNumbers);
            }
        }

        // 2. Pre-fill Indikator
        if (window.initialIndikatorData && typeof window.initialIndikatorData === 'object' && Object.keys(window.initialIndikatorData).length > 0) {
            if (indikatorContainer) {
                indikatorContainer.innerHTML = '';
                Object.entries(window.initialIndikatorData).forEach(([bulan, data]) => {
                    addRepeaterRow(indikatorContainer, indikatorTemplateHTML, () => {
                        const row = indikatorContainer.querySelector('.repeater-row-indikator:last-child');
                        if (row) {
                            const bulanSelect = row.querySelector('select[name="indikator_bulan[]"]');
                            const namaInput = row.querySelector('input[name="indikator_nama[]"]');
                            const targetInput = row.querySelector('input[name="indikator_target[]"]');

                            if (bulanSelect) {
                                bulanSelect.value = bulan;
                                bulanSelect.setAttribute('filled', 'true');
                            }
                            if (namaInput) namaInput.value = (typeof data === 'object') ? data.deskripsi : data;
                            if (targetInput && typeof data === 'object') targetInput.value = data.target_persen;
                        }
                    });
                });
            }
        } else {
            // Default first row if empty
            if (indikatorContainer && indikatorContainer.children.length === 0) {
                addRepeaterRow(indikatorContainer, indikatorTemplateHTML);
            }
        }

        // 3. Pre-fill IKU
        if (window.initialIkuData && Array.isArray(window.initialIkuData)) {
            window.initialIkuData.forEach(item => selectedIndicators.add(item));
            renderTags();
        }

        // 4. Pre-fill RAB
        if (window.initialBudgetData && typeof window.initialBudgetData === 'object') {
            // Assign a unique temporary ID to each item if it doesn't have one
            let tempIdCounter = 1;
            for (const category in window.initialBudgetData) {
                if (Array.isArray(window.initialBudgetData[category])) {
                    window.initialBudgetData[category] = window.initialBudgetData[category].map(item => {
                        if (!item.hasOwnProperty('id') || item.id === undefined) {
                            item.id = Date.now() + tempIdCounter++;
                        }
                        return item;
                    });
                }
            }

            // Merge initial data into budgetData
            budgetData = { ...budgetData, ...window.initialBudgetData };

            // Set active category to first available
            const keys = Object.keys(budgetData);
            if (keys.length > 0) {
                activeCategory = keys[0];
            }

            renderRabSidebar();
            renderRabContent();
            calculateTotals();
        }
    }

    // Jalankan inisialisasi edit mode
    initEditMode();

});