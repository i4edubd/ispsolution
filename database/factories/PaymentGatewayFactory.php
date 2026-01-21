<?php

namespace Database\Factories;

use App\Models\PaymentGateway;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentGateway>
 */
class PaymentGatewayFactory extends Factory
{
    protected $model = PaymentGateway::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gateways = ['bkash', 'nagad', 'sslcommerz', 'stripe'];
        $slug = $this->faker->randomElement($gateways);

        return [
            'tenant_id' => Tenant::factory(),
            'name' => ucfirst($slug),
            'slug' => $slug,
            'is_active' => true,
            'configuration' => [
                'app_key' => $this->faker->uuid(),
                'app_secret' => $this->faker->uuid(),
                'merchant_id' => $this->faker->numerify('########'),
            ],
            'test_mode' => true,
        ];
    }

    /**
     * Indicate that the gateway is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the gateway is in production mode.
     */
    public function production(): static
    {
        return $this->state(fn (array $attributes) => [
            'test_mode' => false,
        ]);
    }
}
