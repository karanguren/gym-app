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
        Schema::create('workout_logs', function (Blueprint $table) {
            $table->id();
            
            // Relación con el usuario (cliente) que hizo el entrenamiento
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Relación con la rutina que se completó
            $table->foreignId('routine_id')->constrained('routines')->onDelete('cascade');
            
            // Duración total del entrenamiento en segundos
            $table->integer('duration_seconds')->default(0);

            // Marca de tiempo de cuándo se completó
            $table->timestamp('completed_at')->nullable();
            
            // Detalles del progreso (series, repeticiones, peso) por ejercicio, almacenado como JSON
            $table->json('details'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_logs');
    }
};
