<?php

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\User;
use Database\Seeders\MasterDataSeeder;
use Database\Seeders\RolePermissionSeeder;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed([RolePermissionSeeder::class, MasterDataSeeder::class]);
});

function roleUser(UserRole $role): User
{
    $user = User::factory()->create(['phone' => '0812']);
    $user->assignRole($role->value);

    return $user;
}

it('semua halaman nasabah dapat dirender', function () {
    $nasabah = roleUser(UserRole::Nasabah);
    $complaint = Complaint::factory()->create([
        'user_id' => $nasabah->id,
        'category_id' => Category::first()->id,
    ]);

    actingAs($nasabah)->get(route('customer.complaints.index'))->assertOk();
    actingAs($nasabah)->get(route('customer.complaints.create'))->assertOk();
    actingAs($nasabah)->get(route('customer.complaints.show', $complaint))->assertOk();
    actingAs($nasabah)->get(route('dashboard'))->assertRedirect(route('customer.complaints.index'));
});

it('semua halaman cs dapat dirender', function () {
    $cs = roleUser(UserRole::Cs);
    $nasabah = roleUser(UserRole::Nasabah);
    $complaint = Complaint::factory()->create([
        'user_id' => $nasabah->id,
        'category_id' => Category::first()->id,
    ]);

    actingAs($cs)->get(route('staff.dashboard'))->assertOk();
    actingAs($cs)->get(route('staff.complaints.index'))->assertOk();
    actingAs($cs)->get(route('staff.complaints.show', $complaint))->assertOk();
    actingAs($cs)->get(route('staff.categories.index'))->assertOk();
    actingAs($cs)->get(route('staff.departments.index'))->assertOk();
    actingAs($cs)->get(route('staff.users.index'))->assertOk();
});

it('halaman pimpinan dapat dirender', function () {
    $pimpinan = roleUser(UserRole::Pimpinan);

    actingAs($pimpinan)->get(route('manager.dashboard'))->assertOk();
    actingAs($pimpinan)->get(route('dashboard'))->assertRedirect(route('manager.dashboard'));
});
