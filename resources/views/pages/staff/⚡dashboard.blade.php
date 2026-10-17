<?php

use App\Enums\ComplaintStatus;
use App\Models\Complaint;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::staff')] #[Title('Dashboard CS')] class extends Component {
    public function with(): array
    {
        $counts = Complaint::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byStatus = [];
        foreach (ComplaintStatus::cases() as $status) {
            $byStatus[$status->value] = (int) ($counts[$status->value] ?? 0);
        }

        return [
            'total' => Complaint::count(),
            'byStatus' => $byStatus,
            'needsAction' => Complaint::whereIn('status', [
                ComplaintStatus::Baru->value,
                ComplaintStatus::Divalidasi->value,
            ])->count(),
            'latest' => Complaint::with('category')->latest()->take(5)->get(),
            'statuses' => ComplaintStatus::cases(),
        ];
    }
}; ?>

<div wire:poll.30s>
    <flux:heading size="xl">{{ __('Dashboard Customer Service') }}</flux:heading>
    <flux:text class="mt-1">{{ __('Ringkasan tiket pengaduan nasabah.') }}</flux:text>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:text class="text-sm text-zinc-500">{{ __('Total Pengaduan') }}</flux:text>
            <flux:heading size="xl" class="mt-1">{{ $total }}</flux:heading>
        </div>
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:text class="text-sm text-zinc-500">{{ __('Perlu Tindakan') }}</flux:text>
            <flux:heading size="xl" class="mt-1 text-amber-600">{{ $needsAction }}</flux:heading>
        </div>
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:text class="text-sm text-zinc-500">{{ __('Selesai') }}</flux:text>
            <flux:heading size="xl" class="mt-1 text-green-600">{{ $byStatus[\App\Enums\ComplaintStatus::Selesai->value] }}</flux:heading>
        </div>
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:text class="text-sm text-zinc-500">{{ __('Ditolak') }}</flux:text>
            <flux:heading size="xl" class="mt-1 text-red-600">{{ $byStatus[\App\Enums\ComplaintStatus::Ditolak->value] }}</flux:heading>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="sm">{{ __('Jumlah per Status') }}</flux:heading>
            <flux:separator class="my-3" />
            <div class="space-y-2">
                @foreach ($statuses as $status)
                    <div class="flex items-center justify-between">
                        <x-status-badge :status="$status" />
                        <flux:text class="font-semibold">{{ $byStatus[$status->value] }}</flux:text>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <flux:heading size="sm">{{ __('Pengaduan Terbaru') }}</flux:heading>
                <flux:link class="text-sm" :href="route('staff.complaints.index')" wire:navigate>{{ __('Lihat semua') }}</flux:link>
            </div>
            <flux:separator class="my-3" />
            <div class="space-y-3">
                @forelse ($latest as $complaint)
                    <a href="{{ route('staff.complaints.show', $complaint) }}" wire:navigate wire:key="latest-{{ $complaint->id }}" class="block">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <flux:text class="truncate font-medium">{{ $complaint->subject }}</flux:text>
                                <flux:text class="text-xs text-zinc-500">{{ $complaint->ticket_number }}</flux:text>
                            </div>
                            <x-status-badge :status="$complaint->status" />
                        </div>
                    </a>
                @empty
                    <flux:text class="text-sm text-zinc-500">{{ __('Belum ada pengaduan.') }}</flux:text>
                @endforelse
            </div>
        </div>
    </div>
</div>
