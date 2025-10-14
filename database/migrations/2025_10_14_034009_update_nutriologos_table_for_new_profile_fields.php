<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Añade los nuevos campos y renombra los existentes para Nutriologos.
     */
    public function up(): void
    {
        Schema::table('nutriologos', function (Blueprint $table) {
            
            // 1. Añadir Nuevos Campos de Datos Personales (Cédula, Contactos, Dirección)
            // Se coloca después de user_id
            $table->string('id_number')->unique()->after('user_id')->comment('Cédula de identidad personal (única)'); 
            $table->string('address')->after('id_number');
            $table->string('personal_contact')->after('address');
            $table->string('emergency_contact')->after('personal_contact');

            // 2. Renombrar 'bio' a 'personal_description'
            // Esto es crucial para coincidir con la nueva lógica del formulario.
            $table->renameColumn('bio', 'personal_description');
            
            // 3. Añadir Nuevos Campos Profesionales (Experiencia y Rutas de Documentos)
            $table->text('work_experience')->nullable()->after('personal_description')->comment('Descripción de la experiencia laboral');
            
            // 4. Campo para las rutas de los documentos de licencia/cédula profesional (JSON)
            $table->json('certification_paths')->nullable()->after('work_experience')->comment('Rutas de los archivos de licencias/diplomas');
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('nutriologos', function (Blueprint $table) {
            
            // 1. Revertir el cambio de nombre de 'personal_description' a 'bio'
            $table->renameColumn('personal_description', 'bio');

            // 2. Eliminar los campos añadidos
            $table->dropColumn([
                'id_number',
                'address',
                'personal_contact',
                'emergency_contact',
                'work_experience',
                'certification_paths',
            ]);
        });
    }
};
