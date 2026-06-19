<?php

namespace Database\Seeders;

use App\Models\PosProduct;
use Illuminate\Database\Seeder;

class PosProductSeeder extends Seeder
{
    /**
     * @var array<int, array{product_name: string, price: float, stock: int}>
     */
    private array $products = [
        [
            'product_name' => 'Air Mineral 600ml',
            'price' => 5000.00,
            'stock' => 100,
        ],
        [
            'product_name' => 'Snack Keripik',
            'price' => 8000.00,
            'stock' => 60,
        ],
        [
            'product_name' => 'Kaos Turnamen',
            'price' => 75000.00,
            'stock' => 30,
        ],
        [
            'product_name' => 'Minuman Energi',
            'price' => 12000.00,
            'stock' => 50,
        ],
        [
            'product_name' => 'Mie Ayam Cup',
            'price' => 10000.00,
            'stock' => 40,
        ],
    ];

    public function run(): void
    {
        foreach ($this->products as $product) {
            PosProduct::create($product);
        }
    }
}
