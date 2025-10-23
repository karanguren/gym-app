<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Exercise;
use App\Models\Routine;
use App\Models\RoutineExercise; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RoutineBuilder extends Component
{
    // --- PROPIEDADES PÚBLICAS ---
    public string $layout = 'layouts.app'; 
    public string $routineName = '';

    public string $searchQuery = ''; 
    
    public ?Collection $exercises = null; 

    public array $selectedRoutineIds = [];

    /**
     * ESTRUCTURA CLAVE: Almacena los arrays de sets/reps/kg por ejercicio.
     * Ejemplo: [exercise_id => [ ['reps' => 12, 'kg' => 40], ['reps' => 10, 'kg' => 45] ]]
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
        $this->exercises = Exercise::orderBy('muscle_group')
                                       ->get()
                                       ->groupBy('muscle_group')
                                       ->collect(); 
        
        // Inicializa routineData si es necesario, por ejemplo, si se está editando
        // Si no es edición, se mantiene como array vacío.
    }

    // --- PROPIEDADES COMPUTADAS ---

    #[Computed]
    public function filteredExercises(): Collection
    {
        $allExercises = $this->exercises ?? collect();

        if (empty($this->searchQuery)) {
            return $allExercises;
        }

        $search = Str::lower($this->searchQuery);
        $filteredExercises = collect();

        foreach ($allExercises as $groupKey => $exercisesInGroup) {
            $filtered = $exercisesInGroup->filter(function ($exercise) use ($search) {
                return Str::contains(Str::lower($exercise->name), $search);
            });
            
            if ($filtered->isNotEmpty()) {
                $filteredExercises->put($groupKey, $filtered);
            }
        }

        return $filteredExercises;
    }


    #[Computed]
    public function selectedRoutineIds(): array
    {
        return array_keys($this->routineData);
    }

    #[Computed]
    public function selectedExercisesDetails(): Collection
    {
        if ($this->currentView === 'selector' || empty($this->selectedRoutineIds)) {
            return collect();
        }
        
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
            // Validamos que cada set tenga reps y kg válidos
            'routineData.*.*.reps' => 'required|integer|min:1|max:500', 
            'routineData.*.*.kg' => 'nullable|numeric|min:0|max:5000', 
        ];
    }
    
    // --- MÉTODOS DE INTERACCIÓN ---

    public function toggleExercise(int $exerciseId): void
    {
        $id = $exerciseId;

        if (isset($this->routineData[$id])) {
            unset($this->routineData[$id]);
        } else {
            // Inicializa con un set por defecto
            $this->routineData[$id] = [
                ['reps' => 10, 'kg' => 0.0] 
            ];
        }
        $this->selectedRoutineIds = array_keys($this->routineData);
    }

    public function addSet($exerciseId): void
    {
        // Usa el último set ingresado como valor por defecto para el nuevo set
        $lastSetIndex = count($this->routineData[(int)$exerciseId]) - 1;
        $lastSet = $this->routineData[(int)$exerciseId][$lastSetIndex];

        // Añade el nuevo set
        $this->routineData[(int)$exerciseId][] = [
            'reps' => $lastSet['reps'] - 2 > 0 ? $lastSet['reps'] - 2 : $lastSet['reps'], // Sugerencia de drop set
            'kg' => $lastSet['kg'] + 5.0, // Sugerencia de pirámide ascendente
        ];
    }

    public function removeSet($exerciseId, $setIndex): void
    {
        $exerciseId = (int)$exerciseId;
        
        if (isset($this->routineData[$exerciseId]) && isset($this->routineData[$exerciseId][$setIndex])) {
            unset($this->routineData[$exerciseId][$setIndex]);
            
            // Reindexar el array para evitar problemas en Livewire/Blade
            $this->routineData[$exerciseId] = array_values($this->routineData[$exerciseId]);
            
            // Si no quedan sets, elimina el ejercicio de la rutina
            if (empty($this->routineData[$exerciseId])) {
                unset($this->routineData[$exerciseId]);
                $this->selectedRoutineIds = array_keys($this->routineData);
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

    // --- GUARDAR RUTINA (LÓGICA ACTUALIZADA PARA SETS VARIABLES) ---

    /**
     * Guarda la rutina principal y sus ejercicios planificados en routine_exercises.
     * Ahora utiliza JSON para guardar los detalles individuales de cada set.
     */
    public function saveRoutine(): void
    {
        $this->validate(); 

        try {
            // 1. Crear el registro principal en la tabla `routines`
            $routine = Routine::create([
                'user_id' => Auth::id(),
                'name' => $this->routineName,
            ]);

            $routineExercisesData = [];
            $order = 1;

            // 2. Preparar los datos para la inserción en la tabla `routine_exercises`
            foreach ($this->routineData as $exerciseId => $sets) {
                
                $targetSets = count($sets); 
                $firstSet = $sets[0] ?? ['reps' => 10, 'kg' => 0.0];
                
                // *** CAMBIO CRUCIAL: SERIALIZACIÓN A JSON ***
                $setsDetailsJson = json_encode($sets);
                
                $routineExercisesData[] = [
                    'routine_id'    => $routine->id,
                    'exercise_id'   => $exerciseId,
                    'order'         => $order++,
                    // sets_target: Cuenta total de sets
                    'sets_target'   => $targetSets, 
                    // Estos dos campos se mantienen por convención o fallback (usan el primer set)
                    'reps_target'   => $firstSet['reps'], 
                    'weight_target' => $firstSet['kg'],
                    // *** NUEVO CAMPO: Detalles de cada set (incluye todas las reps/kg) ***
                    'sets_details'  => $setsDetailsJson,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            // 3. Inserción masiva del plan de ejercicios
            RoutineExercise::insert($routineExercisesData);

            session()->flash('success', '¡Rutina "' . $routine->name . '" guardada exitosamente! Puedes consultarla en tu Dashboard.');
            
            $this->redirect(route('client.routines'), navigate: true); 

        } catch (\Exception $e) {
            session()->flash('error', 'Hubo un error al guardar la rutina. Intenta de nuevo. (Detalles: ' . $e->getMessage() . ')');
            // Log::error($e->getMessage()); // Descomentar para debugging
        }
    }

    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        return view('livewire.routine-builder', [
            'exercises' => $this->filteredExercises, 
            'selectedExercisesDetails' => $this->selectedExercisesDetails, 
        ])->title('Arma tu Rutina'); 
    }
}
