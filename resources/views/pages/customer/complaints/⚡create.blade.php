<?php

use App\Enums\AccountType;
use App\Http\Requests\StoreComplaintRequest;
use App\Models\Category;
use App\Models\Complaint;
use App\Services\ComplaintService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::customer')] #[Title('Buat Pengaduan')] class extends Component {
    public string $account_type = '';
    public ?int $category_id = null;
    public string $subject = '';
    public string $description = '';

    public function mount(): void
    {
        $this->authorize('create', Complaint::class);
        $this->account_type = Auth::user()->account_type?->value ?? '';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return (new StoreComplaintRequest)->rules();
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return (new StoreComplaintRequest)->attributes();
    }

    public function store(ComplaintService $service): void
    {
        $this->authorize('create', Complaint::class);

        $validated = $this->validate();

        $complaint = $service->create(Auth::user(), $validated);

        Flux::toast(
            variant: 'success',
            heading: __('Pengaduan terkirim'),
            text: __('Nomor tiket Anda: :ticket', ['ticket' => $complaint->ticket_number]),
        );

        $this->redirectRoute('customer.complaints.show', $complaint, navigate: true);
    }

    public function with(): array
    {
        return [
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            'accountTypes' => AccountType::options(),
        ];
    }
}; ?>

<div class="mx-auto w-full max-w-2xl">
    <flux:heading size="xl">{{ __('Buat Pengaduan Baru') }}</flux:heading>
    <flux:text class="mt-1">{{ __('Lengkapi formulir berikut. Nomor tiket akan dibuat otomatis setelah dikirim.') }}</flux:text>

    <flux:separator class="my-6" />

    <form wire:submit="store" class="space-y-6">
        <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
            <flux:heading size="sm">{{ __('Identitas Pengadu') }}</flux:heading>
            <div class="mt-3 grid gap-4 sm:grid-cols-2">
                <flux:input :label="__('Nama')" value="{{ auth()->user()->name }}" readonly />
                <flux:input :label="__('Email')" value="{{ auth()->user()->email }}" readonly />
                <flux:input :label="__('No. HP')" value="{{ auth()->user()->phone ?? '-' }}" readonly />
                <flux:select wire:model="account_type" :label="__('Jenis Rekening')" :placeholder="__('Pilih jenis rekening')">
                    @foreach ($accountTypes as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <flux:select wire:model="category_id" :label="__('Kategori Layanan')" :placeholder="__('Pilih kategori')">
            @foreach ($categories as $category)
                <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model="subject" :label="__('Subjek')" :placeholder="__('Ringkasan singkat pengaduan')" />

        <flux:textarea wire:model="description" :label="__('Deskripsi Pengaduan')" rows="5" :placeholder="__('Jelaskan permasalahan Anda secara rinci')" />

        <div class="flex items-center justify-end gap-3">
            <flux:button :href="route('customer.complaints.index')" variant="ghost" wire:navigate>{{ __('Batal') }}</flux:button>
            <flux:button type="submit" variant="primary" icon="paper-airplane">{{ __('Kirim Pengaduan') }}</flux:button>
        </div>
    </form>
</div>
