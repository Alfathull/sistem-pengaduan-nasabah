@props([
    'label' => '',
    'value' => '',
    'accent' => 'ink', // ink | success | warning | danger
])

@php
    $valueColor = match ($accent) {
        'success' => 'text-success',
        'warning' => 'text-warning',
        'danger' => 'text-danger',
        default => 'text-ink dark:text-white',
    };
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-line bg-white p-5 shadow-card dark:border-zinc-700 dark:bg-zinc-900']) }}>
    <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $label }}</p>
    <p class="mt-2 text-3xl font-extrabold tabular-nums {{ $valueColor }}">{{ $value }}</p>
    @if (isset($slot) && trim($slot) !== '')
        <div class="mt-1 text-sm text-muted">{{ $slot }}</div>
    @endif
</div>
