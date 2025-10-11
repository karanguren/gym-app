<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class TrainerRegister extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Define las reglas de validación para el formulario.
     */
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Procesa el registro del nuevo entrenador.
     */
    public function register()
    {
        $validatedData = $this->validate();

        // Crear el usuario con rol 'trainer'
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'trainer', // Asignar el rol específico de entrenador
        ]);

        // Autenticar al nuevo entrenador
        auth()->login($user);

        // Redirigir al dashboard de empleados/entrenadores
        return $this->redirect(route('dashboard'), navigate: true);
    }

    /**
     * Renderiza la vista del componente de registro.
     */
    public function render()
    {
        return view('livewire.auth.trainer-register')
            ->layout('layouts.guest', ['title' => 'Registro de Entrenador']);
    }
}
