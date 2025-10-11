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
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Datos Públicos
            $table->string('last_name');
            
            // Datos Privados Requeridos
            $table->string('id_number')->unique(); // cédula
            $table->float('height');              // estatura
            $table->float('weight');              // peso
            $table->string('address');            // dirección
            $table->string('emergency_contact');
            $table->string('personal_number');

            // Opcional
            $table->string('profile_photo_path')->nullable();
            
            // CLAVE: Estado de Verificación
            $table->boolean('is_verified')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
    
};
