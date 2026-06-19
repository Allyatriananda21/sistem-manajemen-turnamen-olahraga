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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team1_id')->constrained('tournament_teams');
            $table->foreignId('team2_id')->constrained('tournament_teams');
            $table->string('round', 50)->nullable()->index();
            $table->dateTime('match_date')->nullable();
            $table->string('venue', 200)->nullable();
            $table->integer('score_team1')->default(0);
            $table->integer('score_team2')->default(0);
            $table->foreignId('winner_id')->nullable()->constrained('tournament_teams');
            $table->string('referee', 100)->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'done', 'cancelled'])->default('scheduled')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
