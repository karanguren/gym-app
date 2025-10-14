<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Necesario para la conversión

return new class extends Migration
{
    public function up(): void
    {
        // 🚨 ANTES DE dropColumn, asegúrate de que el rol 'empleado' ya no exista.
        // Si tienes datos, ejecuta esto manualmente:
        // DB::table('users')->where('role', 'empleado')->update(['role' => 'trainer']);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role'); 
        });

        Schema::table('users', function (Blueprint $table) {
            // Se añaden los 4 roles definitivos
            $table->enum('role', [
                'administrador', 
                'trainer', 
                'nutriologo', 
                'cliente'
            ])->default('cliente')->after('email');
        });
    }

    public function down(): void
    {
        // ... (código de rollback)
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->string('role')->default('cliente')->after('email');
        });
    }
};