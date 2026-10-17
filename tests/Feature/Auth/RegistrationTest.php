<?php

use App\Enums\AccountType;
use Database\Seeders\RolePermissionSeeder;
use Laravel\Fortify\Features;

use function Pest\Laravel\seed;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
    seed(RolePermissionSeeder::class);
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'account_type' => AccountType::Tabungan->value,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
