<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->optional(0.7)->sentence(),
            'price' => fake()->randomFloat(2, 0.5, 500),
            'barcode' => fake()->unique()->numerify('############'),
            'category' => fake()->randomElement(['Electronics', 'Food', 'Beverages', 'Cleaning', 'Stationery']),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'min_stock_alert' => fake()->numberBetween(5, 20),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function lowStock(): static
    {
        return $this->state(fn () => [
            'stock_quantity' => 2,
            'min_stock_alert' => 10,
        ]);
    }
}
