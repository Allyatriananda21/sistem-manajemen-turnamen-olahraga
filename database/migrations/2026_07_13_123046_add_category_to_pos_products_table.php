<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pos_products', function (Blueprint $table) {
            $table->enum('category', ['makanan', 'minuman', 'perlengkapan'])
                ->default('makanan')
                ->after('product_name')
                ->index();
        });

        // Set kategori awal berdasarkan nama produk yang sudah ada
        DB::table('pos_products')->where('product_name', 'like', '%mineral%')->orWhere('product_name', 'like', '%minum%')->orWhere('product_name', 'like', '%teh%')->orWhere('product_name', 'like', '%susu%')->orWhere('product_name', 'like', '%isotonik%')->update(['category' => 'minuman']);

        DB::table('pos_products')->where('product_name', 'like', '%kaos%')->orWhere('product_name', 'like', '%baju%')->orWhere('product_name', 'like', '%perlengkapan%')->update(['category' => 'perlengkapan']);
    }

    public function down(): void
    {
        Schema::table('pos_products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
