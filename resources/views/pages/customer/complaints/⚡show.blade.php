<?php

use App\Enums\ComplaintStatus;
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

    {{-- ─── Header ringkasan ─────────────────────────────── --}}
    <div class="rounded-2xl border border-line bg-white p-5 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2" x-data="{ copied: false }">
                    <flux:heading size="xl" class="font-mono tabular-nums">{{ $complaint->ticket_number }}</flux:heading>
                    <button
                        type="button"
                        @click="navigator.clipboard.writeText('{{ $complaint->ticket_number }}').then(() => { copied = true; $dispatch('sipena-toast'); setTimeout(() => copied = false, 2000) })"
                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-line text-muted transition hover:bg-surface focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent dark:border-zinc-700 dark:hover:bg-zinc-800"
                        :aria-label="copied ? '{{ __('Tersalin') }}' : '{{ __('Salin nomor tiket') }}'"
                    >
                        <svg x-show="!copied" class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7 7V4.5A1.5 1.5 0 0 1 8.5 3h7A1.5 1.5 0 0 1 17 4.5v7a1.5 1.5 0 0 1-1.5 1.5H13M4.5 7h7A1.5 1.5 0 0 1 13 8.5v7A1.5 1.5 0 0 1 11.5 17h-7A1.5 1.5 0 0 1 3 15.5v-7A1.5 1.5 0 0 1 4.5 7Z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <svg x-show="copied" x-cloak class="h-4 w-4 text-success" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5 10 3.2 3.2L15 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M6 2.5v2m8-2v2M3.5 7h13M4.5 4.5h11A1.5 1.5 0 0 1 17 6v9.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 3 15.5V6a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ $complaint->created_at->translatedFormat('d M Y, H:i') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary-600"></span>{{ $complaint->category->name }}
                    </span>
                    @if ($complaint->department)
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-gold-500"></span>{{ $complaint->department->name }}
                        </span>
                    @endif
                </div>
            </div>
            <x-status-badge :status="$complaint->status" />
        </div>

        {{-- Penjelasan status --}}
        <div class="mt-4 rounded-xl bg-primary-50 px-4 py-3 text-sm text-ink dark:bg-zinc-800 dark:text-zinc-100">
            <span class="font-semibold">{{ $complaint->status->label() }}:</span>
            {{ $complaint->status->description() }}
        </div>

        {{-- Progress stepper / alert penolakan --}}
        @if ($complaint->status === \App\Enums\ComplaintStatus::Ditolak)
            <div class="mt-4 rounded-xl border border-danger/30 bg-danger/5 p-4">
                <div class="flex items-start gap-2">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-danger" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4.5M10 13.2v.3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                    <div>
                        <p class="text-sm font-semibold text-danger">{{ __('Pengaduan Ditolak') }}</p>
                        <p class="mt-1 text-sm text-ink dark:text-zinc-100">{{ $complaint->rejection_reason ?: __('Tidak ada alasan yang dicantumkan.') }}</p>
                    </div>
                </div>
            </div>
        @else
            @php
                $currentIndex = $complaint->status->flowIndex();
                $escalatedReached = $complaint->histories->contains(fn ($h) => $h->status === \App\Enums\ComplaintStatus::Dieskalasi);
            @endphp
            <ol class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-5">
                @foreach (\App\Enums\ComplaintStatus::flow() as $i => $step)
                    @php
                        $isEscalationStep = $step === \App\Enums\ComplaintStatus::Dieskalasi;
                        $skipped = $isEscalationStep && ! $escalatedReached && $currentIndex > $i;
                        $done = $currentIndex > $i && ! $skipped;
                        $active = $currentIndex === $i;
                    @endphp
                    <li class="relative flex items-center gap-3 sm:flex-col sm:items-start sm:gap-2" wire:key="step-{{ $step->value }}">
                        <div class="flex items-center gap-3 sm:w-full">
                            <span @class([
                                'flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-bold tabular-nums',
                                'bg-primary-800 text-white' => $done,
                                'bg-white text-primary-700 ring-2 ring-gold-500' => $active,
                                'bg-surface text-muted ring-1 ring-line' => ! $done && ! $active,
                                'opacity-40' => $skipped,
                            ])>
                                @if ($done)
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m4 8 2.5 2.5L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </span>
                            {{-- garis penghubung (desktop) --}}
                            @unless ($loop->last)
                                <span @class(['hidden h-px flex-1 sm:block', 'bg-primary-800' => $done, 'bg-line' => ! $done])></span>
                            @endunless
                        </div>
                        <span @class([
                            'text-xs font-medium',
                            'text-ink dark:text-white' => $active || $done,
                            'text-muted' => ! $active && ! $done,
                            'line-through opacity-50' => $skipped,
                        ])>{{ $step->label() }}</span>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

    {{-- ─── Konten utama ─────────────────────────────────── --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Data pengaduan --}}
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="sm">{{ __('Data Pengaduan') }}</flux:heading>
                <flux:separator class="my-3" />
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-muted">{{ __('Kategori') }}</dt>
                        <dd class="font-medium text-ink dark:text-white">{{ $complaint->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">{{ __('Jenis Rekening') }}</dt>
                        <dd class="font-medium text-ink dark:text-white">{{ $complaint->account_type->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">{{ __('Prioritas') }}</dt>
                        <dd class="mt-0.5"><x-priority-badge :priority="$complaint->priority" /></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">{{ __('Departemen Terkait') }}</dt>
                        <dd class="font-medium text-ink dark:text-white">{{ $complaint->department?->name ?? '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-muted">{{ __('Subjek') }}</dt>
                        <dd class="font-medium text-ink dark:text-white">{{ $complaint->subject }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-muted">{{ __('Deskripsi') }}</dt>
                        <dd class="whitespace-pre-line text-ink dark:text-zinc-100">{{ $complaint->description }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Identitas --}}
            <div class="rounded-2xl border border-line bg-white p-5 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="sm">{{ __('Identitas Pengadu') }}</flux:heading>
                <flux:separator class="my-3" />
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-muted">{{ __('Nama') }}</dt>
                        <dd class="font-medium text-ink dark:text-white">{{ $complaint->reporter_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">{{ __('Email') }}</dt>
                        <dd class="font-medium text-ink dark:text-white">{{ $complaint->reporter_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-muted">{{ __('No. HP') }}</dt>
                        <dd class="font-medium text-ink dark:text-white">{{ $complaint->reporter_phone ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Timeline riwayat --}}
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Riwayat & Tindak Lanjut') }}</flux:heading>
            <flux:separator class="my-3" />

            <ol>
                @foreach ($complaint->histories as $history)
                    <li wire:key="history-{{ $history->id }}" class="grid grid-cols-[24px_1fr] gap-x-3">
                        {{-- Rail: titik + garis --}}
                        <div class="flex flex-col items-center">
                            <span @class([
                                'mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full',
                                'bg-primary-600' => ! $loop->first,
                                'bg-gold-500 ring-4 ring-gold-500/20' => $loop->first,
                            ])></span>
                            @unless ($loop->last)
                                <span class="w-0.5 flex-1 bg-line dark:bg-zinc-700"></span>
                            @endunless
                        </div>

                        {{-- Konten --}}
                        <div @class([
                            'min-w-0 pb-5',
                            '-mt-1 rounded-lg bg-primary-50 px-3 py-2 dark:bg-zinc-800' => $loop->first,
                        ])
                            x-data="{ expanded: false }"
                        >
                            <div class="flex flex-wrap items-center gap-2">
                                <x-status-badge :status="$history->status" />
                                <span class="text-sm text-muted tabular-nums">{{ $history->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            @if ($history->user)
                                <p class="mt-1 text-xs text-muted">{{ $history->user->name }}</p>
                            @endif
                            @if ($history->follow_up)
                                <p
                                    class="mt-1 whitespace-pre-line text-sm text-ink dark:text-zinc-100"
                                    :class="!expanded && 'line-clamp-4'"
                                >{{ $history->follow_up }}</p>
                                @if (\Illuminate\Support\Str::length($history->follow_up) > 180)
                                    <button type="button" @click="expanded = !expanded" class="mt-1 text-xs font-medium text-primary-600 hover:underline dark:text-primary-300">
                                        <span x-show="!expanded">{{ __('Lihat selengkapnya') }}</span>
                                        <span x-show="expanded" x-cloak>{{ __('Sembunyikan') }}</span>
                                    </button>
                                @endif
                            @endif
                            @if ($history->department)
                                <p class="mt-1 text-xs text-muted">{{ __('Departemen: :dept', ['dept' => $history->department->name]) }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    {{-- Toast "nomor tiket disalin" --}}
    <div
        x-data="{ show: false, timer: null }"
        x-on:sipena-toast.window="show = true; clearTimeout(timer); timer = setTimeout(() => show = false, 2200)"
        x-show="show"
        x-cloak
        x-transition
        class="fixed bottom-5 left-1/2 z-50 -translate-x-1/2 rounded-xl bg-primary-800 px-4 py-2.5 text-sm font-medium text-white shadow-card"
        role="status"
    >
        {{ __('Nomor tiket disalin') }}
    </div>
</div>
