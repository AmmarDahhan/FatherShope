<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function create(array $items, string $cashierId, string $cashierName, string $paymentMethod, float $tax = 0): Sale
    {
        return DB::transaction(function () use ($items, $cashierId, $cashierName, $paymentMethod, $tax) {
            $subtotal = 0;
            $saleItems = [];

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if (!$product->is_active) {
                    throw new InsufficientStockException($product->name, $item['quantity'], 0);
                }

                if ($product->stock_quantity < $item['quantity']) {
                    throw new InsufficientStockException(
                        $product->name,
                        $item['quantity'],
                        $product->stock_quantity
                    );
                }

                $unitPrice = (float) $product->price;
                $totalPrice = $unitPrice * $item['quantity'];
                $subtotal += $totalPrice;

                $product->decrement('stock_quantity', $item['quantity']);

                $saleItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];
            }

            $total = $subtotal + $tax;

            $sale = Sale::create([
                'cashier_id' => $cashierId,
                'cashier_name' => $cashierName,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $paymentMethod,
            ]);

            foreach ($saleItems as $saleItem) {
                $sale->items()->create($saleItem);
            }

            return $sale->load('items');
        });
    }
}
