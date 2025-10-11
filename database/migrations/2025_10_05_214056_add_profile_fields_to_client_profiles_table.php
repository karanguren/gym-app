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
        // Asegúrate de que esta tabla existe antes de ejecutar esta migración.
        // Si no existe, deberías crear la tabla primero.
        if (Schema::hasTable('client_profiles')) {
            Schema::table('client_profiles', function (Blueprint $table) {
                
                // Campos que faltaban o se asumieron:
                $table->date('birth_date')->nullable();
                $table->text('medical_history')->nullable();

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            // Revertir los cambios eliminando las columnas
            $table->dropColumn('medical_history');
            $table->dropColumn('birth_date');
        });
    }
};