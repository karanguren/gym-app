<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClientProfile; // ¡Importante! Usamos ClientProfile
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    public function run(): void
    {
        // 1. Definimos el ID del entrenador que se asignará de forma fija
        $fixedTrainerId = 7;
        
        // Verificamos si el entrenador con ID 7 existe (opcional, pero buena práctica)
        $trainerExists = User::where('id', $fixedTrainerId)->where('role', 'trainer')->exists();

        // 2. Obtener los Usuarios Clientes existentes (role: 'cliente')
        // LIMITAMOS la colección a solo los primeros 11 registros usando take(11)
        $clientUsers = User::where('role', 'cliente')
                            ->take(11) // <--- CAMBIO 1: Limitar a 11
                            ->get();
        
        $count = $clientUsers->count();

        if ($count === 0) {
            $this->command->warn('No se encontraron Usuarios Clientes existentes (role: "cliente"). No se crearon perfiles.');
            return;
        }

        $this->command->info("Creando exactamente {$count} perfiles (ClientProfile) para Usuarios Clientes existentes...");

        // 3. Iterar sobre cada usuario cliente y crear su perfil
        $clientUsers->each(function (User $client, $index) use ($fixedTrainerId) {
            
            // Alternamos el estado de asignación: asignado o pendiente
            $status = ($index % 2 === 0) ? 'accepted' : 'pending';

            // Crear el perfil asociado al usuario
            ClientProfile::factory()->create([
                'user_id' => $client->id,
                
                // <--- CAMBIO 2: Valores fijos establecidos en 7
                'assigned_trainer_id' => $fixedTrainerId,
                'requested_trainer_id' => $fixedTrainerId,
                
                'assignment_status' => $status,
            ]);
        });

        $this->command->info("Proceso completado. Se han creado {$count} Perfiles de Cliente (ClientProfile) y todos apuntan al entrenador ID {$fixedTrainerId}.");
    }
}