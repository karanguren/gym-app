<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Routine;
use Illuminate\Support\Facades\Auth;

class RoutineTemplatesManager extends Component
{
    // Propiedades para la lógica de edición (si la necesitas)
    // public $routineIdToEdit = null; 

    /**
     * Propiedad computada para obtener las plantillas del entrenador actual.
     */
    #[Computed]
    public function routineTemplates()
    {
        // Trae las rutinas que son plantillas (is_template = true)
        // y que fueron creadas por el entrenador actual (creator_id = Auth::id())
        return Routine::where('is_template', true)
            ->where('creator_id', Auth::id())
            ->orderBy('name')
            ->get();
    }
    
    // --- LÓGICA DE ACCIÓN ---
    
    /**
     * Define la acción a ejecutar cuando se presiona el botón "Usar como Base" (Editar/Clonar)
     */
    public function editTemplate(int $routineId): void
    {
        // Redirige al formulario de edición/creación, llevando el ID de la plantilla
        // para que pueda ser cargada o clonada.
        // Asegúrate de que esta ruta existe:
        $this->redirect(route('employee.routines.edit', $routineId), navigate: true);
    }
    
    // --- RENDER ---
    
    public function render()
    {
        return view('livewire.employee.routine-templates-manager')
            ->title('Mis Plantillas de Rutina');
    }
}
