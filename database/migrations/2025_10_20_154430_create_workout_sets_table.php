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
        Schema::create('workout_sets', function (Blueprint $table) {
            $table->id();
            
            // Claves foráneas (FK)
            $table->foreignId('workout_log_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('exercise_id')->constrained(); 

            // Datos del Set REALIZADO
            $table->unsignedSmallInteger('set_number');
            // 💡 CAMBIO: Usamos decimal(8, 2) en lugar de unsignedDecimal
            $table->decimal('weight', 8, 2)->nullable()->comment('Peso real levantado (Ej: 95.5).'); 
            $table->unsignedSmallInteger('reps')->comment('Repeticiones reales completadas.');
            $table->text('notes')->nullable(); 

            $table->index(['workout_log_id', 'exercise_id', 'set_number']); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workout_sets');
    }
};
