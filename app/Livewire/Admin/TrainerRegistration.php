<?php

use Livewire\Volt\Component;
use App\Models\User; // Asegúrate de que este namespace sea correcto
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

// El componente Volt contiene la lógica y la vista Blade.
new class extends Component
{
    // Propiedades del formulario
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    // Define las reglas de validación
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // La contraseña debe ser confirmada y seguir las reglas de seguridad por defecto de Laravel
            'password' => ['required', 'string', 'confirmed', Password::defaults()], 
        ];
    }

    /**
     * Procesa el formulario para registrar un nuevo entrenador.
     */
    public function submit()
    {
        $this->validate();

        try {
            // 1. Crear el nuevo usuario
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => 'empleado', // ¡ASIGNACIÓN CRUCIAL! Fija el rol de Staff/Entrenador
            ]);

            // 2. Limpiar el formulario y notificar éxito al front-end
            $this->reset();
            $this->dispatch('trainer-registered'); 

        } catch (\Exception $e) {
            // Manejo de errores
            // Muestra un error si la creación falla (ej: error de DB)
            session()->flash('error', 'Ocurrió un error al registrar: Intente de nuevo.');
        }
    }
}; ?>


