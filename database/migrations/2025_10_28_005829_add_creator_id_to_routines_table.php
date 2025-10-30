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
        Schema::table('routines', function (Blueprint $table) {
            // Agrega la columna creator_id (quién creó la rutina)
            // Usa foreignId() para crear la columna y la clave foránea en una sola línea.
            $table->foreignId('creator_id')
                  ->nullable() // Permite NULL si el cliente crea su propia rutina o por si se borra el creador.
                  ->after('user_id') // Posiciona la columna después de 'user_id'
                  ->constrained('users') // Hace referencia a la tabla 'users'
                  ->onDelete('set null'); // Si el usuario creador se elimina, el campo se pone a NULL.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            // 1. Eliminar la clave foránea para evitar errores
            $table->dropConstrainedForeignId('creator_id');
            // 2. Luego eliminar la columna
            $table->dropColumn('creator_id');
        });
    }
};
