<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'iPhone 15', 'category' => 'Electronics', 'price' => 999.99, 'stock_quantity' => 25, 'min_stock_alert' => 5],
            ['name' => 'Samsung Galaxy S24', 'category' => 'Electronics', 'price' => 849.99, 'stock_quantity' => 20, 'min_stock_alert' => 5],
            ['name' => 'Laptop Dell XPS', 'category' => 'Electronics', 'price' => 1299.99, 'stock_quantity' => 10, 'min_stock_alert' => 3],
            ['name' => 'USB-C Cable', 'category' => 'Electronics', 'price' => 9.99, 'stock_quantity' => 150, 'min_stock_alert' => 20],
            ['name' => 'Wireless Mouse', 'category' => 'Electronics', 'price' => 29.99, 'stock_quantity' => 45, 'min_stock_alert' => 10],
            ['name' => 'Mineral Water 1L', 'category' => 'Beverages', 'price' => 1.50, 'stock_quantity' => 200, 'min_stock_alert' => 50],
            ['name' => 'Coffee Beans 500g', 'category' => 'Beverages', 'price' => 12.99, 'stock_quantity' => 3, 'min_stock_alert' => 10],
            ['name' => 'Orange Juice 1L', 'category' => 'Beverages', 'price' => 3.99, 'stock_quantity' => 60, 'min_stock_alert' => 15],
            ['name' => 'Chips Original', 'category' => 'Food', 'price' => 2.49, 'stock_quantity' => 80, 'min_stock_alert' => 20],
            ['name' => 'Chocolate Bar', 'category' => 'Food', 'price' => 1.99, 'stock_quantity' => 100, 'min_stock_alert' => 25],
            ['name' => 'Cleaning Spray', 'category' => 'Cleaning', 'price' => 4.99, 'stock_quantity' => 35, 'min_stock_alert' => 10],
            ['name' => 'Paper Towels', 'category' => 'Cleaning', 'price' => 6.99, 'stock_quantity' => 2, 'min_stock_alert' => 15],
            ['name' => 'Notebook A4', 'category' => 'Stationery', 'price' => 3.49, 'stock_quantity' => 50, 'min_stock_alert' => 10],
            ['name' => 'Ballpoint Pens (10)', 'category' => 'Stationery', 'price' => 5.99, 'stock_quantity' => 40, 'min_stock_alert' => 10],
            ['name' => 'Printer Paper A4', 'category' => 'Stationery', 'price' => 8.99, 'stock_quantity' => 4, 'min_stock_alert' => 10],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
