<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos para crear usuarios con roles específicos.
     */
    public function run(): void
    {
        // 1. Crear 5 Entrenadores (Trainers)
        // Usamos el estado trainer() definido en el UserFactory
        User::factory()->count(15)->trainer()->create();
        $this->command->info('Se han creado 15 Entrenadores (role: trainer).');

        // 2. Crear 3 Nutricionistas (Nutritionists)
        // Usamos el estado nutritionist() definido en el UserFactory
        // User::factory()->count(3)->nutritionist()->create();
        // $this->command->info('Se han creado 3 Nutricionistas (role: nutritionist).');

        // 3. Crear 50 Clientes (Clients)
        // Usamos el estado client() definido en el UserFactory
        User::factory()->count(50)->client()->create();
        $this->command->info('Se han creado 50 Clientes (role: client).');

        
    }
}