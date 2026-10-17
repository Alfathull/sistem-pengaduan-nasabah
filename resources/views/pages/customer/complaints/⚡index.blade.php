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

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Complaint::ownedBy(Auth::user())
            ->with('category')
            ->latest();

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        return [
            'complaints' => $query->paginate(10),
            'statusOptions' => ComplaintStatus::options(),
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

    <div class="mt-6 max-w-xs">
        <flux:select wire:model.live="statusFilter" :label="__('Filter Status')">
            <flux:select.option value="">{{ __('Semua status') }}</flux:select.option>
            @foreach ($statusOptions as $value => $label)
                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="mt-6">
        @if ($complaints->isEmpty())
            <div class="rounded-xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-600">
                <flux:icon.inbox class="mx-auto size-10 text-zinc-400" />
                <flux:text class="mt-2">{{ __('Belum ada pengaduan.') }}</flux:text>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($complaints as $complaint)
                    <a href="{{ route('customer.complaints.show', $complaint) }}" wire:navigate wire:key="complaint-{{ $complaint->id }}"
                        class="block rounded-xl border border-zinc-200 p-4 transition hover:border-blue-400 hover:shadow-sm dark:border-zinc-700">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <flux:badge size="sm" color="zinc" inset="top bottom">{{ $complaint->ticket_number }}</flux:badge>
                                    <x-status-badge :status="$complaint->status" />
                                </div>
                                <flux:heading size="sm" class="mt-2 truncate">{{ $complaint->subject }}</flux:heading>
                                <flux:text class="mt-1 text-sm">{{ $complaint->category->name }} &middot; {{ $complaint->created_at->translatedFormat('d M Y H:i') }}</flux:text>
                            </div>
                            <flux:icon.chevron-right class="hidden size-5 text-zinc-400 sm:block" />
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $complaints->links() }}
            </div>
        @endif
    </div>
</div>
