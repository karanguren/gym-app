<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect; 
use App\Models\User; 

class EmployeeDashboard extends Component
{
    /**
     * @var User|null $user Almacena el objeto del usuario autenticado.
     */
    public ?User $user = null;

    // Propiedad para almacenar el rol determinado: 'trainer', 'nutriologo', o vacío
    public string $userRole = '';

    public function mount()
    {
        $this->user = Auth::user();
        
        // 1. Verificación de Autenticación
        if (!$this->user) {
            Auth::logout();
            return redirect()->route('login');
        }

        // 2. Determinar el rol y verificar que sea un rol permitido (Entrenador o Nutriólogo)
        if ($this->user->isTrainer()) {
            $this->userRole = 'trainer';
        } elseif ($this->user->isNutriologo()) {
            $this->userRole = 'nutriologo';
        } else {
            // Si el usuario tiene un rol que no es de empleado, lo desloguea.
            Auth::logout();
            return redirect()->route('login');
        }
        
        // 3. 🎯 LÓGICA DE REDIRECCIÓN: Verificar si el perfil está completo
        // Se asume que hasEmployeeProfile() está implementado en el modelo User
        if (!$this->user->hasEmployeeProfile()) {
            return Redirect::route('employee.profile.setup');
        }
    }

    /**
     * Renderiza la vista del dashboard y le pasa los datos para la carga condicional.
     */
    public function render()
    {
        // Pasamos el usuario y el rol a la vista para que pueda cargar el sub-componente
        return view('livewire.employee.dashboard', [
            'user' => $this->user,
            'userRole' => $this->userRole,
        ])->layout('components.layouts.app', ['title' => 'Dashboard de Empleado']);
    }
}
