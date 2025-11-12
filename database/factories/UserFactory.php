<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * El nombre del modelo correspondiente.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('123456789'),
            'remember_token' => Str::random(10),
            'role' => 'cliente', // Rol por defecto, si no se especifica otro
        ];
    }

    /**
     * Indica que el usuario es un entrenador.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
     */
    public function trainer(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'trainer',
        ]);
    }

    /**
     * Indica que el usuario es un nutricionista.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
     */
    public function nutritionist(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'nutriologo',
        ]);
    }

    /**
     * Indica que el usuario es un cliente (ya es el default, pero lo dejamos por claridad).
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
     */
    public function client(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'cliente',
        ]);
    }
}