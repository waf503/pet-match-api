<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        // Change raza column to TEXT for JSON array storage
        Schema::table('pets', function (Blueprint $table) {
            $table->text('raza')->nullable()->change();
        });

        // Migrate existing single-string values to JSON arrays
        DB::table('pets')
            ->whereNotNull('raza')
            ->where('raza', '!=', '')
            ->whereRaw("raza NOT LIKE '[%'")
            ->get()
            ->each(fn($pet) => DB::table('pets')->where('id', $pet->id)
                ->update(['raza' => json_encode([$pet->raza])]));
    }

    public function down(): void {
        Schema::table('pets', function (Blueprint $table) {
            $table->string('raza', 255)->nullable()->change();
        });
    }
};
