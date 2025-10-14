<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     * Añade los nuevos campos y renombra los existentes.
     */
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            
            // 1. Añadir Nuevos Campos de Datos Personales
            // Cédula: Debe ser única.
            $table->string('id_number')->unique()->after('user_id'); 
            $table->string('address')->after('id_number');
            $table->string('personal_contact')->after('address');
            $table->string('emergency_contact')->after('personal_contact');

            // 2. Renombrar 'bio' a 'personal_description'
            // Esto es crucial para coincidir con la nueva lógica del formulario.
            // Nota: El método renameColumn puede requerir la instalación del paquete `doctrine/dbal` 
            // si usas versiones antiguas de Laravel o ciertas bases de datos.
            $table->renameColumn('bio', 'personal_description');
            
            // 3. Añadir Nuevos Campos Profesionales
            $table->text('work_experience')->nullable()->after('personal_description')->comment('Descripción de la experiencia laboral');
            
            // 4. Campo para las rutas de las certificaciones (JSON)
            $table->json('certification_paths')->nullable()->after('work_experience')->comment('Rutas de los archivos de certificación');
            
            // 5. Modificar el campo 'certification_id' para que no sea único si es necesario
            // Si la 'cédula' ya es única, el ID de certificación podría no serlo.
            // Si deseas mantenerlo como estaba, omite esta sección. La dejamos nullable.
            // $table->string('certification_id')->nullable()->change();

        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            
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
            
            // 3. Si modificaste la unicidad de 'certification_id', reviértela aquí
            // $table->string('certification_id')->unique()->nullable()->change();
        });
    }
};
