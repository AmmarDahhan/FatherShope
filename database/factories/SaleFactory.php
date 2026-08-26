<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sale>
 */
class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 5, 1000);
        $tax = round($subtotal * 0.15, 2);

        return [
            'cashier_id' => User::factory(),
            'cashier_name' => fake()->userName(),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'payment_method' => fake()->randomElement(['cash', 'card']),
        ];
    }
}
