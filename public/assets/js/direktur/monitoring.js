document.addEventListener('DOMContentLoaded', () => {
    const filterTabs = document.querySelectorAll('.riwayat-filter-tab');
    const statusInput = document.getElementById('status-input');
    const jurusanFilter = document.getElementById('jurusan-filter');
    const searchInput = document.getElementById('search-monitoring-input');
    const form = document.getElementById('filter-form');
    const loadingSpinner = document.getElementById('loading-spinner');
    const paginationLinks = document.querySelectorAll('nav a');
    
    // Fungsi untuk show loading dengan smooth fade-in
    function showLoading() {
        if (loadingSpinner) {
            loadingSpinner.classList.remove('hidden');
            loadingSpinner.classList.add('flex');
            // Trigger fade-in animation
            setTimeout(() => {
                loadingSpinner.style.opacity = '1';
            }, 10);
        }
    }
    
    // Event listener untuk tab filter dengan animasi
    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Tambahkan smooth transition pada tab
            filterTabs.forEach(t => t.classList.remove('active-tab'));
            tab.classList.add('active-tab');
            
            statusInput.value = tab.dataset.status;
            showLoading();
            
            // Delay kecil untuk animasi smooth
            setTimeout(() => {
                form.submit();
            }, 150);
        });
    });
    
    // Event listener untuk dropdown jurusan
    jurusanFilter.addEventListener('change', () => {
        showLoading();
        setTimeout(() => {
            form.submit();
        }, 150);
    });
    
    // Debounce untuk search input dengan loading indicator
    let searchTimeout;
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            showLoading();
            setTimeout(() => {
                form.submit();
            }, 150);
        }, 500);
    });
    
    // Tambahkan loading pada pagination links
    paginationLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            showLoading();
            
            // Navigate setelah loading muncul
            setTimeout(() => {
                window.location.href = link.href;
            }, 150);
        });
    });
    
    // Fade out loading setelah halaman dimuat
    window.addEventListener('load', () => {
        if (loadingSpinner) {
            loadingSpinner.style.opacity = '0';
            setTimeout(() => {
                loadingSpinner.classList.add('hidden');
                loadingSpinner.classList.remove('flex');
            }, 300);
        }
    });
});