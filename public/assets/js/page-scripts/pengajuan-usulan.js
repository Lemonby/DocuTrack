document.addEventListener('DOMContentLoaded', () => {

    // ===================================
    // TEMPLATE STEPPER
    // ===================================
     const stepperTemplates = {
         1: `<nav aria-label="Progress"><ol role="list" class="relative z-0 flex items-center justify-between w-full max-w-3xl mx-auto"><div class="absolute left-0 top-6 w-full -translate-y-1/2 h-1.5 bg-gray-200 -z-10"></div><div class="stepper-line absolute left-0 top-6 w-0 -translate-y-1/2 h-1.5 -z-10 transition-all duration-700 ease-out bg-gradient-to-r from-blue-500 to-cyan-400 line-flow-animation"></div><li class="relative"><a href="#" class="group" aria-current="step"><div class="flex flex-col items-center w-24 md:w-40 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-white ring-4 ring-blue-500 shadow-xl shadow-blue-500/50 transition-all duration-300"><span class="font-bold md:font-extrabold text-xl md:text-2xl bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">1</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-bold text-blue-600">Kerangka Acuan</span><span class="block text-[10px] md:text-xs text-blue-500">Sedang diisi</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-24 md:w-40 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300"><span class="font-medium md:font-bold text-lg md:text-xl">2</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700">Indikator Kinerja</span><span class="block text-[10px] md:text-xs text-gray-400">Berikutnya</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-24 md:w-40 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300"><span class="font-medium md:font-bold text-lg md:text-xl">3</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700">Rincian Anggaran</span><span class="block text-[10px] md:text-xs text-gray-400">Berikutnya</span></div></div></a></li></ol></nav>`,
         2: `<nav aria-label="Progress"><ol role="list" class="relative z-0 flex items-center justify-between w-full max-w-3xl mx-auto"><div class="absolute left-0 top-6 w-full -translate-y-1/2 h-1.5 bg-gray-200 -z-10"></div><div class="stepper-line absolute left-0 top-6 w-1/2 -translate-y-1/2 h-1.5 -z-10 transition-all duration-700 ease-out bg-gradient-to-r from-blue-500 to-cyan-400 line-flow-animation"></div><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-24 md:w-40 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300"><i class="fas fa-check text-lg md:text-xl"></i></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-semibold text-gray-700">Kerangka Acuan</span><span class="block text-[10px] md:text-xs text-gray-500">Selesai</span></div></div></a></li><li class="relative"><a href="#" class="group" aria-current="step"><div class="flex flex-col items-center w-24 md:w-40 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-white ring-4 ring-blue-500 shadow-xl shadow-blue-500/50 transition-all duration-300"><span class="font-bold md:font-extrabold text-xl md:text-2xl bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">2</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-bold text-blue-600">Indikator Kinerja</span><span class="block text-[10px] md:text-xs text-blue-500">Sedang diisi</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-24 md:w-40 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300"><span class="font-medium md:font-bold text-lg md:text-xl">3</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700">Rincian Anggaran</span><span class="block text-[10px] md:text-xs text-gray-400">Berikutnya</span></div></div></a></li></ol></nav>`,
         3: `<nav aria-label="Progress"><ol role="list" class="relative z-0 flex items-center justify-between w-full max-w-3xl mx-auto"><div class="absolute left-0 top-6 w-full -translate-y-1/2 h-1.5 bg-gray-200 -z-10"></div><div class="stepper-line absolute left-0 top-6 w-full -translate-y-1/2 h-1.5 -z-10 transition-all duration-700 ease-out bg-gradient-to-r from-blue-500 to-cyan-400 line-flow-animation"></div><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-24 md:w-40 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300"><i class="fas fa-check text-lg md:text-xl"></i></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-semibold text-gray-700">Kerangka Acuan</span><span class="block text-[10px] md:text-xs text-gray-500">Selesai</span></div></div></a></li><li class="relative"><a href="#" class="group"><div class="flex flex-col items-center w-24 md:w-40 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300"><i class="fas fa-check text-lg md:text-xl"></i></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-semibold text-gray-700">Indikator Kinerja</span><span class="block text-[10px] md:text-xs text-gray-500">Selesai</span></div></div></a></li><li class="relative"><a href="#" class="group" aria-current="step"><div class="flex flex-col items-center w-24 md:w-40 text-center"><span class="flex items-center justify-center w-12 h-12 rounded-full bg-white ring-4 ring-blue-500 shadow-xl shadow-blue-500/50 transition-all duration-300"><span class="font-bold md:font-extrabold text-xl md:text-2xl bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">3</span></span><div class="mt-2 md:mt-3"><span class="block text-xs md:text-sm font-bold text-blue-600">Rincian Anggaran</span><span class="block text-[10px] md:text-xs text-blue-500">Sedang diisi</span></div></div></a></li></ol></nav>`
     };

    // ===================================
    // TEMPLATE REPEATER KAK
    // ===================================
     const tahapanTemplateHTML = `<div class="flex items-center gap-2 md:gap-3 repeater-row-tahapan border border-transparent p-3 mb-3"><span class="tahapan-number text-gray-500 font-medium pt-3 flex-shrink-0"></span><div class="relative flex-grow"><i class="fas fa-flag absolute top-3.5 left-3 text-gray-400 transition-colors duration-300 peer-focus:text-blue-600 pointer-events-none"></i><input required type="text" name="tahapan[]" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "><label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Tahapan Pelaksanaan</label></div><button type="button" class="text-red-500 hover:text-red-700 pt-3 remove-row-btn transition-colors flex-shrink-0"><i class="fas fa-trash pointer-events-none"></i></button></div>`;
     // [PENTING] Mengganti name="indikator_nama[]" menjadi name="indikator_keberhasilan[]" agar cocok dengan controller
     const indikatorTemplateHTML = `<div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-start md:items-center repeater-row-indikator border md:border-transparent border-gray-100 rounded-lg p-3 md:p-0 mb-3 md:mb-0"><div class="col-span-1 md:col-span-2 select-wrapper pb-4"><label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Bulan</label><div class="relative"><i class="fas fa-calendar-alt absolute top-1/2 -translate-y-1/2 left-3 text-gray-400 transition-colors duration-300 peer-focus-within:text-blue-600 pointer-events-none z-10"></i><select required name="indikator_bulan[]" class="floating-select block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" onchange="this.setAttribute('filled', this.value !== '' ? 'true' : 'false')"><option value="" selected></option><option value="1">Januari</option><option value="2">Februari</option><option value="3">Maret</option><option value="4">April</option><option value="5">Mei</option><option value="6">Juni</option><option value="7">Juli</option><option value="8">Agustus</option><option value="9">September</option><option value="10">Oktober</option><option value="11">November</option><option value="12">Desember</option></select><label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Pilih Bulan</label></div></div><div class="col-span-1 md:col-span-4 pb-4"><label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Indikator</label><div class="relative"><i class="fas fa-clipboard-check absolute top-3.5 left-3 text-gray-400 transition-colors duration-300 peer-focus:text-blue-600 pointer-events-none"></i><input required type="text" name="indikator_keberhasilan[]" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "><label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Indikator Keberhasilan</label></div></div><div class="col-span-1 md:col-span-1 pb-4"><label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Target (%)</label><div class="relative"><i class="fas fa-bullseye absolute top-3.5 left-3 text-gray-400 transition-colors duration-300 peer-focus:text-blue-600 pointer-events-none"></i><input required type="number" min="0" max="100" name="indikator_target[]" class="block w-full px-4 py-3.5 pl-10 pr-7 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "><label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Target</label><span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 pointer-events-none text-sm">%</span></div></div><div class="col-span-1 md:col-span-1 text-right md:text-center mt-2 md:mt-0 pt-3 md:pt-0"><button type="button" class="text-red-500 hover:text-red-700 remove-row-btn transition-colors"><i class="fas fa-trash pointer-events-none"></i></button></div></div>`;
    
    // ===================================
    // ELEMEN DOM UTAMA
    // ===================================
     const queueSection = document.getElementById('queue-section');
     const formSection = document.getElementById('form-section');
     const startFormBtn = document.getElementById('start-form-btn');
     const backToQueueBtn = document.getElementById('back-to-queue-btn');
     let currentStep = 1;
     const stepperContainer = document.getElementById('stepper-container');
     const formSteps = document.querySelectorAll('.form-step');
     const kakFormElement = document.getElementById('kak-form-element');

    // ===================================
    // LOGIKA PENCARIAN TABEL (BARU)
    // ===================================
    const searchInputKAK = document.getElementById('search-kak-input'); 
    const tableBodyKAK = document.querySelector('#queue-section tbody'); 
    const tableRowsKAK = tableBodyKAK ? Array.from(tableBodyKAK.querySelectorAll('tr')) : [];
    let emptyRow = tableBodyKAK ? tableBodyKAK.querySelector('td[colspan="5"]') : null; // Cari baris "kosong"

    function filterKakTable() {
        if (!searchInputKAK) return;
        const filterText = searchInputKAK.value.toLowerCase().trim();
        let hasVisibleRows = false;

        tableRowsKAK.forEach(row => {
            // Lewati baris "kosong"
            if (row.cells.length === 1 && row.cells[0].getAttribute('colspan') === '5') {
                return; 
            }

            const nameCell = row.cells[1]; // Kolom Nama Kegiatan
            if (nameCell) {
                const name = nameCell.textContent.toLowerCase();
                const isMatch = name.includes(filterText);
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) {
                    hasVisibleRows = true;
                }
            }
        });

        // Tampilkan atau sembunyikan pesan "kosong"
        if (emptyRow) {
            emptyRow.parentElement.style.display = hasVisibleRows ? 'none' : '';
        }
    }
    searchInputKAK?.addEventListener('input', filterKakTable);


    // ===================================
    // LOGIKA NAVIGASI (Antrian <-> Form)
    // ===================================
    if (startFormBtn && queueSection && formSection && backToQueueBtn && formSteps.length > 0) {
        startFormBtn.addEventListener('click', () => {
            queueSection.classList.add('animate-fade-out', 'pointer-events-none');
            formSection.style.opacity = '0';
            formSection.classList.remove('hidden');

            setTimeout(() => {
                queueSection.classList.add('hidden');
                queueSection.classList.remove('animate-fade-out', 'pointer-events-none');

                formSteps.forEach((step, index) => {
                    step.classList.toggle('active', index === 0);
                    step.classList.toggle('inactive', index !== 0);
                });
                currentStep = 1;
                renderStepper(currentStep);
                document.querySelector('.grand-total-container')?.classList.add('hidden');

                formSection.classList.add('animate-fade-in');
                formSection.style.opacity = '1';

                setTimeout(() => formSection.classList.remove('animate-fade-in'), 500);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 500);
        });

        backToQueueBtn.addEventListener('click', () => {
            formSection.classList.add('animate-fade-out', 'pointer-events-none');
            setTimeout(() => {
                formSection.classList.add('hidden');
                formSection.classList.remove('animate-fade-out', 'pointer-events-none', 'opacity-0');
                formSection.style.opacity = '';

                queueSection.classList.remove('hidden');
                queueSection.classList.add('animate-fade-in');

                currentStep = 1;

                setTimeout(() => queueSection.classList.remove('animate-fade-in'), 500);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }, 500);
        });
    }

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
            grandTotalContainer.classList.toggle('hidden', targetStep !== 3);
        }

        if (targetStep === 3) {
            renderRabSidebar();
            renderRabContent();
            calculateTotals();
        }

        if (window.scrollY > 100 && stepperContainer) window.scrollTo({ top: stepperContainer.offsetTop - 80, behavior: 'smooth' });
    }

    // ===================================
    // FUNGSI VALIDASI
    // ===================================
    function validateStep(stepNumber) {
        let isValid = true;
        const activeStepElement = document.getElementById(`form-tahap-${stepNumber}`);
        if (!activeStepElement) return false;

        activeStepElement.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
        activeStepElement.querySelectorAll('.ring-red-500').forEach(el => el.classList.remove('ring-red-500', 'ring-2'));

        const inputs = activeStepElement.querySelectorAll('input[required], textarea[required], select[required]');
        let firstErrorElement = null;

        inputs.forEach(input => {
            if (input.value.trim() === '') {
                isValid = false;
                const inputBorderEl = input.tagName === 'SELECT' ? (input.closest('.select-wrapper') || input) : input;
                inputBorderEl.classList.add('border-red-500');
                if (input.tagName === 'SELECT') inputBorderEl.classList.add('ring-2', 'ring-red-500');
                
                if (!firstErrorElement) firstErrorElement = input;
            }
        });

        if (!isValid) {
            // Menggunakan SweetAlert untuk notifikasi error
            Swal.fire({
                title: 'Form Belum Lengkap',
                text: 'Harap isi semua bidang yang wajib diisi (ditandai merah) sebelum melanjutkan.',
                icon: 'error',
                confirmButtonColor: '#3B82F6'
            });
            if (firstErrorElement) {
                firstErrorElement.focus();
                firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        return isValid;
    }
    
    // ===================================
    // DATA RAB (Tahap 3)
    // ===================================
    // Variabel ini akan menampung semua data RAB
    let budgetData = {
        "Belanja Barang": [],
        "Belanja Jasa": [],
        "Belanja Perjalanan": []
    };
    let activeCategory = "Belanja Barang"; // Default

    // ===================================
    // MODIFIKASI EVENT LISTENER NAVIGASI STEP
    // ===================================
    document.querySelectorAll('.btn-nav').forEach(button => {
        
        // --- 1. Tombol SUBMIT (Step Terakhir) ---
        if (button.getAttribute('type') === 'submit') {
            button.addEventListener('click', (e) => {
                e.preventDefault(); // Selalu hentikan submit
                
                // Validasi step terakhir (asumsi step 3)
                if (validateStep(3)) {
                    // Ambil nama kegiatan dari form
                    const namaKegiatanInput = document.getElementById('nama_kegiatan_kak');
                    const namaKegiatan = namaKegiatanInput.value.trim() || 'Kegiatan Ini';

                    // Tampilkan Popup Kustom
                    Swal.fire({
                        title: 'Konfirmasi Pengajuan KAK?',
                        html: `Anda akan mengajukan KAK untuk:<br><div class="swal-kegiatan-nama">${namaKegiatan}</div>Data akan dikunci dan dikirim untuk verifikasi.`,
                        icon: 'info',
                        customClass: { popup: 'swal-konfirmasi' }, // Terapkan style kustom
                        showCancelButton: true,
                        confirmButtonColor: '#3B82F6', // Biru
                        cancelButtonColor: '#6B7280', // Abu-abu
                        confirmButtonText: 'Ya, Ajukan Sekarang!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Tampilkan loading
                            Swal.fire({
                                title: 'Memproses...',
                                html: 'Harap tunggu, data sedang disimpan.',
                                customClass: { popup: 'swal-loading' },
                                allowOutsideClick: false,
                                didOpen: () => { Swal.showLoading(); }
                            });

                            // ==============================================
                            // === [PERUBAHAN UTAMA] KIRIM DATA DENGAN FETCH ===
                            // ==============================================

                            // 1. Siapkan FormData dari elemen form
                            const formData = new FormData(kakFormElement);

                            // 2. [PENTING] Tambahkan data RAB dari JavaScript (budgetData)
                            // Controller (yang saya buat) mengharapkan ini sbg JSON string
                            formData.append('rab_data', JSON.stringify(budgetData));
                            
                            // 3. [PENTING] Tentukan URL Controller
                            // Sesuaikan path ini jika struktur foldermu berbeda
                            const controllerURL = '/docutrack/src/controller/admin/pengajuan_usulan.php';
                            
                            // 4. Kirim data menggunakan fetch (AJAX)
                            fetch(controllerURL, {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                // Cek apakah respons-nya OK
                                if (!response.ok) {
                                    // Jika server mengembalikan error (spt 500), lempar error
                                    return response.json().then(err => { throw new Error(err.message || 'Error tidak diketahui dari server.'); });
                                }
                                // Jika OK, parse JSON-nya
                                return response.json();
                            })
                            .then(data => {
                                // 5. Handle respon SUKSES dari controller
                                if (data.status === 'success') {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#3B82F6',
                                    }).then(() => {
                                        // Sukses, reload halaman untuk melihat data baru di antrian
                                        window.location.reload(); 
                                    });
                                } else {
                                    // Handle jika controller mengembalikan status 'error'
                                    throw new Error(data.message || 'Controller mengembalikan error.');
                                }
                            })
                            .catch(error => {
                                // 6. Handle jika terjadi ERROR (fetch gagal atau server error)
                                Swal.fire({
                                    title: 'Gagal Menyimpan!',
                                    text: error.message,
                                    icon: 'error',
                                    confirmButtonColor: '#EF4444' // Merah
                                });
                            });
                            
                            // HAPUS BARIS INI:
                            // kakFormElement.submit(); // <-- INI YANG LAMA DAN SALAH
                        }
                    });
                }
                // Jika tidak valid, validateStep() akan menampilkan alert-nya sendiri
            });
            return;
        }

        // --- 2. Tombol Navigasi (Lanjut / Kembali) ---
        button.addEventListener('click', () => {
            const targetStep = parseInt(button.dataset.targetStep);
            const direction = button.dataset.direction;

            if (direction === 'prev') {
                // (Logika loading spinner)
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

            if (validateStep(currentStep)) {
                // (Logika loading spinner)
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
            if(numberSpan) numberSpan.textContent = (index + 1) + '.';
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
        if(callback) callback();

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
            if(callback) callback();
        }, 300);
    }

    tambahTahapanBtn?.addEventListener('click', () => addRepeaterRow(tahapanContainer, tahapanTemplateHTML, updateTahapanNumbers));
    tambahIndikatorBtn?.addEventListener('click', () => addRepeaterRow(indikatorContainer, indikatorTemplateHTML));

    kakFormElement?.addEventListener('click', function(event) {
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
    // LOGIKA MODAL INDIKATOR KINERJA UTAMA
    // ===================================
     const allIndicators = ["Mendapat Pekerjaan", "Melanjutkan studi", "Menjadi Wiraswasta", "Kegiatan luar prodi", "Prestasi", "Pengabdian Masyarakat"];
     let selectedIndicators = new Set();
     const openBtn = document.getElementById('open-indicator-modal-btn');
     const closeBtn = document.getElementById('close-indicator-modal-btn');
     const doneBtn = document.getElementById('done-indicator-modal-btn');
     const modalBackdrop = document.getElementById('indicator-modal-backdrop');
     const modalContent = document.getElementById('indicator-modal-content');
     const searchInput = document.getElementById('indicator-search-input');
     const listContainer = document.getElementById('indicator-list-container');
     const tagsContainer = document.getElementById('indicator-tags-container');
     // [PENTING] Ganti ID-nya agar cocok dengan form view
     const hiddenInput = document.getElementById('indikator_kinerja_hidden');

     function renderModalList(filter = '') {
        if(!listContainer) return;
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
    // LOGIKA FORM RAB (Tahap 3)
    // ===================================
     // Data RAB (`budgetData`) sudah dideklarasikan di atas
     
     // HAPUS ATAU BERI KOMENTAR PADA BARIS DI BAWAH INI:
     // let activeCategory = "Belanja Barang"; // Default ke kategori pertama <-- ERROR: Variabel ini sudah dideklarasikan di baris 206

     const rabSidebar = document.getElementById('category-sidebar');
     const rabContent = document.getElementById('rab-content');
     const addCategoryToggleBtnRAB = document.getElementById('add-category-toggle-btn');
     const categoryPopupRAB = document.getElementById('category-popup');
     const createCategoryBtnRAB = document.getElementById('create-category-btn');
     const newCategoryNameInputRAB = document.getElementById('new-category-name');
     const grandTotalDisplay = document.getElementById('grand-total-display');

     // formatRupiah diambil dari helpers.js
     const getIconForCategory = (name) => { const ln=name.toLowerCase(); if(ln.includes('barang'))return'fa-shopping-bag'; if(ln.includes('jasa'))return'fa-concierge-bell'; if(ln.includes('perjalanan'))return'fa-plane-departure'; return'fa-folder'; };

     function renderRabSidebar() {
         if (!rabSidebar) return;
         const categories = Object.keys(budgetData);
         const sidebarContent = rabSidebar.querySelector('.flex.md\\:flex-col');
         if (!sidebarContent) return;
         sidebarContent.innerHTML = '';
         if (categories.length === 0) { sidebarContent.innerHTML = '<p class="p-3 text-sm text-gray-500 italic text-center w-full">Klik "Tambah Kategori".</p>'; activeCategory = null; return; }
         if (!activeCategory || !budgetData[activeCategory]) { activeCategory = categories[0]; }
         categories.forEach(cn => { const isA=cn===activeCategory?'bg-blue-100 text-blue-700 font-semibold shadow-sm':'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200 md:border-transparent'; const iC=getIconForCategory(cn); sidebarContent.innerHTML += `<div class="category-sidebar-item flex-shrink-0 flex items-center gap-2 p-2 md:p-3 rounded-lg cursor-pointer ${isA} transition-colors duration-150 md:justify-between" data-category-name="${cn}"><div class="flex items-center gap-2 md:gap-3 overflow-hidden"><i class="fas ${iC} w-4 text-center flex-shrink-0"></i><span class="text-xs md:text-sm truncate" title="${cn}">${cn}</span></div><button type="button" class="actions text-gray-400 hover:text-red-500 ml-1 md:ml-2 flex-shrink-0 p-1 rounded hover:bg-red-100" title="Hapus Kategori"><i class="fas fa-trash-alt text-xs pointer-events-none"></i></button></div>`; });
     }
     function renderRabContent() {
         if (!rabContent) return;
         if (!activeCategory || !budgetData[activeCategory]) { rabContent.innerHTML = '<div class="p-10 text-center text-gray-500 bg-gray-50 rounded-lg italic">Pilih atau buat kategori, lalu klik "Tambah baris".</div>'; calculateTotals(); return; }
         const items = budgetData[activeCategory]; const iC=getIconForCategory(activeCategory); let trHTML='';
         if (items.length > 0) { items.forEach(i => { trHTML += `<tr data-item-id="${i.id}" class="animate-reveal" style="animation-duration: 0.3s;"><td><input type="text" class="uraian w-full p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.uraian||''}" placeholder="Cth: Konsumsi"></td><td><input type="text" class="rincian w-full p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.rincian||''}" placeholder="Cth: Snack Box"></td><td><input type="text" class="satuan w-24 p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.satuan||''}" placeholder="Cth: Box"></td><td><input type="number" min="0" class="volume w-20 p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.volume||1}"></td><td><input type="number" min="0" class="harga w-32 p-2 border border-gray-200 rounded-md text-sm text-gray-800 bg-white" value="${i.harga||0}" placeholder="0"></td><td class="total text-sm font-semibold text-gray-800 whitespace-nowrap px-2"></td><td class="action-icons text-center"><button type="button" class="delete-row-btn text-gray-400 hover:text-red-500 cursor-pointer p-1 rounded hover:bg-red-100" title="Hapus Baris"><i class="fas fa-trash-alt pointer-events-none"></i></button></td></tr>`; }); }
         else { trHTML = '<tr><td colspan="7"><div class="p-6 text-center text-gray-500 bg-gray-50 rounded-lg italic">Klik "Tambah baris".</div></td></tr>'; }
         rabContent.innerHTML = `<div class="accordion-item border border-gray-200 rounded-lg overflow-hidden animate-fade-in" style="animation-duration: 0.5s;"><header class="flex items-center p-4 bg-white border-b border-gray-200"><div class="accordion-icon flex-shrink-0 w-10 h-10 rounded-full grid place-items-center bg-blue-100 text-blue-600 mr-4"><i class="fas ${iC}"></i></div><div class="accordion-title"><h3 class="text-base font-semibold text-gray-900">${activeCategory}</h3><p id="subtotal-header" class="text-xs text-gray-500"></p></div></header><div class="accordion-content p-4"><div class="overflow-x-auto"><table class="rab-entry-table w-full min-w-[700px]"><thead><tr class="bg-gray-100"><th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[25%]">Uraian</th><th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[30%]">Rincian</th><th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[10%]">Satuan</th><th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[10%]">Volume</th><th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase w-[15%]">Harga (RP)</th><th class="p-2 text-left text-xs font-semibold text-gray-600 uppercase">Total</th><th class="p-2 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th></tr></thead><tbody id="rab-table-body" class="divide-y divide-gray-100">${trHTML}</tbody></table></div></div><footer class="accordion-footer flex justify-between items-center p-4 bg-gray-50 border-t border-gray-200"><div class="subtotal text-sm font-semibold text-gray-600">Subtotal Kategori: <span id="subtotal-display" class="text-gray-900"></span></div><div class="footer-actions flex items-center gap-4"><button type="button" class="btn-primary inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-center text-white rounded-lg transition-all bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-sm hover:shadow-md" id="add-row-btn"><i class="fas fa-plus"></i> Tambah baris</button></div></footer></div>`; calculateTotals();
     }
     function calculateTotals() {
         let grandTotal = 0; let activeSub = 0;
         for(const cn in budgetData){ let catTotal=0; budgetData[cn].forEach(i=>{catTotal+=(i.volume||0)*(i.harga||0);}); if(cn===activeCategory)activeSub=catTotal; grandTotal+=catTotal; }
         const format = (v) => (typeof formatRupiah === 'function') ? formatRupiah(v) : `RP ${v}`; // Fallback
         const subD=document.getElementById('subtotal-display'); if(subD)subD.textContent= format(activeSub);
         const subH=document.getElementById('subtotal-header'); if(subH)subH.textContent=`Subtotal: ${format(activeSub)}`;
         if(grandTotalDisplay)grandTotalDisplay.textContent= format(grandTotal);
         const curRC=document.getElementById('rab-content'); if(curRC){curRC.querySelectorAll('#rab-table-body tr[data-item-id]').forEach(r=>{const vI=r.querySelector('.volume');const hI=r.querySelector('.harga');const tC=r.querySelector('.total');if(vI&&hI&&tC){const v=parseFloat(vI.value)||0;const h=parseFloat(hI.value)||0; tC.textContent= format(v*h);}});}}

     if (rabSidebar && addCategoryToggleBtnRAB && categoryPopupRAB && createCategoryBtnRAB && newCategoryNameInputRAB && rabContent && grandTotalDisplay) {
         rabSidebar.addEventListener('click', (e) => { const ci=e.target.closest('.category-sidebar-item'); if(!ci)return; const cN=ci.dataset.categoryName; if(e.target.closest('.actions')){if(confirm(`Hapus kategori "${cN}"?`)){delete budgetData[cN]; const rK=Object.keys(budgetData); activeCategory=rK.length>0?rK[0]:null; renderRabSidebar(); renderRabContent();}}else{activeCategory=cN; renderRabSidebar(); renderRabContent();} });
         addCategoryToggleBtnRAB.addEventListener('click', (e) => { e.stopPropagation(); categoryPopupRAB.classList.toggle('invisible'); categoryPopupRAB.classList.toggle('opacity-0'); categoryPopupRAB.classList.toggle('-translate-y-2'); if(!categoryPopupRAB.classList.contains('invisible')) newCategoryNameInputRAB.focus(); });
         document.addEventListener('click', (e) => { if (categoryPopupRAB && !categoryPopupRAB.classList.contains('invisible') && !categoryPopupRAB.contains(e.target) && !addCategoryToggleBtnRAB.contains(e.target)) categoryPopupRAB.classList.add('invisible', 'opacity-0', '-translate-y-2');});
         categoryPopupRAB.addEventListener('click', (e) => e.stopPropagation());
         createCategoryBtnRAB.addEventListener('click', () => { const n=newCategoryNameInputRAB.value.trim(); if(n&&!budgetData[n]){budgetData[n]=[]; activeCategory=n; renderRabSidebar(); renderRabContent(); newCategoryNameInputRAB.value=''; categoryPopupRAB.classList.add('invisible','opacity-0','-translate-y-2');}else if(!n)alert("Nama tidak boleh kosong.");else alert("Nama sudah ada."); });
         newCategoryNameInputRAB.addEventListener('keypress', (e) => { if (e.key === 'Enter') createCategoryBtnRAB.click(); });
         rabContent.addEventListener('click', (e) => { if(e.target.closest('#add-row-btn')){if(!activeCategory){alert("Pilih atau buat kategori dulu."); return;} budgetData[activeCategory].push({id:Date.now(), uraian:'',rincian:'',satuan:'',volume:1,harga:0}); renderRabContent();} if(e.target.closest('.delete-row-btn')){const r=e.target.closest('tr'); if(!r||!r.dataset.itemId)return; const id=parseInt(r.dataset.itemId); budgetData[activeCategory]=budgetData[activeCategory].filter(i=>i.id!==id); renderRabContent();} });
         rabContent.addEventListener('input', (e) => { const t=e.target; const r=t.closest('tr'); if(!r||!r.dataset.itemId)return; const id=parseInt(r.dataset.itemId); const pM={'uraian':'uraian','rincian':'rincian','satuan':'satuan','volume':'volume','harga':'harga'}; const p=Object.keys(pM).find(k=>t.classList.contains(k)); const i=budgetData[activeCategory]?.find(item=>item.id===id); if(i&&p){i[p] = t.type==='number'?(parseFloat(t.value)||0):t.value; calculateTotals();} });
     }

     // --- Inisialisasi Flatpickr ---
     // (Tidak ada input tanggal di form KAK ini, jadi dikosongkan)

}); // Akhir DOMContentLoaded