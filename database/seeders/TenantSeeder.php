<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'Demo ISP',
                'domain' => 'demo-isp.local',
                'subdomain' => 'demo',
                'database' => null,
                'settings' => [
                    'currency' => 'BDT',
                    'timezone' => 'Asia/Dhaka',
                    'billing_day' => 1,
                ],
                'status' => 'active',
            ],
            [
                'name' => 'Test ISP',
                'domain' => 'test-isp.local',
                'subdomain' => 'test',
                'database' => null,
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'America/New_York',
                    'billing_day' => 1,
                ],
                'status' => 'active',
            ],
        ];

        foreach ($tenants as $tenantData) {
            Tenant::firstOrCreate(
                ['domain' => $tenantData['domain']],
                $tenantData
            );
        }

        $this->command->info('Tenants seeded successfully!');
    }
}
