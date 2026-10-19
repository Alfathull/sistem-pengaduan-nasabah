<?php

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::customer')] #[Title('Pengaduan Saya')] class extends Component {
    use WithPagination;

    public string $statusFilter = '';

    public function setFilter(string $filter): void
    {
        $this->statusFilter = $this->statusFilter === $filter ? '' : $filter;
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $base = Complaint::ownedBy(Auth::user());

        // Ringkasan jumlah per kelompok status milik nasabah.
        $counts = (clone $base)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $activeStatuses = [
            ComplaintStatus::Baru->value,
            ComplaintStatus::Divalidasi->value,
            ComplaintStatus::Diproses->value,
            ComplaintStatus::Dieskalasi->value,
        ];
        $summary = [
            'aktif' => collect($activeStatuses)->sum(fn ($s) => (int) ($counts[$s] ?? 0)),
            'selesai' => (int) ($counts[ComplaintStatus::Selesai->value] ?? 0),
            'ditolak' => (int) ($counts[ComplaintStatus::Ditolak->value] ?? 0),
        ];

        $query = (clone $base)->with('category')->latest('updated_at');

        if ($this->statusFilter === 'aktif') {
            $query->whereIn('status', $activeStatuses);
        } elseif ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        return [
            'complaints' => $query->paginate(10),
            'summary' => $summary,
        ];
    }
}; ?>

<div wire:poll.30s>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Pengaduan Saya') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Pantau status dan progres pengaduan Anda.') }}</flux:text>
        </div>
        <flux:button variant="primary" icon="plus-circle" :href="route('customer.complaints.create')" wire:navigate>
            {{ __('Buat Pengaduan') }}
        </flux:button>
    </div>

    {{-- Chip ringkasan sekaligus filter cepat --}}
    <div class="mt-6 flex flex-wrap gap-2">
        @php
            $chips = [
                ['' , __('Semua'), array_sum($summary), 'primary'],
                ['aktif', __('Aktif'), $summary['aktif'], 'primary'],
                [ComplaintStatus::Selesai->value, __('Selesai'), $summary['selesai'], 'success'],
                [ComplaintStatus::Ditolak->value, __('Ditolak'), $summary['ditolak'], 'danger'],
            ];
        @endphp
        @foreach ($chips as [$value, $label, $count, $tone])
            <button
                type="button"
                wire:click="setFilter('{{ $value }}')"
                @class([
                    'inline-flex items-center gap-2 rounded-full border px-3.5 py-1.5 text-sm font-medium transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent',
                    'border-primary-800 bg-primary-800 text-white' => $statusFilter === $value,
                    'border-line bg-white text-ink hover:border-primary-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100' => $statusFilter !== $value,
                ])
                aria-pressed="{{ $statusFilter === $value ? 'true' : 'false' }}"
            >
                {{ $label }}
                <span @class([
                    'rounded-full px-1.5 text-xs tabular-nums',
                    'bg-white/20 text-white' => $statusFilter === $value,
                    'bg-surface text-muted dark:bg-zinc-800' => $statusFilter !== $value,
                ])>{{ $count }}</span>
            </button>
        @endforeach
    </div>

    <div class="mt-6">
        @if ($complaints->isEmpty())
            <x-empty-state
                icon="inbox"
                :title="$statusFilter !== '' ? __('Tidak ada pengaduan pada filter ini') : __('Belum ada pengaduan')"
                :description="__('Pengaduan yang Anda kirim akan muncul di sini beserta status dan riwayatnya.')"
            >
                <x-slot name="action">
                    <flux:button variant="primary" icon="plus-circle" :href="route('customer.complaints.create')" wire:navigate>
                        {{ __('Buat Pengaduan') }}
                    </flux:button>
                </x-slot>
            </x-empty-state>
        @else
            <div class="space-y-3">
                @foreach ($complaints as $complaint)
                    <a href="{{ route('customer.complaints.show', $complaint) }}" wire:navigate wire:key="complaint-{{ $complaint->id }}"
                        class="block rounded-2xl border border-line bg-white p-4 transition hover:border-primary-300 hover:shadow-card focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs font-semibold text-muted tabular-nums">{{ $complaint->ticket_number }}</span>
                                    <x-status-badge :status="$complaint->status" />
                                </div>
                                <p class="mt-1.5 truncate font-semibold text-ink dark:text-white">{{ $complaint->subject }}</p>
                                <p class="mt-0.5 text-sm text-muted">{{ $complaint->category->name }} &middot; {{ $complaint->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                            <div class="hidden shrink-0 text-right sm:block">
                                <p class="text-xs text-muted">{{ __('Update terakhir') }}</p>
                                <p class="text-sm font-medium text-ink dark:text-zinc-100">{{ $complaint->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-5">
                {{ $complaints->links('vendor.pagination.sipena', ['unit' => __('pengaduan')]) }}
            </div>
        @endif
    </div>
</div>
