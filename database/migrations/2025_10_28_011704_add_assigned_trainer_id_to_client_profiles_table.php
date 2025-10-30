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
        if (Schema::hasTable('client_profiles') && !Schema::hasColumn('client_profiles', 'assigned_trainer_id')) {
            Schema::table('client_profiles', function (Blueprint $table) {
                // Columna para almacenar el ID del entrenador asignado.
                // Es nullable porque un cliente puede no tener un entrenador asignado.
                $table->foreignId('assigned_trainer_id')
                      ->nullable()
                      ->after('user_id') 
                      // Define la clave foránea a la tabla 'users'
                      ->constrained('users') 
                      // Si el entrenador es eliminado, el campo se establece en NULL 
                      // (el cliente queda desvinculado).
                      ->onDelete('set null'); 
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Define la lógica para revertir la migración
        Schema::table('client_profiles', function (Blueprint $table) {
             if (Schema::hasColumn('client_profiles', 'assigned_trainer_id')) {
                // 1. Eliminar la restricción de clave foránea
                // Laravel automáticamente usa el nombre de la columna para la clave foránea.
                $table->dropForeign(['assigned_trainer_id']); 
                
                // 2. Eliminar la columna
                $table->dropColumn('assigned_trainer_id');
            }
        });
    }
};
