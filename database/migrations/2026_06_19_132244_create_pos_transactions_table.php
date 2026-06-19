<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('transaction_type', ['registrasi', 'retail', 'denda'])->index();
            $table->foreignId('team_id')->nullable()->constrained('tournament_teams')->nullOnDelete();
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'qris'])->default('cash');
            $table->string('cashier_name', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_transactions');
    }
};
