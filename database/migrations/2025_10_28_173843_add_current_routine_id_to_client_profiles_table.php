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
        Schema::table('client_profiles', function (Blueprint $table) {
            // Añade la nueva clave foránea que apunta a la tabla 'routines'
            $table->foreignId('current_routine_id')
                  ->nullable() // Es nullable porque al inicio un cliente podría no tener rutina
                  ->constrained('routines')
                  ->onDelete('set null') // Si se borra la rutina, el campo se pone a NULL
                  ->after('assigned_trainer_id'); // O donde prefieras colocarla
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropForeign(['current_routine_id']);
            $table->dropColumn('current_routine_id');
        });
    }
};