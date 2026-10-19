<?php

use App\Enums\ComplaintStatus;
use App\Models\Category;
use App\Models\Complaint;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::staff')] #[Title('Tiket Pengaduan')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $categoryFilter = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function updated(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'categoryFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Complaint::query()
            ->with(['category', 'user'])
            ->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('ticket_number', 'like', "%{$this->search}%")
                    ->orWhere('reporter_name', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter !== '') {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->dateFrom !== '') {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo !== '') {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return [
            'complaints' => $query->paginate(12),
            'statusOptions' => ComplaintStatus::options(),
            'categories' => Category::orderBy('name')->get(),
        ];
    }
}; ?>

<div wire:poll.30s>
    <flux:heading size="xl">{{ __('Tiket Pengaduan') }}</flux:heading>
    <flux:text class="mt-1">{{ __('Kelola, validasi, dan distribusikan tiket pengaduan nasabah.') }}</flux:text>

    <div class="mt-6 rounded-2xl border border-line bg-white p-4 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <flux:input wire:model.live.debounce.400ms="search" :label="__('Cari')" icon="magnifying-glass" :placeholder="__('No. tiket / nama / subjek')" />
            <flux:select wire:model.live="statusFilter" :label="__('Status')">
                <flux:select.option value="">{{ __('Semua status') }}</flux:select.option>
                @foreach ($statusOptions as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="categoryFilter" :label="__('Kategori')">
                <flux:select.option value="">{{ __('Semua kategori') }}</flux:select.option>
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input type="date" wire:model.live="dateFrom" :label="__('Dari Tanggal')" />
            <flux:input type="date" wire:model.live="dateTo" :label="__('Sampai Tanggal')" />
            <div class="flex items-end">
                <flux:button variant="ghost" icon="x-mark" wire:click="resetFilters">{{ __('Reset') }}</flux:button>
            </div>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-line bg-white dark:border-zinc-700 dark:bg-zinc-900">
        <flux:table :paginate="$complaints">
            <flux:table.columns>
                <flux:table.column>{{ __('No. Tiket') }}</flux:table.column>
                <flux:table.column>{{ __('Pengadu') }}</flux:table.column>
                <flux:table.column>{{ __('Subjek') }}</flux:table.column>
                <flux:table.column>{{ __('Kategori') }}</flux:table.column>
                <flux:table.column>{{ __('Tanggal') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($complaints as $complaint)
                    <flux:table.row wire:key="row-{{ $complaint->id }}">
                        <flux:table.cell variant="strong">{{ $complaint->ticket_number }}</flux:table.cell>
                        <flux:table.cell>{{ $complaint->reporter_name }}</flux:table.cell>
                        <flux:table.cell class="max-w-xs truncate">{{ $complaint->subject }}</flux:table.cell>
                        <flux:table.cell>{{ $complaint->category->name }}</flux:table.cell>
                        <flux:table.cell>{{ $complaint->created_at->translatedFormat('d M Y') }}</flux:table.cell>
                        <flux:table.cell><x-status-badge :status="$complaint->status" /></flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" icon="eye" :href="route('staff.complaints.show', $complaint)" wire:navigate>{{ __('Detail') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-zinc-500">{{ __('Tidak ada tiket yang cocok.') }}</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>
</div>
