<?php

use App\Enums\AccountType;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

use function Pest\Laravel\post;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolePermissionSeeder::class);
});

it('nasabah baru terdaftar dengan role nasabah dan data profil lengkap', function () {
    post(route('register.store'), [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'phone' => '081234567890',
        'account_type' => AccountType::Tabungan->value,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $user = User::where('email', 'budi@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->phone)->toBe('081234567890')
        ->and($user->account_type)->toBe(AccountType::Tabungan)
        ->and($user->hasRole(UserRole::Nasabah->value))->toBeTrue();
});

it('registrasi menolak jenis rekening yang tidak valid', function () {
    post(route('register.store'), [
        'name' => 'Budi Santoso',
        'email' => 'budi2@example.com',
        'phone' => '081234567890',
        'account_type' => 'kartu-kredit',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('account_type');

    expect(User::where('email', 'budi2@example.com')->exists())->toBeFalse();
});
