/**
 * Memformat angka menjadi format mata uang Rupiah (RP xxx.xxx).
 * @param {number|string|null|undefined} angka Angka yang akan diformat.
 * @returns {string} String dalam format Rupiah.
 */
function formatRupiah(angka) {
  const number = Number(angka) || 0; // Pastikan tipe number, default ke 0 jika tidak valid
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0 // Tidak pakai desimal
  }).format(number).replace(/\s?Rp/, 'RP ').trim(); // Ganti Rp jadi RP (jika perlu)
}

// Anda bisa tambahkan fungsi helper lain di sini nanti