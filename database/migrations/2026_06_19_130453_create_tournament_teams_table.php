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
        Schema::create('tournament_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('sport_type', 50);
            $table->string('coach_name', 100)->nullable();
            $table->string('contact_person', 100);
            $table->string('phone', 20);
            $table->string('logo', 255)->nullable();
            $table->timestamp('registered_at')->useCurrent();
            $table->enum('status', ['pending', 'approved', 'disqualified'])->default('pending')->index();
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->index();
            $table->string('invoice_number', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_teams');
    }
};
