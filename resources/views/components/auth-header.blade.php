@props([
    'title',
    'description',
])

<div class="flex w-full flex-col">
    <flux:heading size="xl" level="1">{{ $title }}</flux:heading>
    <flux:subheading class="mt-1">{{ $description }}</flux:subheading>
</div>
