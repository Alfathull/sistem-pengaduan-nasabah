<?php

use App\Enums\AccountType;
use App\Enums\UserRole;
use App\Models\User;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::staff')] #[Title('Kelola Pengguna')] class extends Component {
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $role = '';
    public string $account_type = '';
    public string $password = '';

    public string $search = '';

    public function create(): void
    {
        $this->reset(['editingId', 'name', 'email', 'phone', 'role', 'account_type', 'password']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function edit(User $user): void
    {
        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->roles->first()?->name ?? '';
        $this->account_type = $user->account_type?->value ?? '';
        $this->password = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'phone' => ['nullable', 'string', 'regex:/^[0-9]+$/', 'min:8', 'max:15'],
            'role' => ['required', Rule::in(UserRole::values())],
            'account_type' => ['nullable', Rule::in(array_column(AccountType::cases(), 'value'))],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8'],
        ], [
            'phone.regex' => 'Nomor HP hanya boleh berisi angka.',
        ], [
            'name' => 'nama',
            'email' => 'email',
            'phone' => 'nomor HP',
            'role' => 'role',
            'account_type' => 'jenis rekening',
            'password' => 'kata sandi',
        ]);

        $attributes = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'account_type' => $validated['role'] === UserRole::Nasabah->value ? ($validated['account_type'] ?: null) : null,
        ];

        if (! empty($validated['password'])) {
            $attributes['password'] = $validated['password'];
        }

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update($attributes);
        } else {
            $user = User::create([...$attributes, 'email_verified_at' => now()]);
        }

        $user->syncRoles([$validated['role']]);

        $this->showModal = false;
        Flux::toast(variant: 'success', text: __('Pengguna disimpan.'));
    }

    public function with(): array
    {
        $query = User::with('roles')->latest();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return [
            'users' => $query->paginate(10),
            'roleOptions' => UserRole::options(),
            'accountTypes' => AccountType::options(),
            'isNasabahRole' => $this->role === UserRole::Nasabah->value,
        ];
    }
}; ?>

<div>
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Kelola Pengguna') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Kelola akun nasabah, CS, dan pimpinan.') }}</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">{{ __('Tambah') }}</flux:button>
    </div>

    <div class="mt-6 max-w-xs">
        <flux:input wire:model.live.debounce.400ms="search" icon="magnifying-glass" :placeholder="__('Cari nama / email')" />
    </div>

    {{-- Desktop: tabel --}}
    <div class="mt-4 hidden overflow-hidden rounded-2xl border border-line bg-white shadow-card md:block dark:border-zinc-700 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line bg-surface text-left text-xs uppercase tracking-wide text-muted dark:border-zinc-700 dark:bg-zinc-800">
                        <th class="px-5 py-3.5 font-semibold">{{ __('Nama') }}</th>
                        <th class="px-5 py-3.5 font-semibold">{{ __('Email') }}</th>
                        <th class="px-5 py-3.5 font-semibold">{{ __('No. HP') }}</th>
                        <th class="px-5 py-3.5 font-semibold">{{ __('Role') }}</th>
                        <th class="px-5 py-3.5 text-right font-semibold"><span class="sr-only">{{ __('Aksi') }}</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="border-b border-line transition last:border-0 hover:bg-primary-50 dark:border-zinc-800 dark:hover:bg-zinc-800">
                            <td class="px-5 py-3.5 font-semibold text-ink dark:text-white">{{ $user->name }}</td>
                            <td class="px-5 py-3.5 text-ink dark:text-zinc-100">{{ $user->email }}</td>
                            <td class="px-5 py-3.5 text-muted tabular-nums">{{ $user->phone ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                <flux:badge color="blue" size="sm" inset="top bottom">
                                    {{ \App\Enums\UserRole::tryFrom($user->roles->first()?->name ?? '')?->label() ?? '-' }}
                                </flux:badge>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <flux:button size="xs" variant="ghost" icon="pencil-square" wire:click="edit({{ $user->id }})">{{ __('Ubah') }}</flux:button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile: daftar kartu --}}
    <div class="mt-4 space-y-3 md:hidden">
        @foreach ($users as $user)
            <div wire:key="ucard-{{ $user->id }}" class="rounded-2xl border border-line bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-ink dark:text-white">{{ $user->name }}</p>
                        <p class="truncate text-sm text-muted">{{ $user->email }}</p>
                    </div>
                    <flux:badge color="blue" size="sm" inset="top bottom">
                        {{ \App\Enums\UserRole::tryFrom($user->roles->first()?->name ?? '')?->label() ?? '-' }}
                    </flux:badge>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <span class="text-xs text-muted tabular-nums">{{ $user->phone ?? '-' }}</span>
                    <flux:button size="xs" variant="ghost" icon="pencil-square" wire:click="edit({{ $user->id }})">{{ __('Ubah') }}</flux:button>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-5">
        {{ $users->links('vendor.pagination.sipena', ['unit' => __('pengguna')]) }}
    </div>

    <flux:modal wire:model.self="showModal" class="max-w-md">
        <form wire:submit="save" class="space-y-4">
            <flux:heading size="lg">{{ $editingId ? __('Ubah Pengguna') : __('Tambah Pengguna') }}</flux:heading>
            <flux:input wire:model="name" :label="__('Nama')" />
            <flux:input wire:model="email" type="email" :label="__('Email')" />
            <flux:input
                wire:model="phone"
                :label="__('No. HP')"
                type="tel"
                inputmode="numeric"
                maxlength="15"
                x-on:input="$el.value = $el.value.replace(/\D/g, '')"
                x-on:keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
            />
            <x-ui.select wire:model.live="role" :label="__('Role')" :placeholder="__('Pilih role')" :options="$roleOptions" :error="$errors->first('role')" />
            @if ($isNasabahRole)
                <x-ui.select wire:model="account_type" :label="__('Jenis Rekening')" :placeholder="__('Pilih jenis rekening')" :options="$accountTypes" :error="$errors->first('account_type')" />
            @endif
            <flux:input wire:model="password" type="password" :label="$editingId ? __('Kata Sandi (kosongkan jika tidak diubah)') : __('Kata Sandi')" viewable />
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Simpan') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
