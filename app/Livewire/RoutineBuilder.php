<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed; // 🎯 NUEVA IMPORTACIÓN
use App\Models\Exercise;
use App\Models\Routine; // Importar el nuevo modelo Routine
use Illuminate\Support\Facades\Auth;

class RoutineBuilder extends Component
{
    // 🎯 CORRECCIÓN: Definimos el layout usando el nombre de archivo real (layouts.app)
    // y lo hacemos público para que Livewire lo utilice.
    public string $layout = 'layouts.app'; 
    
    /**
     * La propiedad protegida $allExercises se elimina y se reemplaza por el método Computed.
     */
    // protected $allExercises; // <-- ELIMINADO
    
    // Propiedades para la lógica de la rutina
    public $selectedRoutineIds = [];
    public $currentView = 'selector'; // 'selector' o 'routine'
    public $showModal = false;
    public $selectedExerciseDetails = null;

    // Propiedad para el guardado de la rutina
    public $routineName = '';

    /**
     * Define los grupos musculares agrupados por tren.
     * La hacemos pública para que sea accesible en la vista Blade.
     */
    public array $muscleGroupsMap = [
        'Tren Superior' => ['pecho', 'espalda', 'hombros', 'biceps', 'triceps'],
        'Tren Inferior' => ['piernas', 'gluteos', 'gemelos', 'femorales'],
    ];

    /**
     * Propiedad Computada: Carga y agrupa los ejercicios.
     * Esto previene el error 'Call to a member function get() on null' al garantizar que el método
     * de carga se ejecuta solo cuando se necesita y está cacheado.
     */
    #[Computed]
    public function allExercises()
    {
        return Exercise::orderBy('muscle_group')->get()->groupBy('muscle_group');
    }

    /**
     * Reglas de validación para guardar la rutina.
     */
    protected function rules()
    {
        return [
            'routineName' => 'required|string|min:3|max:100',
            // Aseguramos que haya al menos un ejercicio seleccionado.
            'selectedRoutineIds' => 'required|array|min:1', 
        ];
    }
    
    /**
     * Inicializa el componente.
     */
    public function mount()
    {
        // La carga de ejercicios se ha movido al método #[Computed] allExercises().
    }

    /**
     * Lógica para seleccionar/deseleccionar un ejercicio.
     */
    public function toggleExercise($exerciseId)
    {
        $id = (int) $exerciseId;

        if (in_array($id, $this->selectedRoutineIds)) {
            $this->selectedRoutineIds = array_diff($this->selectedRoutineIds, [$id]);
        } else {
            $this->selectedRoutineIds[] = $id;
        }
        $this->selectedRoutineIds = array_values($this->selectedRoutineIds);
    }

    /**
     * Cambia entre la vista del selector de ejercicios y la vista de la rutina.
     */
    public function changeView($view)
    {
        if ($view === 'routine' && empty($this->selectedRoutineIds)) {
            session()->flash('error', 'Debes seleccionar al menos un ejercicio para armar la rutina.');
            return;
        }
        $this->currentView = $view;
    }

    /**
     * Muestra el modal de instrucciones del ejercicio.
     */
    public function showExerciseDetails($exerciseId)
    {
        // 🎯 CORRECCIÓN: Eliminamos la lógica de recarga, ya que la propiedad computada
        // garantiza que los datos estén disponibles cuando se acceden desde el render.
        
        $exercise = Exercise::find($exerciseId);
        if ($exercise) {
            $this->selectedExerciseDetails = $exercise;
            $this->showModal = true;
        }
    }

    /**
     * Cierra el modal de instrucciones.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedExerciseDetails = null;
    }

    /**
     * Guarda la rutina seleccionada en la base de datos.
     */
    public function saveRoutine()
    {
        // 1. Validar nombre y que haya ejercicios seleccionados
        $this->validate();

        try {
            // 2. Crear el registro de la rutina
            Routine::create([
                'user_id' => Auth::id(),
                'name' => $this->routineName,
                // exercise_ids se guardará como JSON gracias al casting en el modelo
                'exercise_ids' => $this->selectedRoutineIds,
                'notes' => 'Rutina creada por el cliente a través del Constructor de Rutinas.',
            ]);

            // 3. Limpiar el estado y notificar éxito
            $this->reset(['routineName', 'selectedRoutineIds']);
            $this->currentView = 'selector'; // Volver al selector
            session()->flash('success', '¡Rutina guardada exitosamente! Puedes consultarla en tu Dashboard.');

        } catch (\Exception $e) {
            // Manejar error de base de datos
            session()->flash('error', 'Hubo un error al guardar la rutina. Intenta de nuevo. (Detalles: ' . $e->getMessage() . ')');
        }
    }


    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        // Carga los detalles de los ejercicios seleccionados para la vista 'routine'
        $selectedExercisesDetails = $this->currentView === 'routine' && !empty($this->selectedRoutineIds)
            ? Exercise::whereIn('id', $this->selectedRoutineIds)->get()
            : collect();

        // Accedemos a la Propiedad Computada como si fuera una propiedad normal ($this->allExercises)
        return view('livewire.routine-builder', [
            'exercises' => $this->allExercises, // Acceso a la Propiedad Computada
            'selectedExercisesDetails' => $selectedExercisesDetails,
        ])->title('Arma tu Rutina'); 
    }
}
