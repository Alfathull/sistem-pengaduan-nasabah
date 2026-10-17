<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('staff.dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Menu')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('staff.dashboard')" :current="request()->routeIs('staff.dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="inbox-stack" :href="route('staff.complaints.index')" :current="request()->routeIs('staff.complaints.*')" wire:navigate>
                        {{ __('Tiket Pengaduan') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Master Data')" class="grid">
                    <flux:sidebar.item icon="tag" :href="route('staff.categories.index')" :current="request()->routeIs('staff.categories.*')" wire:navigate>
                        {{ __('Kategori') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-office" :href="route('staff.departments.index')" :current="request()->routeIs('staff.departments.*')" wire:navigate>
                        {{ __('Departemen') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('staff.users.index')" :current="request()->routeIs('staff.users.*')" wire:navigate>
                        {{ __('Pengguna') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" />
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            <x-mobile-user-menu />
        </flux:header>

        <flux:main>
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
