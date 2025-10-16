<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            // Eliminar la restricción UNIQUE y hacerla nullable
            $table->string('certification_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            // Si deseas revertir, debes saber cómo era antes
            // $table->string('certification_id')->unique()->change();
        });
    }
};
