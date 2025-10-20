<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('email', 'admin@tudominio.com')->exists()) {
            User::create([
                'name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@tudominio.com',
                'password' => Hash::make('123456789'),
                'role' => 'administrador',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
