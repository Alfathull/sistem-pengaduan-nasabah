<?php

use App\Models\Category;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::staff')] #[Title('Kategori Layanan')] class extends Component {
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public bool $is_active = true;

    public function create(): void
    {
        $this->reset(['editingId', 'name']);
        $this->is_active = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(Category $category): void
    {
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->is_active = $category->is_active;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ], [], ['name' => 'nama kategori']);

        Category::updateOrCreate(['id' => $this->editingId], $validated);

        $this->showModal = false;
        Flux::toast(variant: 'success', text: __('Kategori disimpan.'));
    }

    public function toggle(Category $category): void
    {
        $category->update(['is_active' => ! $category->is_active]);
        Flux::toast(variant: 'success', text: __('Status kategori diperbarui.'));
    }

    public function with(): array
    {
        return [
            'categories' => Category::withCount('complaints')->orderBy('name')->get(),
        ];
    }
}; ?>

<div>
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Kategori Layanan') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Kelola master kategori pengaduan.') }}</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">{{ __('Tambah') }}</flux:button>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-card dark:border-zinc-700 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line bg-surface text-left text-xs uppercase tracking-wide text-muted dark:border-zinc-700 dark:bg-zinc-800">
                        <th class="px-5 py-3.5 font-semibold">{{ __('Nama') }}</th>
                        <th class="px-5 py-3.5 text-right font-semibold">{{ __('Jumlah Tiket') }}</th>
                        <th class="px-5 py-3.5 font-semibold">{{ __('Status') }}</th>
                        <th class="px-5 py-3.5 text-right font-semibold"><span class="sr-only">{{ __('Aksi') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr wire:key="cat-{{ $category->id }}" class="border-b border-line transition last:border-0 hover:bg-primary-50 dark:border-zinc-800 dark:hover:bg-zinc-800">
                            <td class="px-5 py-3.5 font-semibold text-ink dark:text-white">{{ $category->name }}</td>
                            <td class="px-5 py-3.5 text-right text-muted tabular-nums">{{ $category->complaints_count }}</td>
                            <td class="px-5 py-3.5">
                                @if ($category->is_active)
                                    <flux:badge color="green" size="sm" inset="top bottom">{{ __('Aktif') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" inset="top bottom">{{ __('Nonaktif') }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex justify-end gap-1">
                                    <flux:button size="xs" variant="ghost" icon="pencil-square" wire:click="edit({{ $category->id }})">{{ __('Ubah') }}</flux:button>
                                    <flux:button size="xs" variant="ghost" wire:click="toggle({{ $category->id }})">
                                        {{ $category->is_active ? __('Nonaktifkan') : __('Aktifkan') }}
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <flux:modal wire:model.self="showModal" class="max-w-md">
        <form wire:submit="save" class="space-y-5">
            <flux:heading size="lg">{{ $editingId ? __('Ubah Kategori') : __('Tambah Kategori') }}</flux:heading>
            <flux:input wire:model="name" :label="__('Nama Kategori')" />
            <flux:checkbox wire:model="is_active" :label="__('Aktif')" />
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Simpan') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
