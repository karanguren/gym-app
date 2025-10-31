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
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->enum('assignment_status', [
                'unassigned', 
                'pending',    
                'accepted',   
                'rejected'    
            ])->default('unassigned')->after('assigned_trainer_id'); 
        });

        DB::table('client_profiles')
            ->whereNotNull('assigned_trainer_id')
            ->update(['assignment_status' => 'accepted']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropColumn('assignment_status');
        });
    }
};
