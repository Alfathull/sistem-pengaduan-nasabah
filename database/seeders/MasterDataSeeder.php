<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed master data kategori layanan dan departemen.
     */
    public function run(): void
    {
        $categories = [
            'Cek Saldo',
            'Mutasi Rekening',
            'ATM/CDM/CRM',
            'Mobile Banking',
            'SMS Banking',
            'Internet Banking',
            'EDC',
            'BSB Lur',
            'Lainnya',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name], ['is_active' => true]);
        }

        $departments = [
            'Customer Service',
            'Operasional',
            'E-Channel',
            'Kredit',
        ];

        foreach ($departments as $name) {
            Department::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
