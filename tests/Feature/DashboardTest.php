<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(RolePermissionSeeder::class);
});

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated nasabah are redirected to their complaints page', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Nasabah->value);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('customer.complaints.index'));
});

test('authenticated cs are redirected to the staff dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(UserRole::Cs->value);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('staff.dashboard'));
});
