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
            // Agrega la nueva columna, nullable ya que los clientes sin entrenador no la tendrán.
            $table->foreignId('requested_trainer_id')
                  ->nullable()
                  ->after('assigned_trainer_id') // Colócala después de la columna asignada
                  ->constrained('users') // Hace referencia a la tabla 'users'
                  ->onDelete('set null'); // Si el usuario entrenador es eliminado, se establece en null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            // Asegúrate de eliminar la clave foránea antes de eliminar la columna
            $table->dropConstrainedForeignId('requested_trainer_id');
            // Elimina la columna
            $table->dropColumn('requested_trainer_id');
        });
    }
};
