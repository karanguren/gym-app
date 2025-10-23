<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Elimina la columna 'details' de la tabla 'workout_logs'.
     */
    public function up(): void
    {
        Schema::table('workout_logs', function (Blueprint $table) {
            // Verifica si la columna existe antes de intentar eliminarla
            if (Schema::hasColumn('workout_logs', 'details')) {
                $table->dropColumn('details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workout_logs', function (Blueprint $table) {
            // Si necesitas revertir, puedes volver a añadir la columna como TEXT/JSON/nullable.
            $table->json('details')->nullable(); 
        });
    }
};