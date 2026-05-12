<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('nombre_plural');
            $table->string('icono', 10);
            $table->unsignedTinyInteger('orden')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('species'); }
};
