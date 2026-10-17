@props(['status'])

<flux:badge :color="$status->color()" size="sm" inset="top bottom">
    {{ $status->label() }}
</flux:badge>
