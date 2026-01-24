<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Operational Expenses
        $operational = ExpenseCategory::create([
            'name' => 'Operational Expenses',
            'description' => 'Day-to-day operational costs',
            'color' => '#3B82F6',
            'is_active' => true,
        ]);

        $operational->subcategories()->createMany([
            ['name' => 'Office Rent', 'description' => 'Monthly office space rental', 'is_active' => true],
            ['name' => 'Utilities', 'description' => 'Electricity, water, and other utilities', 'is_active' => true],
            ['name' => 'Internet & Phone', 'description' => 'Communication expenses', 'is_active' => true],
            ['name' => 'Office Supplies', 'description' => 'Stationery and supplies', 'is_active' => true],
        ]);

        // Network Infrastructure
        $network = ExpenseCategory::create([
            'name' => 'Network Infrastructure',
            'description' => 'Network equipment and maintenance',
            'color' => '#10B981',
            'is_active' => true,
        ]);

        $network->subcategories()->createMany([
            ['name' => 'Equipment Purchase', 'description' => 'Routers, switches, and network hardware', 'is_active' => true],
            ['name' => 'Equipment Maintenance', 'description' => 'Repair and maintenance costs', 'is_active' => true],
            ['name' => 'Fiber & Cabling', 'description' => 'Fiber optic and cable installation', 'is_active' => true],
            ['name' => 'Bandwidth & Transit', 'description' => 'Upstream bandwidth costs', 'is_active' => true],
        ]);

        // Personnel Expenses
        $personnel = ExpenseCategory::create([
            'name' => 'Personnel Expenses',
            'description' => 'Employee salaries and benefits',
            'color' => '#F59E0B',
            'is_active' => true,
        ]);

        $personnel->subcategories()->createMany([
            ['name' => 'Salaries', 'description' => 'Employee monthly salaries', 'is_active' => true],
            ['name' => 'Bonuses', 'description' => 'Performance and festival bonuses', 'is_active' => true],
            ['name' => 'Benefits', 'description' => 'Health insurance and other benefits', 'is_active' => true],
            ['name' => 'Training', 'description' => 'Employee training and development', 'is_active' => true],
        ]);

        // Marketing & Sales
        $marketing = ExpenseCategory::create([
            'name' => 'Marketing & Sales',
            'description' => 'Marketing and promotional expenses',
            'color' => '#EF4444',
            'is_active' => true,
        ]);

        $marketing->subcategories()->createMany([
            ['name' => 'Advertising', 'description' => 'Online and offline advertising', 'is_active' => true],
            ['name' => 'Promotions', 'description' => 'Customer promotional campaigns', 'is_active' => true],
            ['name' => 'Sales Commissions', 'description' => 'Sales team commissions', 'is_active' => true],
        ]);

        // Administrative
        $administrative = ExpenseCategory::create([
            'name' => 'Administrative',
            'description' => 'Administrative and legal expenses',
            'color' => '#8B5CF6',
            'is_active' => true,
        ]);

        $administrative->subcategories()->createMany([
            ['name' => 'Legal Fees', 'description' => 'Legal consultation and services', 'is_active' => true],
            ['name' => 'Licenses & Permits', 'description' => 'Business licenses and permits', 'is_active' => true],
            ['name' => 'Insurance', 'description' => 'Business insurance premiums', 'is_active' => true],
            ['name' => 'Bank Fees', 'description' => 'Banking and transaction fees', 'is_active' => true],
        ]);

        // Miscellaneous
        ExpenseCategory::create([
            'name' => 'Miscellaneous',
            'description' => 'Other miscellaneous expenses',
            'color' => '#6B7280',
            'is_active' => true,
        ]);
    }
}
