<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Elimina la columna 'exercise_ids' de la tabla 'routines'.
     */
    public function up(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            // Verifica si la columna existe antes de intentar eliminarla
            if (Schema::hasColumn('routines', 'exercise_ids')) {
                $table->dropColumn('exercise_ids');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Revierte el cambio (vuelve a añadir la columna 'exercise_ids' como TEXT para compatibilidad si la reversión es necesaria).
     */
    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            // Por si necesitas deshacer la migración, aunque es altamente desaconsejado.
            $table->text('exercise_ids')->nullable(); 
        });
    }
};