<?php

use App\Models\Complaint;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::customer')] #[Title('Detail Pengaduan')] class extends Component {
    public Complaint $complaint;

    public function mount(Complaint $complaint): void
    {
        $this->authorize('view', $complaint);
        $this->complaint = $complaint;
    }

    public function with(): array
    {
        $this->complaint->load(['category', 'department', 'histories.user', 'histories.department']);

        return [];
    }
}; ?>

<div wire:poll.30s>
    <div class="mb-4">
        <flux:button size="sm" variant="ghost" icon="arrow-left" :href="route('customer.complaints.index')" wire:navigate>
            {{ __('Kembali') }}
        </flux:button>
    </div>

    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <flux:heading size="xl">{{ $complaint->ticket_number }}</flux:heading>
                <x-status-badge :status="$complaint->status" />
            </div>
            <flux:text class="mt-1">{{ __('Dibuat :date', ['date' => $complaint->created_at->translatedFormat('d F Y H:i')]) }}</flux:text>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <!-- Detail pengaduan -->
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <flux:heading size="sm">{{ __('Data Pengaduan') }}</flux:heading>
                <flux:separator class="my-3" />
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <flux:text class="text-xs text-zinc-500">{{ __('Kategori') }}</flux:text>
                        <flux:text class="font-medium">{{ $complaint->category->name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-500">{{ __('Jenis Rekening') }}</flux:text>
                        <flux:text class="font-medium">{{ $complaint->account_type->label() }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-500">{{ __('Prioritas') }}</flux:text>
                        <div class="mt-0.5"><x-priority-badge :priority="$complaint->priority" /></div>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-500">{{ __('Departemen Terkait') }}</flux:text>
                        <flux:text class="font-medium">{{ $complaint->department?->name ?? '-' }}</flux:text>
                    </div>
                    <div class="sm:col-span-2">
                        <flux:text class="text-xs text-zinc-500">{{ __('Subjek') }}</flux:text>
                        <flux:text class="font-medium">{{ $complaint->subject }}</flux:text>
                    </div>
                    <div class="sm:col-span-2">
                        <flux:text class="text-xs text-zinc-500">{{ __('Deskripsi') }}</flux:text>
                        <flux:text class="whitespace-pre-line">{{ $complaint->description }}</flux:text>
                    </div>
                    @if ($complaint->rejection_reason)
                        <div class="sm:col-span-2">
                            <flux:callout variant="danger" icon="x-circle" :heading="__('Pengaduan Ditolak')">
                                {{ $complaint->rejection_reason }}
                            </flux:callout>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Identitas pengadu -->
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <flux:heading size="sm">{{ __('Identitas Pengadu') }}</flux:heading>
                <flux:separator class="my-3" />
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <flux:text class="text-xs text-zinc-500">{{ __('Nama') }}</flux:text>
                        <flux:text class="font-medium">{{ $complaint->reporter_name }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-500">{{ __('Email') }}</flux:text>
                        <flux:text class="font-medium">{{ $complaint->reporter_email }}</flux:text>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-500">{{ __('No. HP') }}</flux:text>
                        <flux:text class="font-medium">{{ $complaint->reporter_phone ?? '-' }}</flux:text>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Timeline riwayat -->
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="sm">{{ __('Riwayat & Tindak Lanjut') }}</flux:heading>
            <flux:separator class="my-3" />

            <ol class="relative space-y-5 border-s border-zinc-200 ps-5 dark:border-zinc-700">
                @foreach ($complaint->histories as $history)
                    <li wire:key="history-{{ $history->id }}" class="relative">
                        <span class="absolute -start-[1.4rem] mt-1 flex h-3 w-3 rounded-full bg-blue-600 ring-4 ring-white dark:ring-zinc-800"></span>
                        <div class="flex items-center gap-2">
                            <x-status-badge :status="$history->status" />
                            <flux:text class="text-xs text-zinc-500">{{ $history->created_at->translatedFormat('d M Y H:i') }}</flux:text>
                        </div>
                        @if ($history->follow_up)
                            <flux:text class="mt-1 text-sm">{{ $history->follow_up }}</flux:text>
                        @endif
                        @if ($history->department)
                            <flux:text class="mt-1 text-xs text-zinc-500">{{ __('Departemen: :dept', ['dept' => $history->department->name]) }}</flux:text>
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</div>
