<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('breeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->boolean('popular')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('breeds'); }
};
