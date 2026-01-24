<?php

namespace Database\Factories;

use App\Models\HotspotUser;
use App\Models\Package;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HotspotUser>
 */
class HotspotUserFactory extends Factory
{
    protected $model = HotspotUser::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'phone_number' => '017' . $this->faker->unique()->numerify('########'),
            'username' => 'hs_' . $this->faker->unique()->numerify('########'),
            'password' => Hash::make('password'),
            'name' => $this->faker->name(),
            'email' => $this->faker->optional()->safeEmail(),
            'address' => $this->faker->optional()->address(),
            'package_id' => Package::factory(),
            'status' => 'active',
            'is_verified' => true,
            'verified_at' => now(),
            'expires_at' => now()->addDays(30),
            'mac_address' => null,
            'active_session_id' => null,
            'last_login_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'expires_at' => now()->subDays(5),
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
