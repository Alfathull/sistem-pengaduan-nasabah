# Pengujian Black-Box — SiPeNa

Pengujian fungsional sistem berdasarkan skenario penggunaan oleh ketiga role.
Kolom "Hasil Aktual" diisi berdasarkan hasil pengujian otomatis (Pest) dan pengujian manual.

| No | Fitur | Skenario | Hasil Diharapkan | Hasil Aktual | Status |
|----|-------|----------|------------------|--------------|--------|
| 1 | Registrasi Nasabah | Mengisi form daftar dengan data valid (nama, email, no. HP, jenis rekening, kata sandi) | Akun dibuat dengan role `nasabah` dan profil tersimpan | Akun dibuat, role nasabah, phone & account_type tersimpan | ✅ Sesuai |
| 2 | Registrasi Nasabah | Memilih jenis rekening tidak valid | Registrasi ditolak dengan pesan validasi | Validasi gagal, user tidak dibuat | ✅ Sesuai |
| 3 | Login | Masuk dengan kredensial benar | Diarahkan ke dashboard sesuai role | Redirect ke halaman sesuai role | ✅ Sesuai |
| 4 | Login | Masuk dengan kata sandi salah | Pesan error, gagal login | Login gagal dengan pesan | ✅ Sesuai |
| 5 | Buat Pengaduan | Nasabah mengirim pengaduan lengkap | Dapat nomor tiket format `PGD-YYYYMMDD-XXXX`, status "Baru" | Tiket dibuat, format nomor sesuai, status Baru | ✅ Sesuai |
| 6 | Buat Pengaduan | Mengirim form kosong | Semua field wajib tervalidasi (Bahasa Indonesia) | Pesan validasi muncul | ✅ Sesuai |
| 7 | Pelacakan | Nasabah membuka "Pengaduan Saya" | Daftar tiket miliknya dengan badge status | Daftar tampil dengan badge status | ✅ Sesuai |
| 8 | Otorisasi | Nasabah membuka tiket milik nasabah lain | Akses ditolak (403) | Mengembalikan 403 Forbidden | ✅ Sesuai |
| 9 | Pelacakan | Nasabah membuka detail tiket miliknya | Timeline riwayat status & tindak lanjut tampil | Timeline riwayat tampil | ✅ Sesuai |
| 10 | Real-time | Status diperbarui CS saat halaman nasabah terbuka | Halaman memperbarui status via polling 30 detik | `wire:poll.30s` memperbarui tampilan | ✅ Sesuai |
| 11 | Notifikasi | CS mengubah status dengan Customer Notification aktif | Nasabah menerima notifikasi in-app (lonceng) | Notifikasi database terkirim ke nasabah | ✅ Sesuai |
| 12 | Kelola Tiket | CS membuka daftar tiket, memfilter status/kategori/tanggal & mencari | Daftar terfilter sesuai kriteria | Filter & pencarian berfungsi | ✅ Sesuai |
| 13 | Validasi Tiket | CS set prioritas & ubah status ke "Divalidasi" | Status & prioritas tersimpan, riwayat tercatat | Perubahan & riwayat tersimpan | ✅ Sesuai |
| 14 | Eskalasi | CS set status "Dieskalasi" + pilih departemen + keterangan | Departemen tersimpan, eskalasi tercatat di riwayat | Departemen & riwayat eskalasi tersimpan | ✅ Sesuai |
| 15 | Tindak Lanjut | CS mencatat keterangan di setiap perubahan status | Keterangan tercatat di tabel riwayat | Keterangan tersimpan per perubahan | ✅ Sesuai |
| 16 | Tolak Tiket | CS menolak tiket tanpa mengisi alasan | Validasi gagal, alasan wajib diisi | Error validasi `rejection_reason` | ✅ Sesuai |
| 17 | Tutup Tiket | CS mengubah status ke "Selesai" | Tiket ditutup, tidak dapat diubah lagi | Tiket terkunci setelah ditutup | ✅ Sesuai |
| 18 | Master Data | CS menambah/ubah kategori & departemen | Data master tersimpan dan tampil di form | CRUD master data berfungsi | ✅ Sesuai |
| 19 | Kelola Pengguna | CS menambah akun dengan role tertentu | Akun & role tersimpan | User & role tersimpan | ✅ Sesuai |
| 20 | Otorisasi | Nasabah mengakses area CS | Akses ditolak (403) | Mengembalikan 403 Forbidden | ✅ Sesuai |
| 21 | Laporan | Pimpinan membuka dashboard rekap | Jumlah total, per status, kategori, departemen & grafik tren tampil | Statistik & grafik Chart.js tampil | ✅ Sesuai |
| 22 | Laporan | Pimpinan memfilter periode tanggal | Rekap menyesuaikan periode | Data terfilter sesuai periode | ✅ Sesuai |
| 23 | Export PDF | Pimpinan menekan "Export PDF" | File PDF rekap terunduh | Respons PDF (application/pdf) berhasil | ✅ Sesuai |
| 24 | Otorisasi | Pimpinan mencoba mengubah tiket | Tidak memiliki akses pengelolaan (403) | Mengembalikan 403, `manage` policy false | ✅ Sesuai |
| 25 | Responsif | Membuka aplikasi pada layar 360px | Tata letak tetap nyaman digunakan | Layout responsif (navbar/sidebar) | ✅ Sesuai |

## Catatan

- Skenario 1, 2, 5, 8, 9, 11, 14, 16, 23, 24 diverifikasi otomatis melalui Pest Feature test
  (`tests/Feature/ComplaintTest.php`, `tests/Feature/RegistrationTest.php`, `tests/Feature/SmokeRenderTest.php`).
- Skenario lainnya diverifikasi melalui pengujian manual pada peramban.
