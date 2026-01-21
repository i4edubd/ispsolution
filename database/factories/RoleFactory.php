<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'super-admin',
            'admin',
            'manager',
            'staff',
            'reseller',
            'sub-reseller',
            'customer',
            'card-distributor',
            'developer',
        ]);

        return [
            'name' => $name,
            'slug' => $name,
            'level' => $this->faker->numberBetween(10, 100),
        ];
    }
}
