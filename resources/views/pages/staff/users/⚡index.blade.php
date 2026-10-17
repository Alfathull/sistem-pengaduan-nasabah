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
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(UserRole::values())],
            'account_type' => ['nullable', Rule::in(array_column(AccountType::cases(), 'value'))],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8'],
        ], [], [
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

    <div class="mt-4 overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700">
        <flux:table :paginate="$users">
            <flux:table.columns>
                <flux:table.column>{{ __('Nama') }}</flux:table.column>
                <flux:table.column>{{ __('Email') }}</flux:table.column>
                <flux:table.column>{{ __('No. HP') }}</flux:table.column>
                <flux:table.column>{{ __('Role') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($users as $user)
                    <flux:table.row wire:key="user-{{ $user->id }}">
                        <flux:table.cell variant="strong">{{ $user->name }}</flux:table.cell>
                        <flux:table.cell>{{ $user->email }}</flux:table.cell>
                        <flux:table.cell>{{ $user->phone ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="blue" size="sm" inset="top bottom">
                                {{ \App\Enums\UserRole::tryFrom($user->roles->first()?->name ?? '')?->label() ?? '-' }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button size="xs" variant="ghost" icon="pencil-square" wire:click="edit({{ $user->id }})">{{ __('Ubah') }}</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:modal wire:model.self="showModal" class="max-w-md">
        <form wire:submit="save" class="space-y-4">
            <flux:heading size="lg">{{ $editingId ? __('Ubah Pengguna') : __('Tambah Pengguna') }}</flux:heading>
            <flux:input wire:model="name" :label="__('Nama')" />
            <flux:input wire:model="email" type="email" :label="__('Email')" />
            <flux:input wire:model="phone" :label="__('No. HP')" />
            <flux:select wire:model.live="role" :label="__('Role')" :placeholder="__('Pilih role')">
                @foreach ($roleOptions as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
            @if ($isNasabahRole)
                <flux:select wire:model="account_type" :label="__('Jenis Rekening')" :placeholder="__('Pilih jenis rekening')">
                    @foreach ($accountTypes as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
            @endif
            <flux:input wire:model="password" type="password" :label="$editingId ? __('Kata Sandi (kosongkan jika tidak diubah)') : __('Kata Sandi')" viewable />
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Simpan') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
