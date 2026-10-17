<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public function markAsRead(string $id): void
    {
        $notification = Auth::user()->unreadNotifications()->where('id', $id)->first();

        $notification?->markAsRead();

        if ($notification && isset($notification->data['complaint_id'])) {
            $this->redirectRoute('customer.complaints.show', $notification->data['complaint_id'], navigate: true);
        }
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function with(): array
    {
        $user = Auth::user();

        return [
            'notifications' => $user->unreadNotifications()->latest()->take(10)->get(),
            'unreadCount' => $user->unreadNotifications()->count(),
        ];
    }
}; ?>

<div wire:poll.30s>
    <flux:dropdown position="bottom" align="end">
        <flux:button variant="ghost" square class="relative">
            <flux:icon.bell variant="outline" class="size-5" />
            @if ($unreadCount > 0)
                <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold text-white">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </flux:button>

        <flux:menu class="w-80">
            <div class="flex items-center justify-between px-2 py-1">
                <flux:heading size="sm">{{ __('Notifikasi') }}</flux:heading>
                @if ($unreadCount > 0)
                    <flux:link class="cursor-pointer text-xs" wire:click="markAllAsRead">{{ __('Tandai semua dibaca') }}</flux:link>
                @endif
            </div>

            <flux:menu.separator />

            @forelse ($notifications as $notification)
                <flux:menu.item wire:key="notif-{{ $notification->id }}" wire:click="markAsRead('{{ $notification->id }}')" class="cursor-pointer">
                    <div class="flex flex-col items-start gap-0.5 py-1">
                        <span class="text-sm font-medium">{{ $notification->data['ticket_number'] ?? '' }}</span>
                        <span class="text-xs text-zinc-500">{{ $notification->data['message'] ?? '' }}</span>
                        <span class="text-[10px] text-zinc-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </flux:menu.item>
            @empty
                <div class="px-3 py-6 text-center">
                    <flux:text class="text-sm text-zinc-500">{{ __('Tidak ada notifikasi baru.') }}</flux:text>
                </div>
            @endforelse
        </flux:menu>
    </flux:dropdown>
</div>
