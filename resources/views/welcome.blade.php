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
    <body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
                <div class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-900 font-bold text-white">Si</span>
                    <span class="text-lg font-bold text-blue-900">SiPeNa</span>
                </div>
                <nav class="flex items-center gap-2 sm:gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-blue-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800">
                            {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-blue-900 transition hover:bg-blue-50">
                            {{ __('Masuk') }}
                        </a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-blue-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-800">
                            {{ __('Daftar') }}
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <!-- Hero -->
            <section class="bg-gradient-to-b from-blue-900 to-blue-800 text-white">
                <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
                    <div class="max-w-2xl">
                        <span class="inline-block rounded-full bg-white/10 px-3 py-1 text-xs font-medium">
                            Bank Sumsel Babel Cabang Indralaya
                        </span>
                        <h1 class="mt-5 text-3xl font-bold leading-tight sm:text-4xl md:text-5xl">
                            Sistem Informasi Pengaduan Nasabah
                        </h1>
                        <p class="mt-4 text-base text-blue-100 sm:text-lg">
                            Kirim, lacak, dan selesaikan pengaduan layanan perbankan secara daring.
                            Transparan, terintegrasi, dan diperbarui secara real-time.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('register') }}" class="rounded-lg bg-white px-6 py-3 text-center text-sm font-semibold text-blue-900 transition hover:bg-blue-50">
                                Buat Pengaduan Sekarang
                            </a>
                            <a href="{{ route('login') }}" class="rounded-lg border border-white/40 px-6 py-3 text-center text-sm font-semibold text-white transition hover:bg-white/10">
                                Masuk ke Akun
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Fitur -->
            <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
                <h2 class="text-center text-2xl font-bold text-slate-900">Layanan SiPeNa</h2>
                <p class="mx-auto mt-2 max-w-2xl text-center text-sm text-slate-500">
                    Menggantikan proses pengaduan konvensional (call center, datang langsung, email terpisah)
                    dengan satu kanal daring yang mudah dipantau.
                </p>

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-900">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-slate-900">Kirim Pengaduan</h3>
                        <p class="mt-1 text-sm text-slate-500">Isi formulir pengaduan dan dapatkan nomor tiket unik secara otomatis.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-900">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75 12 12V6" /><circle cx="12" cy="12" r="9" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-slate-900">Lacak Status</h3>
                        <p class="mt-1 text-sm text-slate-500">Pantau progres pengaduan Anda melalui linimasa status yang diperbarui real-time.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-900">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-slate-900">Penyelesaian Transparan</h3>
                        <p class="mt-1 text-sm text-slate-500">Setiap tindak lanjut dan perubahan status tercatat lengkap dalam riwayat.</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-6xl px-4 py-6 text-center text-sm text-slate-500 sm:px-6">
                &copy; {{ date('Y') }} SiPeNa — Prototipe Capstone Project.
            </div>
        </footer>
    </body>
</html>
