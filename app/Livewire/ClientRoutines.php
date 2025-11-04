<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Routine; 
use Illuminate\Support\Collection; // Necesario para inicializar $this->routines = collect();

class ClientRoutines extends Component
{
    /**
     * @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public $routines;

    // 🎯 PROPIEDADES PARA EL MODAL DE ELIMINACIÓN
    public bool $showDeleteModal = false;
    public ?int $routineToDeleteId = null;
    public string $routineToDeleteName = '';

    /**
     * Monta el componente y carga las rutinas del usuario.
     */
    public function mount()
    {
        // Inicializar como colección vacía para seguridad.
        $this->routines = collect(); 

        if (Auth::check() && Auth::user()->role === 'cliente') {
            $userId = Auth::id();

            $this->routines = Routine::where('creator_id', $userId)
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
        return $this->redirect(route('routine.workout', ['routine' => $routineId]), navigate: true);
    }

    // ------------------------------------------------------------------
    // LÓGICA DEL MODAL DE ELIMINACIÓN
    // ------------------------------------------------------------------

    /**
     * Abre el modal de confirmación y establece la rutina a eliminar.
     * Reemplaza la llamada directa a deleteRoutine en la vista.
     */
    public function confirmRoutineDeletion(int $routineId): void
    {
        $routine = Routine::find($routineId);
        if ($routine) {
            $this->routineToDeleteId = $routineId;
            $this->routineToDeleteName = $routine->name;
            $this->showDeleteModal = true;
        } else {
            session()->flash('error', 'Rutina no encontrada.');
        }
    }

    /**
     * Cierra el modal y restablece las propiedades de eliminación.
     */
    public function closeModal(): void
    {
        $this->showDeleteModal = false;
        $this->routineToDeleteId = null;
        $this->routineToDeleteName = '';
    }

    /**
     * Ejecuta la eliminación de la rutina después de la confirmación del modal.
     */
    public function deleteRoutine(): void
    {
        // Si no hay ID de rutina para eliminar, simplemente cerramos y salimos.
        if (is_null($this->routineToDeleteId)) {
            $this->closeModal();
            return;
        }
        
        $routineId = $this->routineToDeleteId;

        $routine = Routine::where('id', $routineId)
                         ->where('user_id', Auth::id()) 
                         ->first();

        if ($routine) {
            $routine->delete();

            // Recargar la lista de rutinas para actualizar la vista
            $this->mount();
            session()->flash('success', "La rutina '{$routine->name}' ha sido eliminada correctamente.");
        } else {
            session()->flash('error', 'Rutina no encontrada o no tienes permiso para eliminarla.');
        }
        
        // Cerramos el modal después de la operación
        $this->closeModal();
    }

    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        return view('livewire.client-routines');
    }
}