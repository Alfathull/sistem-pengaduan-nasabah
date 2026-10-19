@props([
    'variant' => 'full', // full | icon
    'tone' => 'dark',     // dark (navy, for light bg) | light (white, for navy bg)
])

@php
    // Resolve colors by tone.
    $inkColor = $tone === 'light' ? '#FFFFFF' : '#0B1F3A';
    $goldColor = $tone === 'light' ? '#DCB45C' : '#C8962E';
    $bubbleFill = $tone === 'light' ? 'rgba(255,255,255,0.14)' : '#EEF3FA';
    $wordInk = $tone === 'light' ? 'text-white' : 'text-ink';
    $wordMuted = $tone === 'light' ? 'text-white/70' : 'text-muted';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }} role="img" aria-label="SiPeNa">
    {{-- Icon: shield containing a speech bubble with a check --}}
    <svg class="h-8 w-8 shrink-0" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        {{-- Shield outline (security / trust) --}}
        <path d="M24 3.5 7 9.2v12.5c0 10.6 6.9 18.4 17 22.8 10.1-4.4 17-12.2 17-22.8V9.2L24 3.5Z"
            fill="{{ $inkColor }}" />
        {{-- Inner shield cut for depth --}}
        <path d="M24 8 11 12.4v9.3c0 8.4 5.3 14.8 13 18.5 7.7-3.7 13-10.1 13-18.5v-9.3L24 8Z"
            fill="{{ $bubbleFill }}" />
        {{-- Speech bubble (complaint) --}}
        <path d="M16 16.5h16c1.1 0 2 .9 2 2v8.5c0 1.1-.9 2-2 2H22.6l-4.3 3.6c-.7.6-1.8.1-1.8-.8v-2.8H16c-1.1 0-2-.9-2-2V18.5c0-1.1.9-2 2-2Z"
            fill="{{ $tone === 'light' ? '#FFFFFF' : '#FFFFFF' }}" />
        {{-- Check mark (resolved) in gold --}}
        <path d="m19.5 22.8 3 3 5.4-5.6" stroke="{{ $goldColor }}" stroke-width="2.4"
            stroke-linecap="round" stroke-linejoin="round" />
    </svg>

    @if ($variant === 'full')
        <span class="flex flex-col leading-none">
            <span class="text-lg font-extrabold tracking-tight {{ $wordInk }}">
                Si<span class="text-gold-500">Pe</span>Na
            </span>
            <span class="mt-0.5 text-[10px] font-medium tracking-wide {{ $wordMuted }}">
                Pengaduan Nasabah
            </span>
        </span>
    @endif
</span>
