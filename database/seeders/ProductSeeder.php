<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Laptop', 'price' => 999.99, 'stock' => 10],
            ['name' => 'Mouse', 'price' => 29.99, 'stock' => 50],
            ['name' => 'Keyboard', 'price' => 79.99, 'stock' => 30],
            ['name' => 'Monitor', 'price' => 299.99, 'stock' => 15],
            ['name' => 'Headphones', 'price' => 149.99, 'stock' => 25],
            ['name' => 'Webcam', 'price' => 89.99, 'stock' => 20],
            ['name' => 'USB Cable', 'price' => 9.99, 'stock' => 100],
            ['name' => 'SSD 500GB', 'price' => 59.99, 'stock' => 40],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
