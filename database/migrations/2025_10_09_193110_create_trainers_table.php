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
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();

            // Clave foránea al usuario, asegurando que se elimine el perfil si el usuario se borra.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Datos específicos del perfil del entrenador
            $table->string('certification_id')->unique()->nullable()->comment('Número de licencia o certificación profesional');
            $table->string('specialty')->nullable()->comment('Área de experiencia (ej: nutrición, fuerza, yoga)');
            $table->decimal('hourly_rate', 8, 2)->nullable()->comment('Tarifa por hora');
            $table->text('bio')->nullable()->comment('Breve descripción profesional');
            $table->boolean('is_active')->default(true)->comment('Estado de disponibilidad del entrenador');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
