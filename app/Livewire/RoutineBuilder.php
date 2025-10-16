<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Exercise;
use App\Models\Routine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str; // 🎯 Importar la clase Str para la lógica de búsqueda

class RoutineBuilder extends Component
{
    // --- PROPIEDADES PÚBLICAS ---
    public string $layout = 'layouts.app'; 
    public string $routineName = '';

    // 🎯 NUEVA PROPIEDAD: Para el campo de búsqueda en la vista
    public string $searchQuery = ''; 
    
    // Propiedad que almacena todos los ejercicios (cargada en mount)
    public ?Collection $exercises = null; 

    // Propiedad que se sincronizará con la propiedad computada selectedRoutineIds
    public array $selectedRoutineIds = [];

    /**
     * ESTRUCTURA CLAVE: Almacena los sets y reps/kg por ejercicio.
     */
    public array $routineData = []; 

    // Propiedades para la lógica de la interfaz
    public string $currentView = 'selector'; 
    public bool $showModal = false;
    public $selectedExerciseDetails = null; // Exercise model instance

    /**
     * Define los grupos musculares agrupados por tren para la interfaz del selector.
     */
    public array $muscleGroupsMap = [
        'Tren Superior' => ['pecho', 'espalda', 'hombros', 'biceps', 'triceps'],
        'Tren Inferior' => ['piernas', 'gluteos', 'gemelos', 'femorales'],
    ];

    /**
     * Carga los ejercicios una única vez al montar.
     */
    public function mount(): void
     {
        // Carga y agrupa todos los ejercicios por muscle_group.
        $this->exercises = Exercise::orderBy('muscle_group')
                                   ->get()
                                   ->groupBy('muscle_group')
                                   ->collect(); 
    }

    // --- PROPIEDADES COMPUTADAS ---

    /**
     * Propiedad Computada: Devuelve el array de ejercicios filtrados por $searchQuery.
     * Esta es la colección que la vista 'selector' debe recorrer.
     */
    #[Computed]
    public function filteredExercises(): Collection
    {
        $allExercises = $this->exercises ?? collect();

        if (empty($this->searchQuery)) {
            return $allExercises;
        }

        $search = Str::lower($this->searchQuery);
        $filteredExercises = collect();

        // Itera sobre los grupos (ej. 'pecho', 'espalda')
        foreach ($allExercises as $groupKey => $exercisesInGroup) {
            // Filtra los ejercicios dentro del grupo
            $filtered = $exercisesInGroup->filter(function ($exercise) use ($search) {
                // Comprueba si el nombre del ejercicio contiene la búsqueda
                return Str::contains(Str::lower($exercise->name), $search);
            });
            
            // Si el grupo filtrado no está vacío, lo añade al resultado.
            if ($filtered->isNotEmpty()) {
                $filteredExercises->put($groupKey, $filtered);
            }
        }

        return $filteredExercises;
    }


    /**
     * Propiedad Computada: Devuelve solo los IDs de los ejercicios seleccionados
     * (claves de $routineData). Usado para la vista y validación.
     * @return array
     */
    #[Computed]
    public function selectedRoutineIds(): array
    {
        // Obtiene los IDs de los ejercicios seleccionados
        return array_keys($this->routineData);
    }

    #[Computed]
    public function selectedExercisesDetails(): Collection
    {
        // Si la vista es 'selector' o no hay IDs, devolvemos una colección vacía para no hacer consultas innecesarias.
        if ($this->currentView === 'selector' || empty($this->selectedRoutineIds)) {
            return collect();
        }
        
        // Solo se ejecuta esta consulta si la vista es 'routine' y si $selectedRoutineIds ha cambiado.
        return Exercise::whereIn('id', $this->selectedRoutineIds)
            ->get()
            ->keyBy('id');
    }
    
    // --- REGLAS DE VALIDACIÓN ---

    protected function rules(): array
    {
        return [
            'routineName' => 'required|string|min:3|max:100',
            'routineData' => 'required|array|min:1', 
            'routineData.*.*.reps' => 'required|integer|min:1|max:500',
            'routineData.*.*.kg' => 'nullable|numeric|min:0|max:5000', 
        ];
    }
    
    // --- MÉTODOS DE INTERACCIÓN (sin cambios en su funcionalidad) ---

    public function toggleExercise(int $exerciseId): void
    {
        $id = $exerciseId;

        if (in_array($id, $this->selectedRoutineIds)) {
            // --- 1. DESELECCIONAR (Quitar) ---
            $this->selectedRoutineIds = array_values(array_diff($this->selectedRoutineIds, [$id]));
            unset($this->routineData[$id]);

        } else {
            // --- 2. SELECCIONAR (Añadir) ---
            $this->selectedRoutineIds[] = $id;

            if (!isset($this->routineData[$id])) {
                $this->routineData[$id] = [
                    ['reps' => 10, 'kg' => 0] 
                ];
            }
        }
    }

    public function addSet($exerciseId): void
    {
        $this->routineData[(int)$exerciseId][] = ['reps' => 10, 'kg' => 0];
    }

    public function removeSet($exerciseId, $setIndex): void
    {
        $exerciseId = (int)$exerciseId;
        
        if (isset($this->routineData[$exerciseId]) && isset($this->routineData[$exerciseId][$setIndex])) {
            unset($this->routineData[$exerciseId][$setIndex]);
            
            $this->routineData[$exerciseId] = array_values($this->routineData[$exerciseId]);
            
            if (empty($this->routineData[$exerciseId])) {
                unset($this->routineData[$exerciseId]);
            }
        }
    }

    public function changeView($view): void
    {
        if ($view === 'routine' && empty($this->selectedRoutineIds)) {
            session()->flash('error', 'Debes seleccionar al menos un ejercicio para armar la rutina.');
            return;
        }
        $this->currentView = $view;
    }

    public function showExerciseDetails($exerciseId): void
    {
        $exercise = Exercise::find($exerciseId);
        if ($exercise) {
            $this->selectedExerciseDetails = $exercise;
            $this->showModal = true;
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedExerciseDetails = null;
    }

    // --- GUARDAR RUTINA ---

    /**
     * Guarda la rutina, limpia el estado y redirige al dashboard.
     */
    public function saveRoutine(): void
    {
        $this->validate(); 

        try {
            $routine = Routine::create([
                'user_id' => Auth::id(),
                'name' => $this->routineName,
                'exercise_ids' => $this->routineData, 
                'notes' => 'Rutina creada por el cliente a través del Constructor de Rutinas.',
            ]);

            session()->flash('success', '¡Rutina "' . $routine->name . '" guardada exitosamente! Puedes consultarla en tu Dashboard.');
            
            $this->redirect(route('dashboard'), navigate: true); 

        } catch (\Exception $e) {
            session()->flash('error', 'Hubo un error al guardar la rutina. Intenta de nuevo. (Detalles: ' . $e->getMessage() . ')');
        }
    }

    /**
     * Renderiza la vista del componente.
     */
     public function render()
    {
        // Carga los detalles de los ejercicios seleccionados
        return view('livewire.routine-builder', [
            // 🎯 CAMBIO: Pasamos la propiedad COMPUTADA 'filteredExercises' a la vista
            'exercises' => $this->filteredExercises, 
            'selectedExercisesDetails' => $this->selectedExercisesDetails, 
        ])->title('Arma tu Rutina'); 
    }
}