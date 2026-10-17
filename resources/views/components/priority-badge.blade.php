@props(['priority' => null])

@if ($priority)
    <flux:badge :color="$priority->color()" size="sm" inset="top bottom">
        {{ $priority->label() }}
    </flux:badge>
@else
    <flux:text class="text-sm">-</flux:text>
@endif
