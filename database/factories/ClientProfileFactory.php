<?php

namespace Database\Factories;

use App\Models\ClientProfile; // Usamos el nombre de modelo ClientProfile
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientProfileFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente.
     *
     * @var string
     */
    protected $model = ClientProfile::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Los campos de ID se establecerán en el seeder
            'user_id' => null, 
            'assigned_trainer_id' => null,
            'requested_trainer_id' => null,
            
            // Datos del perfil del cliente
            'assignment_status' => fake()->randomElement(['unassigned', 'pending', 'accepted', 'rejected']),
            'last_name' => fake()->lastName(),
            'id_number' => fake()->unique()->numerify('########'), // Número de identificación único
            'height' => fake()->randomFloat(2, 1.5, 2.1), // Altura en metros
            'weight' => fake()->randomFloat(2, 50, 120), // Peso en kg
            'address' => fake()->address(),
            'personal_number' => fake()->phoneNumber(),
            'profile_photo_path' => null, 
            'emergency_contact' => fake()->name() . ' ' . fake()->phoneNumber(),
            'is_verified' => 0, 
        ];
    }
}