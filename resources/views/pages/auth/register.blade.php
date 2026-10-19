<x-layouts::auth :title="__('Daftar')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Buat Akun Nasabah')" :description="__('Lengkapi data berikut untuk membuat akun SiPeNa')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6"
            x-data="{ submitting: false }" x-on:submit="submitting = true">
            @csrf
            <!-- Nama -->
            <flux:input
                name="name"
                :label="__('Nama Lengkap')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Nama lengkap')"
            />

            <!-- Email -->
            <flux:input
                name="email"
                :label="__('Alamat Email')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@contoh.com"
            />

            <!-- No. HP (hanya angka) -->
            <flux:input
                name="phone"
                :label="__('Nomor HP')"
                :value="old('phone')"
                type="tel"
                required
                autocomplete="tel"
                inputmode="numeric"
                maxlength="15"
                :placeholder="__('08xxxxxxxxxx')"
                x-on:input="$el.value = $el.value.replace(/\D/g, '')"
                x-on:keypress="if (!/[0-9]/.test($event.key)) $event.preventDefault()"
            />

            <!-- Jenis Rekening -->
            <x-ui.select
                name="account_type"
                :label="__('Jenis Rekening')"
                :placeholder="__('Pilih jenis rekening')"
                :options="\App\Enums\AccountType::options()"
                :error="$errors->first('account_type')"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Kata Sandi')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Kata sandi')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Konfirmasi Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Konfirmasi Kata Sandi')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Ulangi kata sandi')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full"
                    data-test="register-user-button"
                    x-bind:disabled="submitting"
                    x-bind:data-loading="submitting"
                >
                    <span x-show="!submitting">{{ __('Daftar') }}</span>
                    <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                        <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 0 1 8-8V0C5.4 0 0 5.4 0 12h4z"></path>
                        </svg>
                        {{ __('Memproses...') }}
                    </span>
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Sudah punya akun?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Masuk') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
