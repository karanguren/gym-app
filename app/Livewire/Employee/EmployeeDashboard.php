<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect; // CLAVE: Importar Redirect para la redirección
use App\Models\User; // Asegurarse de importar el modelo User

class EmployeeDashboard extends Component
{
    /**
     * @var User|null $user Almacena el objeto del usuario autenticado.
     */
    public ?User $user = null;

    // Propiedad para almacenar el rol del usuario (trainer o nutriologo)
    public string $userRole = '';

    /**
     * El método mount se ejecuta antes de que se renderice el componente.
     * Aquí se implementa la lógica de verificación de perfil y redirección.
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function mount()
    {
        // Asignar el usuario a la propiedad de la clase
        $this->user = Auth::user();
        
        if (!$this->user) {
            Auth::logout();
            return redirect()->route('login');
        }

        // 1. Determinar el rol y verificar que es un rol permitido
        if ($this->user->isTrainer()) {
            $this->userRole = 'trainer';
        } elseif ($this->user->isNutriologo()) {
            $this->userRole = 'nutriologo';
        } else {
            // Fallback de seguridad si el usuario no tiene un rol de empleado válido
            Auth::logout();
            return redirect()->route('login');
        }
        
        // 2. 🎯 LÓGICA DE REDIRECCIÓN: Verificar si el perfil está completo
        // Se asume que el método hasEmployeeProfile() está implementado en el modelo User
        if (!$this->user->hasEmployeeProfile()) {
            // Si el perfil está incompleto (no hay registro en trainers o nutriologos),
            // redirigimos al formulario de configuración.
            return Redirect::route('employee.profile.setup');
        }
    }

    /**
     * Renderiza el componente y la vista asociada.
     */
    public function render()
    {
        // Pasamos el rol a la vista para personalizar el contenido
        return view('livewire.employee.dashboard', [
            'employeeRole' => $this->userRole === 'trainer' ? 'Entrenador' : 'Nutriólogo',
        ])->layout('components.layouts.app', ['title' => 'Dashboard de Empleado']);
    }
}
