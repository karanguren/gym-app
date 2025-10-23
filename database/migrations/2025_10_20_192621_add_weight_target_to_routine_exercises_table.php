<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Añade la columna 'weight_target' a la tabla 'routine_exercises'.
     */
    public function up(): void
    {
        Schema::table('routine_exercises', function (Blueprint $table) {
            $table->decimal('weight_target', 8, 2)->after('reps_target')->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routine_exercises', function (Blueprint $table) {
            $table->dropColumn('weight_target');
        });
    }
};