<?php

namespace Database\Factories;

use App\Models\ServicePackage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServicePackage>
 */
class ServicePackageFactory extends Factory
{
    protected $model = ServicePackage::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true) . ' Package',
            'description' => $this->faker->sentence(),
            'bandwidth_up' => $this->faker->randomElement([512, 1024, 2048, 5120, 10240]),
            'bandwidth_down' => $this->faker->randomElement([1024, 2048, 5120, 10240, 20480]),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'billing_cycle' => $this->faker->randomElement(['monthly', 'quarterly', 'yearly']),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
