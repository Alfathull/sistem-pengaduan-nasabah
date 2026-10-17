<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            MasterDataSeeder::class,
        ]);

        $this->createDemoUsers();

        $this->call(DemoComplaintSeeder::class);
    }

    /**
     * Buat akun demo (password: password) untuk tiap role.
     */
    private function createDemoUsers(): void
    {
        $cs = User::firstOrCreate(
            ['email' => 'cs@sipena.test'],
            [
                'name' => 'Petugas Customer Service',
                'phone' => '081100000001',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );
        $cs->syncRoles([UserRole::Cs->value]);

        $pimpinan = User::firstOrCreate(
            ['email' => 'pimpinan@sipena.test'],
            [
                'name' => 'Pimpinan Cabang',
                'phone' => '081100000002',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );
        $pimpinan->syncRoles([UserRole::Pimpinan->value]);

        $nasabah1 = User::firstOrCreate(
            ['email' => 'nasabah1@sipena.test'],
            [
                'name' => 'Andi Nasabah',
                'phone' => '081200000011',
                'account_type' => AccountType::Tabungan,
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );
        $nasabah1->syncRoles([UserRole::Nasabah->value]);

        $nasabah2 = User::firstOrCreate(
            ['email' => 'nasabah2@sipena.test'],
            [
                'name' => 'Siti Nasabah',
                'phone' => '081200000012',
                'account_type' => AccountType::Giro,
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );
        $nasabah2->syncRoles([UserRole::Nasabah->value]);
    }
}
