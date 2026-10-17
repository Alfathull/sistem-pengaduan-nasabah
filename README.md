# SiPeNa — Sistem Informasi Pengaduan Nasabah

Prototipe akademik Capstone Project (Sistem Informasi, Universitas Terbuka):
**"Implementasi Sistem Informasi Pengaduan Nasabah Berbasis Web pada Layanan Bank Sumsel Babel Cabang Indralaya"**.

SiPeNa adalah aplikasi web untuk mengelola tiket pengaduan nasabah secara terintegrasi, transparan, dan real-time:
dari pengiriman pengaduan, validasi dan eskalasi ke departemen terkait, hingga penutupan tiket (ticket resolution).

## Fitur Utama

- **Nasabah**: registrasi/login, mengirim pengaduan (nomor tiket otomatis `PGD-YYYYMMDD-XXXX`), melacak status
  secara real-time, dan menerima notifikasi in-app.
- **Customer Service (CS)**: mengelola, memvalidasi, menetapkan prioritas, mengeskalasi ke departemen,
  mencatat tindak lanjut, menutup tiket, serta mengelola master kategori, departemen, dan akun pengguna.
- **Pimpinan**: melihat laporan rekapitulasi (grafik tren, per status/kategori/departemen) dan export PDF.

## Teknologi

- Laravel 13 + Livewire 4 (official Livewire Starter Kit)
- Flux UI + Tailwind CSS (responsive)
- MySQL
- spatie/laravel-permission (role: `nasabah`, `cs`, `pimpinan`)
- Chart.js (grafik laporan) & barryvdh/laravel-dompdf (export PDF)
- Pest (testing)

## Prasyarat

- PHP 8.3+
- Composer
- Node.js & NPM
- MySQL

## Langkah Instalasi

```bash
# 1. Install dependency PHP & JS
composer install
npm install

# 2. Siapkan environment
cp .env.example .env
php artisan key:generate
```

Konfigurasikan koneksi database pada file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipena
DB_USERNAME=root
DB_PASSWORD=

APP_LOCALE=id
```

Buat database `sipena` terlebih dahulu, lalu jalankan migrasi beserta data demo:

```bash
# 3. Migrasi + seed data demo
php artisan migrate:fresh --seed

# 4. Build aset frontend
npm run build

# 5. Jalankan aplikasi
php artisan serve
```

Buka `http://localhost:8000`.

> Untuk pengembangan, Anda dapat menjalankan `composer run dev` (menjalankan server, queue, dan Vite sekaligus),
> atau `npm run dev` di terminal terpisah.

## Akun Demo

Seeder membuat akun berikut (kata sandi semua akun: **`password`**):

| Role | Email | Kata Sandi |
|------|-------|------------|
| Customer Service | `cs@sipena.test` | `password` |
| Pimpinan | `pimpinan@sipena.test` | `password` |
| Nasabah | `nasabah1@sipena.test` | `password` |
| Nasabah | `nasabah2@sipena.test` | `password` |

Data demo juga menyertakan ±38 tiket pengaduan dengan variasi status, kategori, dan riwayat tindak lanjut.

## Pengujian

```bash
# Jalankan seluruh test
php artisan test

# Atau test fitur pengaduan saja
php artisan test tests/Feature/ComplaintTest.php
```

## Struktur Penting

```
app/
  Enums/           ComplaintStatus, ComplaintPriority, AccountType, UserRole
  Models/          User, Complaint, Category, Department, ComplaintHistory
  Services/        ComplaintService, TicketNumberGenerator, ComplaintReportService
  Policies/        ComplaintPolicy
  Http/
    Requests/      StoreComplaintRequest, UpdateComplaintStatusRequest
    Controllers/   Manager/ReportController (export PDF)
  Notifications/   ComplaintStatusUpdated (notifikasi in-app)
resources/views/
  pages/
    customer/      Area Nasabah (navbar)
    staff/         Area Customer Service (sidebar)
    manager/       Area Pimpinan (sidebar)
docs/              diagrams.md, blackbox-testing.md
```

## Dokumentasi

- Diagram perancangan (Use Case, Activity, ERD, DFD): [`docs/diagrams.md`](docs/diagrams.md)
- Pengujian black-box: [`docs/blackbox-testing.md`](docs/blackbox-testing.md)

---

> Prototipe akademik. Gunakan nama "SiPeNa"; tidak memakai logo resmi bank.
> Sistem tidak membahas transaksi perbankan dan merupakan alat bantu yang menyesuaikan prosedur bank.
