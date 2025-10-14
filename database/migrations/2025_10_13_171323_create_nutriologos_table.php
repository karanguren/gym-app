<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nutriologos', function (Blueprint $table) {
            $table->id();

            // CLAVE FORÁNEA: Relaciona con la tabla de usuarios
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Datos específicos del perfil del Nutriólogo
            $table->string('license_number')->unique()->nullable()->comment('Número de licencia profesional de Nutrición');
            $table->string('specialty')->nullable()->comment('Área de experiencia (ej: deportiva, clínica, bariátrica)');
            $table->decimal('consultation_fee', 8, 2)->nullable()->comment('Tarifa por consulta');
            $table->text('bio')->nullable()->comment('Breve descripción profesional');
            $table->boolean('is_active')->default(true)->comment('Estado de disponibilidad del nutriólogo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutriologos');
    }
};
