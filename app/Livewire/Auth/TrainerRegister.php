<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Trainer; // 🎯 Importar el modelo Trainer
use App\Models\Nutriologo; // 🎯 Importar el modelo Nutriologo
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Livewire\Component;

class TrainerRegister extends Component
{
    public string $name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = ''; // Propiedad para capturar el rol seleccionado

    /**
     * Define las reglas de validación para el formulario.
     */
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
            // Aseguramos que el rol sea uno de los dos permitidos
            'role' => ['required', 'in:trainer,nutriologo'], 
        ];
    }

    /**
     * Procesa el registro del nuevo miembro del staff.
     */
    public function register()
    {
        $validatedData = $this->validate();

        // 1. Crear el usuario en la tabla 'users'
        $user = User::create([
            'name' => $validatedData['name'],
            'last_name' => $validatedData['last_name'], // Asegúrate de incluir el last_name
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $this->role, 
        ]);

        // 2. 🎯 Crear el perfil específico basado en el rol
        if ($this->role === 'trainer') {
            // Crea un registro en la tabla 'trainers'
            Trainer::create(['user_id' => $user->id]);
        } elseif ($this->role === 'nutriologo') {
            // Crea un registro en la tabla 'nutriologos'
            Nutriologo::create(['user_id' => $user->id]);
        }

        // 3. Autenticar al nuevo miembro del staff
        auth()->login($user);

        // 4. Redirigir al dashboard
        $this->redirect(route('trainer.onboarding', absolute: false), navigate: true);
    }

    /**
     * Renderiza la vista del componente de registro.
     */
    public function render()
    {
        return view('livewire.auth.trainer-register')
            ->layout('layouts.guest', ['title' => 'Registro de Staff']); // Ajusté el título para ser más genérico
    }
}
