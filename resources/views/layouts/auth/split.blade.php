<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased">
        <div class="relative grid min-h-dvh lg:grid-cols-5">
            <!-- Panel kiri: navy, branding + value proposition + mockup -->
            <div class="relative hidden flex-col justify-between overflow-hidden bg-primary-800 p-10 text-white lg:col-span-2 lg:flex">
                <div class="bg-dotgrid pointer-events-none absolute inset-0 opacity-[0.07]"></div>

                <a href="{{ route('home') }}" class="relative z-10 flex items-center" wire:navigate>
                    <x-sipena-logo variant="full" tone="light" />
                </a>

                <div class="relative z-10 max-w-sm">
                    <flux:heading size="xl" class="!text-white">
                        {{ __('Sampaikan keluhan, pantau penyelesaiannya.') }}
                    </flux:heading>
                    <p class="mt-3 text-sm leading-relaxed text-white/70">
                        {{ __('Kanal pendukung pengaduan nasabah Bank Sumsel Babel Cabang Indralaya. Transparan dan tercatat.') }}
                    </p>

                    <!-- Mockup kartu tiket kecil -->
                    <div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs text-white/80 tabular-nums">PGD-20261017-0012</span>
                            <span class="rounded-full bg-warning/20 px-2.5 py-0.5 text-[11px] font-semibold text-gold-400">
                                {{ __('Diproses') }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm font-semibold text-white">{{ __('Mobile Banking') }}</p>
                        <div class="mt-4 flex items-center gap-2 text-[11px] text-white/60">
                            <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>{{ __('Baru') }}
                            <span class="h-px flex-1 bg-white/15"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>{{ __('Divalidasi') }}
                            <span class="h-px flex-1 bg-white/15"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>{{ __('Diproses') }}
                        </div>
                    </div>
                </div>

                <p class="relative z-10 text-xs text-white/50">
                    {{ __('Prototipe akademik — Capstone Project Sistem Informasi, Universitas Terbuka 2026.') }}
                </p>
            </div>

            <!-- Panel kanan: form -->
            <div class="flex items-center justify-center bg-white px-6 py-10 lg:col-span-3 lg:px-8 dark:bg-zinc-900">
                <div class="mx-auto flex w-full max-w-sm flex-col gap-6">
                    <a href="{{ route('home') }}" class="flex justify-center lg:hidden" wire:navigate>
                        <x-sipena-logo variant="full" tone="dark" class="dark:[&_.text-ink]:text-white" />
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
