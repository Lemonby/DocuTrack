document.addEventListener('DOMContentLoaded', function () {
    const notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        return; // Tidak ada container notifikasi di halaman ini
    }

    const iconButton = document.getElementById('notification-icon-button');
    const dropdown = document.getElementById('notification-dropdown');
    const countElement = document.getElementById('notification-count');
    const listElement = document.getElementById('notification-list');
    const markAllReadBtn = document.getElementById('mark-all-as-read-btn');
    // const viewAllLink = document.getElementById('view-all-notifications-link'); // Jika diperlukan

    const API_BASE_URL = '/docutrack/public/api/notifikasi';

    let isDropdownOpen = false;

    // --- Fungsi-fungsi ---

    /**
     * Mengambil notifikasi dari server
     */
    async function fetchNotifications() {
        try {
            const response = await fetch(API_BASE_URL);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const responseData = await response.json();
            
            // Fix: Handle structure { success: true, data: { items: [], unread_count: 0 } }
            if (responseData.success && responseData.data) {
                updateUI(responseData.data);
            } else {
                console.warn("Format respon API tidak valid:", responseData);
            }
        } catch (error) {
            console.error("Gagal mengambil notifikasi:", error);
            // Tampilkan pesan error di UI jika diperlukan
            listElement.innerHTML = `<div class="text-center text-red-500 py-4">Gagal memuat notifikasi.</div>`;
        }
    }

    /**
     * Memperbarui UI dengan data notifikasi baru
     */
    function updateUI(data) {
        // Update jumlah notifikasi
        if (data.unread_count > 0) {
            countElement.textContent = data.unread_count;
            countElement.classList.remove('hidden');
        } else {
            countElement.classList.add('hidden');
        }

        // Update daftar notifikasi. Note: Backend key is 'items', JS was 'notifications'
        const notifications = data.items || [];
        
        if (notifications.length === 0) {
            listElement.innerHTML = `<div class="text-center text-gray-500 py-4">Tidak ada notifikasi.</div>`;
        } else {
            listElement.innerHTML = notifications.map(createNotificationItem).join('');
        }
    }

    /**
     * Membuat HTML untuk satu item notifikasi
     */
    function createNotificationItem(notification) {
        const isUnread = notification.status === 'BELUM_DIBACA';
        const readClass = isUnread ? 'bg-blue-50' : 'bg-white';
        const timeAgo = formatTimeAgo(notification.created_at);

        return `
            <a href="${notification.link || '#'}" 
               class="flex items-start px-4 py-3 hover:bg-gray-100 transition-colors duration-200 ${readClass}"
               data-id="${notification.id}"
               ${isUnread ? 'data-action="mark-read"' : ''}>
                <div class="flex-shrink-0">
                    <i class="fas ${getIconForType(notification.tipe_log)} text-blue-500 text-xl"></i>
                </div>
                <div class="ml-3 w-full">
                    <p class="text-sm text-gray-700">${notification.pesan}</p>
                    <p class="text-xs text-gray-500 mt-1">${timeAgo}</p>
                </div>
            </a>
        `;
    }

    /**
     * Mengembalikan kelas ikon berdasarkan tipe log
     */
    function getIconForType(type) {
        switch (type) {
            case 'APPROVAL': return 'fa-check-circle';
            case 'REJECTION': return 'fa-times-circle';
            case 'REVISION': return 'fa-edit';
            case 'SUBMISSION': return 'fa-file-upload';
            default: return 'fa-info-circle';
        }
    }

    /**
     * Menghitung waktu yang lalu (time ago)
     */
    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        let interval = seconds / 31536000;
        if (interval > 1) return Math.floor(interval) + " tahun lalu";
        interval = seconds / 2592000;
        if (interval > 1) return Math.floor(interval) + " bulan lalu";
        interval = seconds / 86400;
        if (interval > 1) return Math.floor(interval) + " hari lalu";
        interval = seconds / 3600;
        if (interval > 1) return Math.floor(interval) + " jam lalu";
        interval = seconds / 60;
        if (interval > 1) return Math.floor(interval) + " menit lalu";
        return Math.floor(seconds) + " detik lalu";
    }

    /**
     * Menandai notifikasi sebagai 'dibaca'
     */
    async function markAsRead(logId) {
        try {
            await fetch(`${API_BASE_URL}/baca/${logId}`, { method: 'POST' });
            // Refresh notifikasi setelah menandai
            fetchNotifications();
        } catch (error) {
            console.error(`Gagal menandai notifikasi ${logId} sebagai dibaca:`, error);
        }
    }

    /**
     * Menandai semua notifikasi sebagai 'dibaca'
     */
    async function markAllAsRead() {
        try {
            await fetch(`${API_BASE_URL}/baca-semua`, { method: 'POST' });
            // Refresh notifikasi setelah menandai
            fetchNotifications();
        } catch (error) {
            console.error("Gagal menandai semua notifikasi sebagai dibaca:", error);
        }
    }
    
    // --- Event Listeners ---

    // Toggle dropdown saat ikon lonceng diklik
    iconButton.addEventListener('click', (e) => {
        e.stopPropagation();
        isDropdownOpen = !isDropdownOpen;
        dropdown.classList.toggle('hidden', !isDropdownOpen);
        if (isDropdownOpen) {
            fetchNotifications(); // Muat notifikasi saat dropdown dibuka
        }
    });

    // Tutup dropdown jika klik di luar area notifikasi
    document.addEventListener('click', (e) => {
        if (!notificationContainer.contains(e.target)) {
            isDropdownOpen = false;
            dropdown.classList.add('hidden');
        }
    });

    // Handler untuk klik di dalam daftar notifikasi (untuk mark-read)
    listElement.addEventListener('click', (e) => {
        const targetLink = e.target.closest('a[data-action="mark-read"]');
        if (targetLink) {
            e.preventDefault(); // Mencegah navigasi langsung
            const logId = targetLink.dataset.id;
            markAsRead(logId).then(() => {
                // Navigasi setelah ditandai sebagai dibaca
                window.location.href = targetLink.href;
            });
        }
    });
    
    // Handler untuk tombol "Tandai semua dibaca"
    markAllReadBtn.addEventListener('click', () => {
        markAllAsRead();
    });

    // --- Inisialisasi ---
    
    // Ambil notifikasi saat halaman pertama kali dimuat
    fetchNotifications();

    // Set interval untuk refresh notifikasi setiap 1 menit (60000 ms)
    setInterval(fetchNotifications, 60000);

});
