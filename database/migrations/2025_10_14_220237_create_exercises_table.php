<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del ejercicio
            $table->enum('muscle_group', [
                'pecho', 'espalda', 'hombros', 'biceps', 'triceps',
                'piernas', 'gluteos', 'gemelos', 'femorales'
            ]); // Grupo muscular principal
            $table->text('description')->nullable(); // Descripción o instrucciones
            $table->string('image_path')->nullable(); // Imagen o miniatura
            $table->string('gif_path')->nullable(); // GIF demostrativo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
