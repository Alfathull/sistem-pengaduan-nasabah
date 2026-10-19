@php
    // Label unit bisa dikirim via $paginator->links('vendor.pagination.sipena', ['unit' => 'pengaduan'])
    $unit = $unit ?? 'hasil';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Navigasi halaman') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        {{-- Ringkasan --}}
        <p class="text-sm text-muted">
            @if ($paginator->total() > 0)
                {{ __('Menampilkan') }}
                <span class="font-semibold text-ink tabular-nums dark:text-white">{{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }}</span>
                {{ __('dari') }}
                <span class="font-semibold text-ink tabular-nums dark:text-white">{{ $paginator->total() }}</span>
                {{ $unit }}
            @endif
        </p>

        {{-- Tombol halaman --}}
        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-[10px] border border-line text-muted opacity-50 dark:border-zinc-700">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12.5 5 7.5 10l5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="prev" aria-label="{{ __('Sebelumnya') }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-[10px] border border-line text-ink transition hover:bg-surface focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12.5 5 7.5 10l5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            @endif

            {{-- Nomor halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-hidden="true" class="inline-flex h-9 min-w-9 items-center justify-center px-2 text-sm text-muted">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-[10px] bg-primary-800 px-2 text-sm font-semibold text-white tabular-nums">{{ $page }}</span>
                        @else
                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                                class="inline-flex h-9 min-w-9 items-center justify-center rounded-[10px] border border-line px-2 text-sm text-ink tabular-nums transition hover:bg-surface focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled" rel="next" aria-label="{{ __('Berikutnya') }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-[10px] border border-line text-ink transition hover:bg-surface focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent dark:border-zinc-700 dark:text-zinc-100 dark:hover:bg-zinc-800">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            @else
                <span aria-disabled="true" class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-[10px] border border-line text-muted opacity-50 dark:border-zinc-700">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            @endif
        </div>
    </nav>
@elseif ($paginator->total() > 0)
    {{-- Satu halaman: tetap tampilkan ringkasan --}}
    <p class="text-sm text-muted">
        {{ __('Menampilkan') }}
        <span class="font-semibold text-ink tabular-nums dark:text-white">{{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }}</span>
        {{ __('dari') }}
        <span class="font-semibold text-ink tabular-nums dark:text-white">{{ $paginator->total() }}</span>
        {{ $unit }}
    </p>
@endif
