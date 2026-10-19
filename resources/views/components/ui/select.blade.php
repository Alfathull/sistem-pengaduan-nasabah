@props([
    'name' => null,
    'label' => null,
    'placeholder' => 'Pilih...',
    'options' => [],          // array value => label
    'searchable' => null,     // null = auto (true bila opsi > 8)
    'disabled' => false,
    'error' => null,          // override pesan error; default ambil dari $errors by name
])

@php
    $resolvedSearchable = $searchable ?? (count($options) > 8);
    $fieldError = $error ?? ($name ? $errors->first($name) : null);

    // Deteksi binding Livewire (wire:model / wire:model.live).
    $wireTarget = $attributes->wire('model')->value();

    $uid = 'uisel_'.\Illuminate\Support\Str::random(10);
    $jsonOptions = json_encode(
        collect($options)->map(fn ($label, $value) => ['value' => (string) $value, 'label' => (string) $label])->values(),
        JSON_UNESCAPED_UNICODE
    );
@endphp

<div
    x-data="uiSelect({
        options: {{ $jsonOptions }},
        searchable: {{ $resolvedSearchable ? 'true' : 'false' }},
        placeholder: @js($placeholder),
        disabled: {{ $disabled ? 'true' : 'false' }},
        listId: @js($uid.'-list'),
        @if ($wireTarget)
            get model() { return $wire.{{ $wireTarget }} },
            set model(v) { $wire.{{ $wireTarget }} = v },
        @else
            initial: @js(old($name)),
        @endif
    })"
    class="w-full"
    @class(['opacity-60 pointer-events-none' => $disabled])
>
    @if ($label)
        <label for="{{ $uid }}" class="mb-1.5 block text-sm font-medium leading-tight text-ink dark:text-white">{{ $label }}</label>
    @endif

    {{-- Hidden input untuk form non-Livewire --}}
    @if ($name && ! $wireTarget)
        <input type="hidden" name="{{ $name }}" x-bind:value="value">
    @endif

    <div class="relative">
        {{-- Trigger: tinggi & radius selaras dengan input Flux --}}
        <button
            type="button"
            id="{{ $uid }}"
            x-ref="trigger"
            @click="toggle()"
            @keydown="onTriggerKeydown($event)"
            :aria-expanded="open.toString()"
            aria-haspopup="listbox"
            role="combobox"
            aria-controls="{{ $uid }}-list"
            :aria-activedescendant="activeId()"
            @class([
                'flex h-10 w-full items-center justify-between gap-2 rounded-[10px] border bg-white px-3 text-left text-sm text-ink transition dark:bg-zinc-800 dark:text-white',
                'border-line hover:border-primary-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2' => ! $fieldError,
                'border-danger focus-visible:ring-2 focus-visible:ring-danger' => (bool) $fieldError,
                'dark:border-zinc-600' => true,
            ])
        >
            <span x-text="selectedLabel()" :class="value === '' && 'text-muted'" class="truncate"></span>
            <svg class="h-4 w-4 shrink-0 text-muted transition-transform" :class="open && 'rotate-180'" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        {{-- Panel opsi --}}
        <div
            x-show="open"
            x-cloak
            x-transition.origin.top
            @click.outside="close()"
            class="absolute z-50 mt-1 w-full overflow-hidden rounded-[10px] border border-line bg-white shadow-card dark:border-zinc-600 dark:bg-zinc-800"
        >
            <template x-if="searchable">
                <div class="border-b border-line p-2 dark:border-zinc-600">
                    <input
                        type="text"
                        x-ref="search"
                        x-model="query"
                        @keydown="onSearchKeydown($event)"
                        placeholder="{{ __('Cari...') }}"
                        class="h-8 w-full rounded-md border border-line bg-surface px-2 text-sm text-ink focus:outline-none dark:border-zinc-600 dark:bg-zinc-900 dark:text-white"
                    >
                </div>
            </template>

            <ul
                id="{{ $uid }}-list"
                x-ref="list"
                role="listbox"
                class="max-h-60 overflow-y-auto py-1"
                tabindex="-1"
            >
                <template x-for="(opt, index) in filtered()" :key="opt.value">
                    <li
                        :id="'{{ $uid }}-list-opt-' + index"
                        role="option"
                        :aria-selected="(opt.value === value).toString()"
                        @click="choose(opt.value)"
                        @mousemove="activeIndex = index"
                        :class="index === activeIndex ? 'bg-primary-50 text-ink dark:bg-zinc-700 dark:text-white' : 'text-ink dark:text-zinc-100'"
                        class="flex cursor-pointer items-center justify-between gap-2 px-3 py-2 text-sm"
                    >
                        <span x-text="opt.label" class="truncate"></span>
                        <svg x-show="opt.value === value" class="h-4 w-4 shrink-0 text-primary-600 dark:text-primary-300" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="m5 10 3.2 3.2L15 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </li>
                </template>

                <li x-show="filtered().length === 0" class="px-3 py-2 text-sm text-muted">{{ __('Tidak ada opsi.') }}</li>
            </ul>
        </div>
    </div>

    @if ($fieldError)
        <p class="mt-1.5 text-sm text-danger">{{ $fieldError }}</p>
    @endif
</div>
