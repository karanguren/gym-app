<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            // ID del usuario (cliente) que creó la rutina
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Nombre descriptivo que el usuario le da a la rutina (Ej: "Rutina de Lunes - Pecho y Biceps")
            $table->string('name'); 
            
            // Guarda los IDs de los ejercicios seleccionados, serializados como JSON.
            // Ejemplo: [1, 5, 12, 8]
            $table->json('exercise_ids'); 
            
            // Un campo opcional para notas o descripción de la rutina
            $table->text('notes')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('routines');
    }
};
