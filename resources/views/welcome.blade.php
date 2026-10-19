<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('SiPeNa — Sistem Informasi Pengaduan Nasabah') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white font-sans text-ink antialiased">

        {{-- ─── Navbar ─────────────────────────────────────────── --}}
        <header
            id="site-header"
            class="sticky top-0 z-50 border-b border-transparent bg-white transition-colors duration-200"
        >
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3.5 sm:px-6">
                <a href="#top" aria-label="SiPeNa — beranda">
                    <x-sipena-logo variant="full" tone="dark" />
                </a>

                <nav class="hidden items-center gap-7 text-sm font-medium text-muted md:flex" aria-label="Navigasi utama">
                    <a href="#layanan" class="transition hover:text-ink">{{ __('Layanan') }}</a>
                    <a href="#alur" class="transition hover:text-ink">{{ __('Alur') }}</a>
                    <a href="#lacak" class="transition hover:text-ink">{{ __('Lacak') }}</a>
                    <a href="#faq" class="transition hover:text-ink">{{ __('FAQ') }}</a>
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-[10px] bg-primary-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-[10px] px-4 py-2 text-sm font-semibold text-ink transition hover:bg-primary-50">
                            {{ __('Masuk') }}
                        </a>
                        <a href="{{ route('register') }}" class="rounded-[10px] bg-primary-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700">
                            {{ __('Daftar') }}
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <main id="top">
            {{-- ─── Hero ───────────────────────────────────────── --}}
            <section class="bg-dotgrid relative overflow-hidden bg-surface">
                <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-12 lg:py-24">
                    {{-- Kiri --}}
                    <div class="lg:col-span-6">
                        <span class="inline-flex items-center gap-2 rounded-full border border-line bg-white px-3 py-1 text-xs font-medium text-muted">
                            <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>
                            {{ __('Bank Sumsel Babel · Cabang Indralaya') }}
                        </span>

                        <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight text-ink sm:text-5xl">
                            {{ __('Sampaikan keluhan.') }}<br>
                            {{ __('Pantau penyelesaiannya.') }}
                        </h1>

                        <p class="mt-5 max-w-md text-base leading-relaxed text-muted">
                            {{ __('SiPeNa mencatat pengaduan layanan perbankan Anda, memberi nomor tiket, dan menampilkan progres penanganannya secara terbuka.') }}
                        </p>

                        <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('register') }}" class="rounded-[10px] bg-primary-800 px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-primary-700">
                                {{ __('Buat Pengaduan') }}
                            </a>
                            <a href="{{ route('login') }}" class="rounded-[10px] border border-line bg-white px-6 py-3 text-center text-sm font-semibold text-ink transition hover:border-primary-200 hover:bg-primary-50">
                                {{ __('Masuk ke Akun') }}
                            </a>
                        </div>

                        <ul class="mt-8 grid gap-3 sm:grid-cols-3">
                            @foreach ([
                                __('Nomor tiket otomatis'),
                                __('Status bisa dipantau'),
                                __('Riwayat tindak lanjut tercatat'),
                            ] as $point)
                                <li class="flex items-start gap-2 text-sm text-ink">
                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-gold-500" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="m5 10 3.2 3.2L15 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Kanan: mockup produk (HTML/Tailwind, bukan gambar) --}}
                    <div class="relative lg:col-span-6">
                        <div class="relative mx-auto max-w-md">
                            {{-- Kartu tumpuk di belakang --}}
                            <div class="absolute -right-3 top-6 hidden h-full w-full rotate-[3deg] rounded-2xl border border-line bg-white/70 sm:block"></div>

                            {{-- Kartu tiket utama --}}
                            <div class="relative -rotate-[1.5deg] rounded-2xl border border-line bg-white p-5 shadow-card">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-800 text-white">
                                            <x-app-logo-icon class="h-5 w-5" />
                                        </span>
                                        <div>
                                            <p class="font-mono text-[13px] font-semibold text-ink tabular-nums">PGD-20261017-0012</p>
                                            <p class="text-xs text-muted">{{ __('Mobile Banking') }}</p>
                                        </div>
                                    </div>
                                    <x-status-badge :status="\App\Enums\ComplaintStatus::Diproses" />
                                </div>

                                <div class="mt-4 rounded-xl bg-surface p-3 text-sm text-ink">
                                    {{ __('Transfer antar bank tertunda, saldo sudah terpotong.') }}
                                </div>

                                {{-- Mini timeline 3 langkah --}}
                                <div class="mt-5">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-muted">{{ __('Riwayat') }}</p>
                                    <ol class="mt-3 space-y-3">
                                        @foreach ([
                                            [\App\Enums\ComplaintStatus::Baru, '17 Okt 09:12', true],
                                            [\App\Enums\ComplaintStatus::Divalidasi, '17 Okt 10:40', true],
                                            [\App\Enums\ComplaintStatus::Diproses, '17 Okt 13:05', false],
                                        ] as [$st, $time, $done])
                                            <li class="flex items-center gap-3">
                                                <span @class([
                                                    'flex h-5 w-5 shrink-0 items-center justify-center rounded-full',
                                                    'bg-gold-500 text-white' => $done,
                                                    'border-2 border-primary-600 bg-white' => ! $done,
                                                ])>
                                                    @if ($done)
                                                        <svg class="h-3 w-3" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m4 8 2.5 2.5L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                    @endif
                                                </span>
                                                <x-status-badge :status="$st" />
                                                <span class="ms-auto text-xs text-muted tabular-nums">{{ $time }}</span>
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ─── Strip kategori layanan ──────────────────────── --}}
            <section class="border-y border-line bg-white" aria-label="Kategori layanan">
                <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
                    <p class="text-center text-xs font-semibold uppercase tracking-wide text-muted">
                        {{ __('Kategori pengaduan yang dapat disampaikan') }}
                    </p>
                    <div class="mt-4 flex flex-wrap justify-center gap-2.5">
                        @foreach ([
                            'Cek Saldo', 'Mutasi Rekening', 'ATM/CDM/CRM', 'Mobile Banking',
                            'SMS Banking', 'Internet Banking', 'EDC', 'BSB Lur',
                        ] as $cat)
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-line bg-surface px-3.5 py-1.5 text-sm text-ink">
                                <span class="h-1.5 w-1.5 rounded-full bg-primary-600"></span>
                                {{ $cat }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- ─── Alur pengaduan ──────────────────────────────── --}}
            <section id="alur" class="scroll-mt-20 bg-surface">
                <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:py-20">
                    <div class="max-w-2xl">
                        <h2 class="text-3xl font-extrabold tracking-tight text-ink">{{ __('Alur Pengaduan') }}</h2>
                        <p class="mt-3 text-muted">{{ __('Setiap tiket melewati tahapan yang sama, dari pengiriman hingga selesai ditangani.') }}</p>
                    </div>

                    <ol class="relative mt-10 grid gap-8 lg:grid-cols-4">
                        {{-- garis penghubung (desktop) --}}
                        <div class="absolute left-0 right-0 top-6 hidden h-px bg-line lg:block" aria-hidden="true"></div>
                        @foreach ([
                            ['01', __('Kirim pengaduan'), __('Isi formulir, sistem membuat nomor tiket otomatis.')],
                            ['02', __('Divalidasi CS'), __('Customer Service memeriksa dan menetapkan prioritas.')],
                            ['03', __('Diproses / eskalasi'), __('Ditangani, bila perlu dieskalasi ke departemen terkait.')],
                            ['04', __('Selesai'), __('Tiket ditutup dengan catatan tindak lanjut.')],
                        ] as [$no, $title, $desc])
                            <li class="relative">
                                <span class="relative z-10 inline-flex h-12 w-12 items-center justify-center rounded-xl border border-line bg-white text-xl font-extrabold tabular-nums text-gold-500 shadow-[0_1px_2px_rgba(11,31,58,0.04)]">
                                    {{ $no }}
                                </span>
                                <h3 class="mt-4 text-base font-bold text-ink">{{ $title }}</h3>
                                <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ $desc }}</p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </section>

            {{-- ─── Layanan (bento) ─────────────────────────────── --}}
            <section id="layanan" class="scroll-mt-20 bg-white">
                <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:py-20">
                    <div class="max-w-2xl">
                        <h2 class="text-3xl font-extrabold tracking-tight text-ink">{{ __('Untuk Siapa SiPeNa?') }}</h2>
                        <p class="mt-3 text-muted">
                            {{ __('SiPeNa adalah kanal pendukung yang melengkapi jalur pengaduan bank yang sudah ada, dengan peran yang jelas untuk setiap pengguna.') }}
                        </p>
                    </div>

                    <div class="mt-10 grid gap-5 lg:grid-cols-3 lg:grid-rows-2">
                        {{-- Nasabah: kartu besar --}}
                        <div class="rounded-2xl border border-line bg-primary-800 p-6 text-white lg:col-span-2 lg:row-span-2">
                            <span class="inline-flex rounded-lg bg-white/10 p-2.5 text-gold-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3.75h6m-9 5.25 3.3-2.4H18a1.5 1.5 0 0 0 1.5-1.5V6A1.5 1.5 0 0 0 18 4.5H6A1.5 1.5 0 0 0 4.5 6v12l.3-.75Z"/></svg>
                            </span>
                            <h3 class="mt-5 text-xl font-bold">{{ __('Nasabah') }}</h3>
                            <p class="mt-2 max-w-md text-sm leading-relaxed text-white/75">
                                {{ __('Mengirim pengaduan layanan, menerima nomor tiket, lalu memantau statusnya secara real-time beserta riwayat tindak lanjut di halaman "Pengaduan Saya".') }}
                            </p>
                            <ul class="mt-6 grid gap-2 text-sm text-white/80 sm:grid-cols-2">
                                @foreach ([__('Formulir pengaduan ringkas'), __('Pelacakan status tiket'), __('Notifikasi perubahan'), __('Riwayat tersimpan rapi')] as $f)
                                    <li class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gold-400" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 10 3.2 3.2L15 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        {{ $f }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Customer Service --}}
                        <div class="rounded-2xl border border-line bg-white p-6 shadow-card">
                            <span class="inline-flex rounded-lg bg-primary-50 p-2.5 text-primary-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            </span>
                            <h3 class="mt-4 text-lg font-bold text-ink">{{ __('Customer Service') }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-muted">
                                {{ __('Memvalidasi tiket, mengeskalasi ke departemen terkait, dan mencatat tindak lanjut hingga tiket ditutup.') }}
                            </p>
                        </div>

                        {{-- Pimpinan --}}
                        <div class="rounded-2xl border border-line bg-white p-6 shadow-card">
                            <span class="inline-flex rounded-lg bg-primary-50 p-2.5 text-primary-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.5 8.25 8.25l3 3L18 4.5M3 19.5h18"/></svg>
                            </span>
                            <h3 class="mt-4 text-lg font-bold text-ink">{{ __('Pimpinan') }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed text-muted">
                                {{ __('Melihat rekapitulasi pengaduan per status, kategori, dan departemen, serta mengunduh laporan dalam format PDF.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ─── Lacak pengaduan ─────────────────────────────── --}}
            <section id="lacak" class="scroll-mt-20 bg-surface">
                <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                    <div class="overflow-hidden rounded-2xl border border-line bg-white shadow-card">
                        <div class="grid items-center gap-8 p-8 sm:p-10 lg:grid-cols-2">
                            <div>
                                <h2 class="text-2xl font-extrabold tracking-tight text-ink">{{ __('Sudah pernah mengadu? Pantau statusnya.') }}</h2>
                                <p class="mt-3 text-muted">
                                    {{ __('Masuk ke akun Anda dan buka halaman "Pengaduan Saya" untuk melihat status terkini dan riwayat tindak lanjut setiap tiket.') }}
                                </p>
                                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                    <a href="{{ route('login') }}" class="rounded-[10px] bg-primary-800 px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-primary-700">
                                        {{ __('Masuk untuk Melacak') }}
                                    </a>
                                    <a href="{{ route('register') }}" class="rounded-[10px] border border-line px-6 py-3 text-center text-sm font-semibold text-ink transition hover:bg-primary-50">
                                        {{ __('Belum punya akun?') }}
                                    </a>
                                </div>
                            </div>
                            <div class="rounded-2xl border border-line bg-surface p-5">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-[13px] font-semibold text-ink tabular-nums">PGD-20261012-0007</span>
                                    <x-status-badge :status="\App\Enums\ComplaintStatus::Selesai" />
                                </div>
                                <p class="mt-2 text-sm text-ink">{{ __('Kartu ATM tertelan di mesin') }}</p>
                                <div class="mt-4 flex items-center gap-2 text-[11px] text-muted">
                                    <span class="h-1.5 w-1.5 rounded-full bg-success"></span>{{ __('Divalidasi') }}
                                    <span class="h-px flex-1 bg-line"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-success"></span>{{ __('Diproses') }}
                                    <span class="h-px flex-1 bg-line"></span>
                                    <span class="h-1.5 w-1.5 rounded-full bg-success"></span>{{ __('Selesai') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ─── FAQ ─────────────────────────────────────────── --}}
            <section id="faq" class="scroll-mt-20 bg-white">
                <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-20">
                    <h2 class="text-center text-3xl font-extrabold tracking-tight text-ink">{{ __('Pertanyaan Umum') }}</h2>

                    <div class="mt-10 divide-y divide-line overflow-hidden rounded-2xl border border-line">
                        @foreach ([
                            [__('Apa saja yang bisa diadukan?'), __('Keluhan seputar layanan perbankan seperti ATM/CDM/CRM, mobile & internet banking, SMS banking, EDC, cek saldo, mutasi rekening, BSB Lur, dan lainnya. SiPeNa tidak memproses transaksi perbankan.')],
                            [__('Berapa lama pengaduan ditangani?'), __('Lama penanganan bergantung pada jenis kasus dan prioritas yang ditetapkan Customer Service. Anda dapat memantau perkembangannya kapan saja melalui halaman "Pengaduan Saya".')],
                            [__('Apakah bank meminta PIN atau OTP lewat SiPeNa?'), __('Tidak pernah. Petugas tidak akan pernah meminta PIN, OTP, kata sandi, atau data rahasia lainnya melalui sistem ini. Jangan bagikan data tersebut kepada siapa pun.')],
                            [__('Bagaimana cara memantau status pengaduan?'), __('Masuk ke akun Anda, buka "Pengaduan Saya", lalu pilih tiket untuk melihat status terkini beserta linimasa riwayat tindak lanjutnya.')],
                            [__('Apakah SiPeNa menggantikan call center atau kunjungan ke kantor?'), __('Tidak. SiPeNa adalah kanal pendukung yang melengkapi jalur pengaduan yang sudah ada dan menyesuaikan prosedur yang berlaku di bank.')],
                        ] as $i => [$q, $a])
                            <details class="group bg-white" @if ($i === 0) open @endif>
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 marker:content-none">
                                    <span class="text-base font-semibold text-ink">{{ $q }}</span>
                                    <svg class="h-5 w-5 shrink-0 text-muted transition-transform duration-200 group-open:rotate-180" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </summary>
                                <p class="px-5 pb-5 text-sm leading-relaxed text-muted">{{ $a }}</p>
                            </details>
                        @endforeach
                    </div>
                </div>
            </section>
        </main>

        {{-- ─── Footer ──────────────────────────────────────────── --}}
        <footer class="bg-primary-800 text-white">
            <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
                <div class="grid gap-10 lg:grid-cols-3">
                    <div class="max-w-sm">
                        <x-sipena-logo variant="full" tone="light" />
                        <p class="mt-4 text-sm leading-relaxed text-white/65">
                            {{ __('Kanal pendukung pengaduan nasabah untuk layanan Bank Sumsel Babel Cabang Indralaya.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-white/50">{{ __('Kontak Cabang') }}</p>
                        <ul class="mt-4 space-y-2 text-sm text-white/75">
                            <li>{{ __('Jl. Lintas Timur No. 00, Indralaya') }}</li>
                            <li>{{ __('Ogan Ilir, Sumatera Selatan') }}</li>
                            <li>(0711) 000-0000</li>
                            <li>cs@sipena.test</li>
                        </ul>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-white/50">{{ __('Tautan Cepat') }}</p>
                        <ul class="mt-4 space-y-2 text-sm text-white/75">
                            <li><a href="#layanan" class="transition hover:text-white">{{ __('Layanan') }}</a></li>
                            <li><a href="#alur" class="transition hover:text-white">{{ __('Alur Pengaduan') }}</a></li>
                            <li><a href="#faq" class="transition hover:text-white">{{ __('FAQ') }}</a></li>
                            <li><a href="{{ route('login') }}" class="transition hover:text-white">{{ __('Masuk') }}</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-10 border-t border-white/10 pt-6 text-xs text-white/50">
                    {{ __('Prototipe akademik — Capstone Project Sistem Informasi, Universitas Terbuka 2026.') }}
                </div>
            </div>
        </footer>

        <script>
            // Tambahkan border + bayangan halus pada navbar saat halaman di-scroll.
            (function () {
                const header = document.getElementById('site-header');
                if (!header) return;
                const onScroll = () => {
                    const active = window.scrollY > 8;
                    header.classList.toggle('border-line', active);
                    header.classList.toggle('border-transparent', !active);
                    header.classList.toggle('shadow-[0_1px_2px_rgba(11,31,58,0.04)]', active);
                    header.classList.toggle('bg-white/90', active);
                    header.classList.toggle('backdrop-blur', active);
                };
                onScroll();
                window.addEventListener('scroll', onScroll, { passive: true });
            })();
        </script>
    </body>
</html>
