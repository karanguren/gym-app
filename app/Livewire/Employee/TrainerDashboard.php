<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Routine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

// Asumiendo que esta es la ruta final del dashboard del empleado
#[Layout('layouts.app')] 
class TrainerDashboard extends Component
{
    // Clientes asignados al entrenador
    public Collection $assignedClients;
    
    // Plantillas de rutinas creadas por el entrenador
    public Collection $routineTemplates;

    public function mount(): void
    {
        $trainerId = Auth::id();

        // 1. Cargar Clientes Asignados
        $this->assignedClients = User::select('users.id', 'users.name')
            ->join('client_profiles', 'users.id', '=', 'client_profiles.user_id')
            ->where('client_profiles.assigned_trainer_id', $trainerId)
            ->orderBy('users.name')
            ->get();

        // 2. Cargar Plantillas de Rutina
        $this->routineTemplates = Routine::where('creator_id', $trainerId)
            ->where('is_template', true)
            // ->whereNull('user_id')
            ->orderByDesc('created_at')
            ->get();
    }

    // --- Métodos de Navegación Actualizados ---
    
    /**
     * Redirige a la página para crear una rutina exclusiva para un cliente.
     * Ruta: employee.routines.create-for-client
     */
    public function createRoutineForClient(int $userId): void
    {
        // Ruta corregida: 'employee.routines.create-for-client'
        $this->redirect(route('employee.routines.create-for-client', ['userId' => $userId]), navigate: true);
    }

    /**
     * Redirige a la página de edición de una plantilla o rutina.
     * Ruta: employee.routines.edit
     */
    public function editTemplate(int $routineId): void
    {
        // Ruta corregida: 'employee.routines.edit'
        $this->redirect(route('employee.routines.edit', ['routineId' => $routineId]), navigate: true);
    }

    /**
     * Redirige a la página de creación de una plantilla general.
     * Ruta: employee.routines.create
     */
    public function createTemplate(): void
    {
        // Ruta corregida: 'employee.routines.create'
        $this->redirect(route('employee.routines.create'), navigate: true);
    }
    
    // ------------------------------------------

    public function render()
    {
        return view('livewire.employee.trainer-dashboard')
            ->title('Dashboard de Entrenador');
    }
}