<?php

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Http\Requests\UpdateComplaintStatusRequest;
use App\Models\Complaint;
use App\Models\Department;
use App\Services\ComplaintService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::staff')] #[Title('Detail Tiket')] class extends Component {
    public Complaint $complaint;

    public string $status = '';
    public string $priority = '';
    public ?int $department_id = null;
    public string $follow_up = '';
    public string $notes = '';
    public string $rejection_reason = '';
    public bool $notify = true;

    public function mount(Complaint $complaint): void
    {
        $this->authorize('view', $complaint);
        $this->complaint = $complaint;
        $this->status = $complaint->status->value;
        $this->priority = $complaint->priority?->value ?? '';
        $this->department_id = $complaint->department_id;
        $this->notes = $complaint->notes ?? '';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return (new UpdateComplaintStatusRequest)->rules();
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return (new UpdateComplaintStatusRequest)->attributes();
    }

    public function save(ComplaintService $service): void
    {
        $this->authorize('manage', $this->complaint);

        $validated = $this->validate();

        $service->updateStatus($this->complaint, Auth::user(), [
            ...$validated,
            'notify' => $this->notify,
        ]);

        $this->complaint->refresh();
        $this->follow_up = '';
        $this->rejection_reason = '';

        Flux::toast(variant: 'success', text: __('Tiket berhasil diperbarui.'));
    }

    public function with(): array
    {
        $this->complaint->load(['category', 'department', 'histories.user', 'histories.department']);

        return [
            'canManage' => Auth::user()->can('manage', $this->complaint),
            'statusOptions' => ComplaintStatus::options(),
            'priorityOptions' => ComplaintPriority::options(),
            'departments' => Department::where('is_active', true)->orderBy('name')->get(),
            'isEscalated' => $this->status === ComplaintStatus::Dieskalasi->value,
            'isRejected' => $this->status === ComplaintStatus::Ditolak->value,
        ];
    }
}; ?>

<div wire:poll.30s>
    <div class="mb-4">
        <flux:button size="sm" variant="ghost" icon="arrow-left" :href="route('staff.complaints.index')" wire:navigate>
            {{ __('Kembali ke daftar') }}
        </flux:button>
    </div>

    <div class="flex items-center gap-2">
        <flux:heading size="xl">{{ $complaint->ticket_number }}</flux:heading>
        <x-status-badge :status="$complaint->status" />
        <x-priority-badge :priority="$complaint->priority" />
    </div>
    <flux:text class="mt-1">{{ __('Dibuat :date', ['date' => $complaint->created_at->translatedFormat('d F Y H:i')]) }}</flux:text>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <!-- Kolom kiri: detail + riwayat -->
        <div class="space-y-6 lg:col-span-2">
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <flux:heading size="sm">{{ __('Identitas & Data Pengaduan') }}</flux:heading>
                <flux:separator class="my-3" />
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div><flux:text class="text-xs text-zinc-500">{{ __('Nama') }}</flux:text><flux:text class="font-medium">{{ $complaint->reporter_name }}</flux:text></div>
                    <div><flux:text class="text-xs text-zinc-500">{{ __('Email') }}</flux:text><flux:text class="font-medium">{{ $complaint->reporter_email }}</flux:text></div>
                    <div><flux:text class="text-xs text-zinc-500">{{ __('No. HP') }}</flux:text><flux:text class="font-medium">{{ $complaint->reporter_phone ?? '-' }}</flux:text></div>
                    <div><flux:text class="text-xs text-zinc-500">{{ __('Jenis Rekening') }}</flux:text><flux:text class="font-medium">{{ $complaint->account_type->label() }}</flux:text></div>
                    <div><flux:text class="text-xs text-zinc-500">{{ __('Kategori') }}</flux:text><flux:text class="font-medium">{{ $complaint->category->name }}</flux:text></div>
                    <div><flux:text class="text-xs text-zinc-500">{{ __('Departemen') }}</flux:text><flux:text class="font-medium">{{ $complaint->department?->name ?? '-' }}</flux:text></div>
                    <div class="sm:col-span-2"><flux:text class="text-xs text-zinc-500">{{ __('Subjek') }}</flux:text><flux:text class="font-medium">{{ $complaint->subject }}</flux:text></div>
                    <div class="sm:col-span-2"><flux:text class="text-xs text-zinc-500">{{ __('Deskripsi') }}</flux:text><flux:text class="whitespace-pre-line">{{ $complaint->description }}</flux:text></div>
                    @if ($complaint->notes)
                        <div class="sm:col-span-2"><flux:text class="text-xs text-zinc-500">{{ __('Catatan') }}</flux:text><flux:text class="whitespace-pre-line">{{ $complaint->notes }}</flux:text></div>
                    @endif
                </dl>
            </div>

            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <flux:heading size="sm">{{ __('Riwayat Tindak Lanjut') }}</flux:heading>
                <flux:separator class="my-3" />
                <ol class="relative space-y-5 border-s border-zinc-200 ps-5 dark:border-zinc-700">
                    @foreach ($complaint->histories as $history)
                        <li wire:key="hist-{{ $history->id }}" class="relative">
                            <span class="absolute -start-[1.4rem] mt-1 flex h-3 w-3 rounded-full bg-blue-600 ring-4 ring-white dark:ring-zinc-800"></span>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-status-badge :status="$history->status" />
                                <flux:text class="text-xs text-zinc-500">{{ $history->created_at->translatedFormat('d M Y H:i') }}</flux:text>
                                @if ($history->user)
                                    <flux:text class="text-xs text-zinc-500">&middot; {{ $history->user->name }}</flux:text>
                                @endif
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

        <!-- Kolom kanan: form pengelolaan -->
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="sm">{{ __('Kelola Tiket') }}</flux:heading>
            <flux:separator class="my-3" />

            @if (! $canManage)
                <flux:callout variant="secondary" icon="lock-closed">
                    {{ __('Tiket ini sudah ditutup dan tidak dapat diubah lagi.') }}
                </flux:callout>
            @else
                <form wire:submit="save" class="space-y-4">
                    <flux:select wire:model.live="status" :label="__('Status')">
                        @foreach ($statusOptions as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="priority" :label="__('Prioritas')" :placeholder="__('Pilih prioritas')">
                        @foreach ($priorityOptions as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    @if ($isEscalated)
                        <flux:select wire:model="department_id" :label="__('Eskalasi ke Departemen')" :placeholder="__('Pilih departemen')">
                            @foreach ($departments as $department)
                                <flux:select.option value="{{ $department->id }}">{{ $department->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    @endif

                    @if ($isRejected)
                        <flux:textarea wire:model="rejection_reason" :label="__('Alasan Penolakan')" rows="3" :placeholder="__('Wajib diisi saat menolak tiket')" />
                    @endif

                    <flux:textarea wire:model="follow_up" :label="__('Keterangan Tindak Lanjut')" rows="3" :placeholder="__('Catatan untuk riwayat perubahan ini')" />

                    <flux:textarea wire:model="notes" :label="__('Catatan (Notes)')" rows="2" :placeholder="__('Catatan internal pada tiket')" />

                    <flux:checkbox wire:model="notify" :label="__('Beri notifikasi ke nasabah (Customer Notification)')" />

                    <flux:button type="submit" variant="primary" icon="check" class="w-full">{{ __('Simpan Perubahan') }}</flux:button>
                </form>
            @endif
        </div>
    </div>
</div>
