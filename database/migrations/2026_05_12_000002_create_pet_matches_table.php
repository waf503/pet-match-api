<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('match_proposals')->cascadeOnDelete();
            $table->foreignId('pet_a_id')->constrained('pets')->cascadeOnDelete();
            $table->foreignId('pet_b_id')->constrained('pets')->cascadeOnDelete();
            $table->foreignId('user_a_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_b_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->string('close_reason')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_matches');
    }
};
