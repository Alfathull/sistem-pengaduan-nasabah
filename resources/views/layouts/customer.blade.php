<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <x-app-logo href="{{ route('customer.complaints.index') }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="inbox-stack" :href="route('customer.complaints.index')" :current="request()->routeIs('customer.complaints.index') || request()->routeIs('customer.complaints.show')" wire:navigate>
                    {{ __('Pengaduan Saya') }}
                </flux:navbar.item>
                <flux:navbar.item icon="plus-circle" :href="route('customer.complaints.create')" :current="request()->routeIs('customer.complaints.create')" wire:navigate>
                    {{ __('Buat Pengaduan') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <div class="mr-2">
                <livewire:notification-bell />
            </div>

            <x-mobile-user-menu />
        </flux:header>

        <!-- Navigasi bawah untuk mobile -->
        <flux:main container>
            <nav class="mb-4 flex gap-2 lg:hidden">
                <flux:button size="sm" variant="{{ request()->routeIs('customer.complaints.index') ? 'primary' : 'ghost' }}" icon="inbox-stack" :href="route('customer.complaints.index')" wire:navigate>
                    {{ __('Pengaduan Saya') }}
                </flux:button>
                <flux:button size="sm" variant="{{ request()->routeIs('customer.complaints.create') ? 'primary' : 'ghost' }}" icon="plus-circle" :href="route('customer.complaints.create')" wire:navigate>
                    {{ __('Buat') }}
                </flux:button>
            </nav>

            {{ $slot }}
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
