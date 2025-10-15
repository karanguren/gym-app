<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Routine; // Asegúrate de que esta importación sea correcta

class ClientRoutines extends Component
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $routines;

    /**
     * Monta el componente y carga las rutinas del usuario.
     */
    public function mount()
    {
        // Inicializar como colección vacía para seguridad, como ya tenías.
        $this->routines = collect(); 

        // 💡 CORRECCIÓN DE CONTEXTO: Usamos un bloque condicional explícito.
        if (Auth::check() && Auth::user()->role === 'cliente') {
            $userId = Auth::id();

            // 🎯 Cambio aquí: Usamos el método query() para asegurarnos 
            // de que estamos empezando desde cero con un Query Builder 
            // y no una Collection maltratada.
            $this->routines = Routine::where('user_id', $userId)
                                    //  ->with('exercises') // Necesario para el conteo manual en Blade
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        } 
    }

    /**
     * Placeholder para la función de ver detalles de una rutina.
     * @param int $routineId
     */
    public function viewRoutineDetails($routineId)
    {
        // En una implementación real, esto abriría un modal o redirigiría.
        session()->flash('info', "Función para ver los detalles de la Rutina #{$routineId} (Próxima Implementación).");
    }

    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        return view('livewire.client-routines');
    }
}
