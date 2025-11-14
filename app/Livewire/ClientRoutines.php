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
    // LÓGICA DE MODAL GLOBAL
    // ------------------------------------------------------------------
    
    protected $listeners = [
        'executeAction' => 'handleGlobalAction', // Captura el evento de ejecución
    ];

    public function handleGlobalAction(string $action, array $params = []): void
    {
        // Verifica si el método ($action, ej. 'removeSet') existe en esta clase
        if (method_exists($this, $action)) {
            
            // ¡Magia! Llama a la función cuyo nombre está en la variable $action,
            // pasándole el array de parámetros $params.
            call_user_func_array([$this, $action], $params);
            
        } else {
            Log::warning("Acción global no implementada: $action");
        }
    }

    /////////////////////

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

            $data = [
                'title' => '¿Estás seguro de que deseas eliminar la rutina?',
                'message' => 'Esto eliminara la rutina: -' . $routine->name . '- Esta acción es IRREVERSIBLE y también eliminará todos sus ejercicios asociados.',
                
                'confirmAction' => 'deleteRoutine', 
                
                'cancelAction' => 'closeModal',
                
                'confirmButtonText' => 'Sí, Eliminar',
                'confirmButtonClass' => 'btn-outline-red',
                'buttonClass' => 'btn-outline-lime',
                
                'params' => [
                    $routineId,
                ]
            ];
        
            // 5. Envía el evento al modal global
            $this->dispatch('openConfirmModal', data: $data);

        } else {
            // session()->flash('error', 'Rutina no encontrada.');
            $this->dispatch('notify', message: 'Rutina no encontrada.', type: 'error', duration: 3500 );
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
    public function deleteRoutine(int $routineId): void
    {
        // Si no hay ID de rutina para eliminar, simplemente cerramos y salimos.
        if (is_null($routineId)) {
            $this->closeModal();
            return;
        }
        
        $routine = Routine::where('id', $routineId)
                         ->where('user_id', Auth::id()) 
                         ->first();

        if ($routine) {
            $routine->delete();

            // Recargar la lista de rutinas para actualizar la vista
            $this->mount();
            $this->dispatch('notify', message: 'La rutina '. $routine->name . ' ha sido eliminada correctamente.', type: 'success', duration: 3500 );

        } else {
            $this->dispatch('notify', message: 'Rutina no encontrada o no tienes permiso para eliminarla.', type: 'error', duration: 3500 );

        }
        
        // Cerramos el modal después de la operación
        $this->closeModal();
    }

    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        $userId = Auth::id();

        $selfMadeRoutines = Routine::where('user_id', $userId)->where('creator_id', $userId)->get();
        $assignedRoutines = Routine::where('user_id', $userId)->where('creator_id', '!=', $userId)->get();

        return view('livewire.client-routines', [
            'selfMadeRoutines' => $selfMadeRoutines,
            'assignedRoutines' => $assignedRoutines,
        ]);
    }
}