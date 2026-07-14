<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosProduct extends Model
{
    protected $fillable = [
        'product_name',
        'category',
        'price',
        'stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /** @return array<string, string> */
    public static function categories(): array
    {
        return [
            'makanan' => 'Makanan',
            'minuman' => 'Minuman',
            'perlengkapan' => 'Perlengkapan',
        ];
    }
}
