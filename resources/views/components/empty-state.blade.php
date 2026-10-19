@props([
    'title' => '',
    'description' => null,
    'icon' => 'inbox', // inbox | search | chart
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-line bg-surface/50 px-6 py-12 text-center dark:border-zinc-700 dark:bg-transparent']) }}>
    <svg class="mx-auto h-16 w-16 text-primary-200 dark:text-zinc-600" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        @switch($icon)
            @case('search')
                <circle cx="28" cy="28" r="14" stroke-linecap="round"/>
                <path d="m39 39 11 11" stroke-linecap="round"/>
                @break
            @case('chart')
                <path d="M12 52h40" stroke-linecap="round"/>
                <rect x="18" y="34" width="8" height="14" rx="2"/>
                <rect x="30" y="24" width="8" height="24" rx="2"/>
                <rect x="42" y="30" width="8" height="18" rx="2"/>
                @break
            @default
                <path d="M12 20h40v24a4 4 0 0 1-4 4H16a4 4 0 0 1-4-4V20Z" stroke-linejoin="round"/>
                <path d="M12 20 32 10l20 10" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 32h12l3 5h10l3-5h12" stroke-linecap="round" stroke-linejoin="round"/>
        @endswitch
    </svg>

    <p class="mt-4 text-base font-semibold text-ink dark:text-white">{{ $title }}</p>
    @if ($description)
        <p class="mx-auto mt-1 max-w-sm text-sm text-muted">{{ $description }}</p>
    @endif

    @if (isset($action))
        <div class="mt-5 flex justify-center">{{ $action }}</div>
    @endif
</div>
