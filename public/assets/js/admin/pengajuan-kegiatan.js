    // --- File: public/assets/js/page-scripts/pengajuan-kegiatan.js ---

    // Karena ini dimuat di footer, DOM sudah siap.
    (function() {
        "use strict"; // Mode ketat untuk menghindari error

        // ===================================
        // ELEMEN DOM UTAMA
        // ===================================
        const stageList = document.getElementById('stage-list');
        const stageForm = document.getElementById('stage-form');
        const btnToList = document.getElementById('back-to-list-btn');
        const startFormBtns = document.querySelectorAll('.btn-lanjut-form, .btn-rincian');
        const tabIndicators = document.querySelectorAll('.tab-indicator');
        const pelaksanaContainer = document.getElementById('pelaksana-container');
        const tambahPelaksanaBtn = document.getElementById('tambah-pelaksana');
        const searchInput = document.getElementById('search-kegiatan-input');
        const tableBody = document.querySelector('#stage-list tbody');
        const tableRows = tableBody ? Array.from(tableBody.querySelectorAll('tr')) : [];
        const progressLine = document.getElementById('tab-progress-line');

        // State Halaman
        let currentKegiatanStage = 'list'; // Default

        // ===================================
        // STATE MANAGEMENT (SWITCH TAMPILAN)
        // ===================================
        function updateStage(stageName) {
            if (!stageList || !stageForm) return; // Guard clause

            const isList = stageName === 'list'; // Step 1 aktif?
            const isForm = stageName === 'form'; // Step 2 aktif?

            // 1. Toggle Visibility Section
            stageList.classList.toggle('hidden', !isList);
            stageForm.classList.toggle('hidden', !isForm);

            // 2. Update Styling Stepper Visual (Logika Baru)
            const circle1 = document.getElementById('step-circle-1');
            const text1 = document.getElementById('step-text-1');
            const title1 = document.getElementById('step-title-1');
            const subtitle1 = document.getElementById('step-subtitle-1');
            const circle2 = document.getElementById('step-circle-2');
            const text2 = document.getElementById('step-text-2');
            const title2 = document.getElementById('step-title-2');
            const subtitle2 = document.getElementById('step-subtitle-2');
            
            // Pastikan semua elemen ada sebelum mengubah
            if (!circle1 || !text1 || !title1 || !subtitle1 || !circle2 || !text2 || !title2 || !subtitle2 || !progressLine) {
                console.error("Elemen stepper tidak ditemukan!");
                return;
            }

            if (isList) { 
                // --- STEP 1 (List) AKTIF ---
                // (Kembali menjadi angka 1)
                circle1.className = 'flex items-center justify-center w-12 h-12 rounded-full bg-white ring-4 ring-blue-500 shadow-xl shadow-blue-500/50 transition-all duration-300';
                text1.innerHTML = '<span class="font-bold md:font-extrabold text-xl md:text-2xl bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">1</span>';
                title1.className = 'block text-xs md:text-sm font-bold text-blue-600';
                subtitle1.className = 'block text-[10px] md:text-xs text-blue-500';
                subtitle1.textContent = 'Aktif';

                // --- Step 2: Non-Aktif (Berikutnya) ---
                circle2.className = 'flex items-center justify-center w-12 h-12 rounded-full bg-gray-200 text-gray-500 ring-4 ring-white group-hover:bg-gray-300 transition-all duration-300';
                text2.innerHTML = '<span class="font-medium md:font-bold text-lg md:text-xl">2</span>';
                title2.className = 'block text-xs md:text-sm font-medium text-gray-500 group-hover:text-gray-700';
                subtitle2.className = 'block text-[10px] md:text-xs text-gray-400';
                subtitle2.textContent = 'Berikutnya';
                
                // --- Garis Progress ---
                progressLine.style.width = '0%'; // Garis belum dimulai
                
            } else if (isForm) { 
                // --- STEP 2 (Form) AKTIF ---
                // Step 1: Selesai (Menjadi Centang)
                circle1.className = 'flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 text-white ring-4 ring-blue-100 group-hover:ring-blue-200 transition-all duration-300';
                text1.innerHTML = '<i class="fas fa-check text-lg md:text-xl"></i>';
                title1.className = 'block text-xs md:text-sm font-semibold text-gray-700';
                subtitle1.className = 'block text-[10px] md:text-xs text-gray-500';
                subtitle1.textContent = 'Selesai';

                // --- Step 2: Aktif ---
                circle2.className = 'flex items-center justify-center w-12 h-12 rounded-full bg-white ring-4 ring-blue-500 shadow-xl shadow-blue-500/50 transition-all duration-300';
                text2.innerHTML = '<span class="font-bold md:font-extrabold text-xl md:text-2xl bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text text-transparent">2</span>';
                title2.className = 'block text-xs md:text-sm font-bold text-blue-600';
                subtitle2.className = 'block text-[10px] md:text-xs text-blue-500';
                subtitle2.textContent = 'Aktif';
                
                // --- Garis Progress ---
                progressLine.style.width = '100%'; // Garis penuh
            }

            currentKegiatanStage = stageName; // Simpan state
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ===================================
        // LOGIKA PENCARIAN TABEL
        // ===================================
        function filterTable() {
            if (!searchInput || !tableBody) return;
            const filterText = searchInput.value.toLowerCase().trim();
            tableRows.forEach(row => {
                const nameCell = row.cells[1]; 
                if (nameCell) {
                    const name = nameCell.textContent.toLowerCase();
                    row.style.display = name.includes(filterText) ? '' : 'none';
                }
            });
        }
        searchInput?.addEventListener('input', filterTable);

        // ===================================
        // TEMPLATE REPEATER PELAKSANA
        // ===================================
        const pelaksanaTemplateHTML = `
            <div class="flex items-center gap-2 md:gap-3 repeater-row-pelaksana">
                <div class="relative flex-grow">
                    <input required type="text" name="pelaksana[]" class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
                    <label class="floating-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-4 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Nama Pelaksana Kegiatan</label>
                </div>
                <button type="button" class="text-red-500 hover:text-red-700 remove-row-btn-pelaksana flex-shrink-0 pt-3"><i class="fas fa-trash pointer-events-none"></i></button>
            </div>`;

        // ===================================
        // EVENT LISTENERS
        // ===================================

        // 1. Memulai Form (Tombol Rincian atau Lanjut di bawah tabel)
        startFormBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                updateStage('form');
            });
        });

        // 2. Kembali ke List
        btnToList?.addEventListener('click', () => {
            updateStage('list');
        });

        // 3. Klik pada Tab Stepper Navigasi
        tabIndicators.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault(); // Cegah link #
                const target = tab.dataset.target;
                if (target && target !== currentKegiatanStage) {
                    updateStage(target);
                }
            });
        });

        // 4. LOGIKA REPEATER PELAKSANA (Tambah)
        tambahPelaksanaBtn?.addEventListener('click', () => {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = pelaksanaTemplateHTML.trim();
            const newRow = tempDiv.firstChild;
            pelaksanaContainer?.appendChild(newRow);
        });

        // 5. Delegasi Event Hapus Baris (untuk repeater pelaksana)
        pelaksanaContainer?.addEventListener('click', (e) => {
            const removeBtn = e.target.closest('.remove-row-btn-pelaksana');
            if (removeBtn) {
                const rowToRemove = removeBtn.closest('.repeater-row-pelaksana');
                if (rowToRemove && pelaksanaContainer.children.length > 1) {
                    rowToRemove.remove();
                } else if (pelaksanaContainer.children.length <= 1) {
                    alert("Minimal harus ada satu Pelaksana Kegiatan.");
                }
            }
        });

        // 6. LOGIKA UPLOAD FILE
        const uploadInput = document.getElementById('upload_surat');
        const fileNameDisplay = document.getElementById('file_name_display');

        uploadInput?.addEventListener('change', () => {
            if (uploadInput.files.length > 0) {
                let fileName = uploadInput.files[0].name;
                if (fileName.length > 30) {
                    fileName = fileName.substring(0, 15) + '...' + fileName.substring(fileName.length - 10);
                }
                fileNameDisplay.value = fileName;
                fileNameDisplay.classList.remove('text-gray-700');
                fileNameDisplay.classList.add('text-black');
            } else {
                fileNameDisplay.value = "Belum ada file yang dipilih...";
                fileNameDisplay.classList.remove('text-black');
                fileNameDisplay.classList.add('text-gray-700');
            }
        });

        // 7. LOGIKA DATE PICKER (Flatpickr)
        // Pastikan Flatpickr JS dimuat di footer
        if (typeof flatpickr === 'function') {
            flatpickr("#tanggal_mulai", {
                altInput: true, altFormat: "j F Y", dateFormat: "Y-m-d", allowInput: false,
            });

            flatpickr("#tanggal_selesai", {
                altInput: true, altFormat: "j F Y", dateFormat: "Y-m-d", allowInput: false,
            });
        } else {
            console.error("Flatpickr library not loaded.");
        }
        
        // ===================================
        // INISIALISASI AWAL
        // ===================================
        updateStage('list'); // Tampilkan stage list saat halaman pertama kali dimuat

    })(); // Akhir IIFE (Immediately Invoked Function Expression)