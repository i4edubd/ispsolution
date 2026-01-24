<?php

namespace Database\Seeders;

use App\Models\VatProfile;
use Illuminate\Database\Seeder;

class VatProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Standard VAT rate (common in many countries)
        VatProfile::create([
            'name' => 'Standard VAT',
            'rate' => 15.00,
            'description' => 'Standard VAT rate applied to most goods and services',
            'is_default' => true,
            'is_active' => true,
        ]);

        // Reduced VAT rate
        VatProfile::create([
            'name' => 'Reduced VAT',
            'rate' => 5.00,
            'description' => 'Reduced VAT rate for specific goods or services',
            'is_default' => false,
            'is_active' => true,
        ]);

        // Zero VAT rate
        VatProfile::create([
            'name' => 'Zero VAT',
            'rate' => 0.00,
            'description' => 'Zero-rated VAT for exempt goods or services',
            'is_default' => false,
            'is_active' => true,
        ]);

        // High VAT rate (luxury items)
        VatProfile::create([
            'name' => 'High VAT',
            'rate' => 25.00,
            'description' => 'High VAT rate for luxury or premium services',
            'is_default' => false,
            'is_active' => false,
        ]);
    }
}
