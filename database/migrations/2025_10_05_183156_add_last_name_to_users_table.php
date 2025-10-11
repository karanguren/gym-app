<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Añadimos la columna 'last_name' después de 'name'
            $table->string('last_name')->after('name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminamos la columna si hacemos rollback
            $table->dropColumn('last_name');
        });
    }
};
