<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routine_exercises', function (Blueprint $table) {
            $table->id();
            
            // Claves foráneas (FK)
            $table->foreignId('routine_id')->constrained()->onDelete('cascade');
            $table->foreignId('exercise_id')->constrained()->onDelete('cascade'); 

            // Parámetros SUGERIDOS para este ejercicio
            $table->unsignedSmallInteger('order')->default(1)->comment('Orden de ejecución del ejercicio.');
            $table->unsignedSmallInteger('sets_target')->default(3)->comment('Número de sets sugeridos.');
            $table->string('reps_target', 50)->nullable()->comment('Reps sugeridas (Ej: 8-12, AMRAP).');
            // 💡 OPCIONAL: Si quieres una sugerencia de peso
            // $table->decimal('weight_suggestion', 8, 2)->nullable()->comment('Peso sugerido.'); // Usar DECIMAL

            $table->unique(['routine_id', 'exercise_id']); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routine_exercises');
    }
};
