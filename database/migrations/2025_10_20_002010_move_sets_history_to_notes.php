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
            // Asegura que 'exercise_ids' sea JSON, aunque solo guardará un array simple de IDs.
            // Si la columna ya existe, esto no hará nada. Si no existe, la crea.
            // Si ya existe y quieres cambiar el tipo, usa $table->json('exercise_ids')->change();
            if (!Schema::hasColumn('routines', 'exercise_ids')) {
                $table->json('exercise_ids')->after('name');
            }

            // Asegura que 'notes' (donde guardaremos el historial de sets) exista y sea JSON/text.
            // Usamos 'text' o 'longText' si los historiales de sets son muy grandes.
            if (!Schema::hasColumn('routines', 'notes')) {
                $table->json('notes')->nullable()->after('exercise_ids');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversión simple (esto no revierte la migración de datos, solo el esquema)
        // Puedes omitir esto si no planeas hacer rollback.
    }
};
