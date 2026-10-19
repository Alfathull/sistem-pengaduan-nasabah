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

    <div class="mt-6 overflow-x-auto rounded-2xl border border-line bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Nama') }}</flux:table.column>
                <flux:table.column>{{ __('Jumlah Tiket') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($categories as $category)
                    <flux:table.row wire:key="cat-{{ $category->id }}">
                        <flux:table.cell variant="strong">{{ $category->name }}</flux:table.cell>
                        <flux:table.cell>{{ $category->complaints_count }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($category->is_active)
                                <flux:badge color="green" size="sm" inset="top bottom">{{ __('Aktif') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" inset="top bottom">{{ __('Nonaktif') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex gap-1">
                                <flux:button size="xs" variant="ghost" icon="pencil-square" wire:click="edit({{ $category->id }})">{{ __('Ubah') }}</flux:button>
                                <flux:button size="xs" variant="ghost" wire:click="toggle({{ $category->id }})">
                                    {{ $category->is_active ? __('Nonaktifkan') : __('Aktifkan') }}
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
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
