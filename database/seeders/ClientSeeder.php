<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ClientProfile;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Ejecuta las semillas de la base de datos.
     */
    // public function run(): void
    // {
    //     // 1. Definimos el ID del entrenador que se asignará de forma fija
    //     $fixedTrainerId = 7;
        
    //     // Obtenemos los IDs de los usuarios que YA TIENEN un perfil
    //     $existingProfileUserIds = ClientProfile::pluck('user_id');

    //     // 2. Obtener los Usuarios Clientes existentes (role: 'cliente')
    //     // EXCLUIMOS a los usuarios cuyos IDs ya están en la colección $existingProfileUserIds
    //     $clientUsers = User::where('role', 'cliente')
    //                         ->whereNotIn('id', $existingProfileUserIds) // <--- CAMBIO CLAVE: Excluir IDs existentes
    //                         ->take(11) 
    //                         ->get();
        
    //     $count = $clientUsers->count();

    //     if ($count === 0) {
    //         $this->command->warn('No se encontraron Usuarios Clientes sin un perfil existente.');
    //         return;
    //     }

    //     $this->command->info("Creando exactamente {$count} perfiles (ClientProfile) para Usuarios Clientes que NO TENÍAN perfil...");

    //     // 3. Iterar sobre cada usuario cliente y crear su perfil
    //     $clientUsers->each(function (User $client, $index) use ($fixedTrainerId) {
            
    //         // Alternamos el estado de asignación: asignado o pendiente
    //         $status = ($index % 2 === 0) ? 'accepted' : 'pending';

    //         // Crear el perfil asociado al usuario
    //         ClientProfile::factory()->create([
    //             'user_id' => $client->id,
                
    //             'assigned_trainer_id' => $fixedTrainerId,
    //             'requested_trainer_id' => $fixedTrainerId,
                
    //             'assignment_status' => $status,
    //         ]);
    //     });

    //     $this->command->info("Proceso completado. Se han creado {$count} Perfiles de Cliente (ClientProfile) y todos apuntan al entrenador ID {$fixedTrainerId}.");
    // }
}