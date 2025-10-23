<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // El campo JSON es esencial para guardar múltiples sets con diferentes reps/kg.
        Schema::table('routine_exercises', function (Blueprint $table) {
            $table->json('sets_details')
                  ->after('weight_target')
                  ->nullable()
                  ->comment('Almacena los detalles de cada set (reps y kg) como un array JSON.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routine_exercises', function (Blueprint $table) {
            $table->dropColumn('sets_details');
        });
    }
};
