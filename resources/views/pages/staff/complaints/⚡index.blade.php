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
            'categoryOptions' => Category::orderBy('name')->pluck('name', 'id')->all(),
        ];
    }
}; ?>

<div wire:poll.30s>
    <flux:heading size="xl">{{ __('Tiket Pengaduan') }}</flux:heading>
    <flux:text class="mt-1">{{ __('Kelola, validasi, dan distribusikan tiket pengaduan nasabah.') }}</flux:text>

    <div class="mt-6 rounded-2xl border border-line bg-white p-4 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <flux:input wire:model.live.debounce.400ms="search" :label="__('Cari')" icon="magnifying-glass" :placeholder="__('No. tiket / nama / subjek')" />
            <x-ui.select wire:model.live="statusFilter" :label="__('Status')" :options="['' => __('Semua status')] + $statusOptions" />
            <x-ui.select wire:model.live="categoryFilter" :label="__('Kategori')" :options="['' => __('Semua kategori')] + $categoryOptions" />
            <flux:input type="date" wire:model.live="dateFrom" :label="__('Dari Tanggal')" />
            <flux:input type="date" wire:model.live="dateTo" :label="__('Sampai Tanggal')" />
            <div class="flex items-end">
                <flux:button variant="ghost" icon="x-mark" wire:click="resetFilters">{{ __('Reset') }}</flux:button>
            </div>
        </div>
    </div>

    @if ($complaints->isEmpty())
        <div class="mt-6">
            <x-empty-state icon="search" :title="__('Tidak ada tiket yang cocok')" :description="__('Coba ubah atau atur ulang filter pencarian Anda.')" />
        </div>
    @else
        {{-- Desktop: tabel --}}
        <div class="mt-6 hidden overflow-hidden rounded-2xl border border-line bg-white shadow-card md:block dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-line bg-surface text-left text-xs uppercase tracking-wide text-muted dark:border-zinc-700 dark:bg-zinc-800">
                            <th class="px-5 py-3.5 font-semibold">{{ __('No. Tiket') }}</th>
                            <th class="px-5 py-3.5 font-semibold">{{ __('Pengadu') }}</th>
                            <th class="px-5 py-3.5 font-semibold">{{ __('Subjek') }}</th>
                            <th class="px-5 py-3.5 font-semibold">{{ __('Kategori') }}</th>
                            <th class="px-5 py-3.5 text-right font-semibold">{{ __('Tanggal') }}</th>
                            <th class="px-5 py-3.5 font-semibold">{{ __('Status') }}</th>
                            <th class="px-5 py-3.5 text-right font-semibold"><span class="sr-only">{{ __('Aksi') }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complaints as $complaint)
                            <tr
                                wire:key="row-{{ $complaint->id }}"
                                onclick="window.Livewire.navigate('{{ route('staff.complaints.show', $complaint) }}')"
                                class="cursor-pointer border-b border-line transition last:border-0 hover:bg-primary-50 dark:border-zinc-800 dark:hover:bg-zinc-800"
                            >
                                <td class="px-5 py-3.5 font-mono font-semibold text-ink tabular-nums dark:text-white">{{ $complaint->ticket_number }}</td>
                                <td class="px-5 py-3.5 text-ink dark:text-zinc-100">{{ $complaint->reporter_name }}</td>
                                <td class="max-w-xs truncate px-5 py-3.5 text-ink dark:text-zinc-100">{{ $complaint->subject }}</td>
                                <td class="px-5 py-3.5 text-muted">{{ $complaint->category->name }}</td>
                                <td class="px-5 py-3.5 text-right text-muted tabular-nums">{{ $complaint->created_at->translatedFormat('d M Y') }}</td>
                                <td class="px-5 py-3.5"><x-status-badge :status="$complaint->status" /></td>
                                <td class="px-5 py-3.5 text-right" onclick="event.stopPropagation()">
                                    <flux:button size="xs" variant="ghost" icon="eye" :href="route('staff.complaints.show', $complaint)" wire:navigate>{{ __('Detail') }}</flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile: daftar kartu --}}
        <div class="mt-6 space-y-3 md:hidden">
            @foreach ($complaints as $complaint)
                <a href="{{ route('staff.complaints.show', $complaint) }}" wire:navigate wire:key="card-{{ $complaint->id }}"
                    class="block rounded-2xl border border-line bg-white p-4 transition hover:border-primary-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono text-xs font-semibold text-muted tabular-nums">{{ $complaint->ticket_number }}</span>
                        <x-status-badge :status="$complaint->status" />
                    </div>
                    <p class="mt-1.5 truncate font-semibold text-ink dark:text-white">{{ $complaint->subject }}</p>
                    <p class="mt-0.5 text-sm text-muted">{{ $complaint->reporter_name }} &middot; {{ $complaint->category->name }}</p>
                    <p class="mt-0.5 text-xs text-muted tabular-nums">{{ $complaint->created_at->translatedFormat('d M Y') }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-5">
            {{ $complaints->links('vendor.pagination.sipena', ['unit' => __('tiket')]) }}
        </div>
    @endif
</div>
