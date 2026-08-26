<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $cashier = User::where('username', 'cashier')->first();
        if (!$cashier) {
            return;
        }

        $products = Product::where('is_active', true)->get();
        if ($products->isEmpty()) {
            return;
        }

        for ($i = 0; $i < 10; $i++) {
            $itemsCount = rand(1, 4);
            $selectedProducts = $products->random($itemsCount);
            $subtotal = 0;
            $saleItems = [];

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 3);
                $unitPrice = (float) $product->price;
                $totalPrice = round($unitPrice * $qty, 2);
                $subtotal += $totalPrice;

                $saleItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            $tax = round($subtotal * 0.15, 2);

            $sale = Sale::create([
                'cashier_id' => $cashier->id,
                'cashier_name' => $cashier->username,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
                'payment_method' => fake()->randomElement(['cash', 'card']),
            ]);

            foreach ($saleItems as $item) {
                $sale->items()->create($item);
            }
        }
    }
}
