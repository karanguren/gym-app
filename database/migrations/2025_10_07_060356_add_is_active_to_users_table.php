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
        // Añade la columna 'is_active' a la tabla 'users'. 
        // Por defecto, se establece en true (activo).
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('password'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Elimina la columna 'is_active' si se revierte la migración.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
