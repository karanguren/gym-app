<?php

namespace App\Livewire;

use App\Models\Routine;
use Livewire\Component;
use App\Models\WorkoutLog;
use App\Models\RoutineExercise; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoutineWorkout extends Component
{
    // ------------------------------------------------------------------
    // PROPIEDADES PÚBLICAS
    // ------------------------------------------------------------------
    public Routine $routine;

    // Propiedad para almacenar el progreso de la sesión de entrenamiento
    public array $workoutData = []; 

    // Propiedades del cronómetro
    public $isRunning = false; // El estado clave para controlar el cronómetro
    public $seconds = 0; // Almacena el tiempo total en segundos
    // NOTA: La propiedad $timer ha sido eliminada.

    // ------------------------------------------------------------------
    // CICLO DE VIDA
    // ------------------------------------------------------------------

    public function mount(Routine $routine)
    {
        $this->routine = $routine;

        if (Auth::id() !== $routine->user_id) {
            abort(403, 'No puedes acceder a esta rutina.');
        }

        // Inicializa workoutData a partir de la planificación (RoutineExercises)
        $initialData = [];
        
        foreach ($routine->routineExercises as $re) {
            
            // Si sets_details es nulo, usa los campos target como fallback (un solo set)
            $details = $re->sets_details 
                ? $re->sets_details
                : [ // Fallback: 1 set
                    [
                        'reps' => $re->target_reps ?? 10, 
                        'kg' => $re->target_weight ?? 0.0
                    ]
                ];
                
            $initialData[$re->id] = $this->prepareInitialSets($details);
        }
        
        $this->workoutData = $initialData;
    }

    private function prepareInitialSets(array $setsDetails): array
    {
        $preparedSets = [];
        foreach ($setsDetails as $set) {
            $preparedSets[] = [
                'target_reps'   => $set['reps'] ?? 10,
                'target_kg'     => $set['kg'] ?? 0.0,
                'done'          => false,
                'result_reps'   => $set['reps'] ?? 10,
                'result_kg'     => $set['kg'] ?? 0.0,
            ];
        }
        return $preparedSets;
    }

    // ------------------------------------------------------------------
    // LÓGICA DEL CRONÓMETRO
    // ------------------------------------------------------------------

    /**
     * Inicia el cronómetro. Livewire ahora usará $isRunning en la vista para el polling.
     */
    public function startTimer(): void
    {
        if (!$this->isRunning) {
            $this->isRunning = true;
            session()->flash('success', '¡Entrenamiento iniciado!');
        }
    }

    /**
     * Pausa el cronómetro.
     */
    public function pauseTimer(): void
    {
        if ($this->isRunning) {
            $this->isRunning = false;
            session()->flash('info', 'Entrenamiento en pausa.');
        }
    }

    /**
     * Este método se llama cada segundo (por el wire:poll).
     */
    public function updateTimer(): void
    {
       if ($this->isRunning) {
        // Asumiendo que has refactorizado $seconds a $elapsedSeconds
        // para coincidir con tus otras propiedades, sino usa $this->seconds++
        $this->seconds++;
    }
    }

    /**
     * Formatea los segundos a H:M:S para la vista.
     */
    public function getFormattedTimeProperty(): string
    {
        $h = floor($this->seconds / 3600);
        $m = floor(($this->seconds % 3600) / 60);
        $s = $this->seconds % 60;

        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
    
    // ------------------------------------------------------------------
    // LÓGICA DE GESTIÓN DE SETS (Se mantiene igual)
    // ------------------------------------------------------------------

    public function toggleSetCompleted(int $routineExerciseId, int $setIndex): void
    {
        $set = &$this->workoutData[$routineExerciseId][$setIndex];
        
        $set['done'] = !$set['done'];
        
        if ($set['done']) {
            session()->flash('success', '¡Set completado!');
        } else {
            session()->flash('info', 'Set marcado como incompleto.');
        }
    }
    
    public function removeSet(int $routineExerciseId, int $setIndex): void
    {
        if (isset($this->workoutData[$routineExerciseId][$setIndex])) {
            unset($this->workoutData[$routineExerciseId][$setIndex]);
            $this->workoutData[$routineExerciseId] = array_values($this->workoutData[$routineExerciseId]);
            session()->flash('warning', 'Set eliminado de la sesión.');
        }
    }

    public function addSet(int $routineExerciseId): void
    {
        $currentSets = $this->workoutData[$routineExerciseId] ?? [];
        $lastSet = end($currentSets); 

        $newSet = [
            'target_reps'   => $lastSet['target_reps'] ?? 10,
            'target_kg'     => $lastSet['target_kg'] ?? 0.0,
            'done'          => false,
            'result_reps'   => $lastSet['target_reps'] ?? 10,
            'result_kg'     => $lastSet['target_kg'] ?? 0.0,
        ];

        $this->workoutData[$routineExerciseId][] = $newSet;
        session()->flash('info', 'Set extra agregado. No olvides rellenar los datos.');
    }
    
    public function finishWorkout(): void
    {
        $this->pauseTimer(); 

        Log::info('Entrenamiento Finalizado:', [
            'routine_id' => $this->routine->id,
            'duration' => $this->seconds,
            'progress' => $this->workoutData,
        ]);
        
        session()->flash('success', "Entrenamiento de '{$this->routine->name}' finalizado. Duración total: {$this->formattedTime}.");
        
        $this->redirect(route('client.routines'), navigate: true);
    }

    // ------------------------------------------------------------------
    // RENDER
    // ------------------------------------------------------------------
    public function render()
    {
        return view('livewire.routine-workout')->title('Entrenamiento: ' . $this->routine->name);
    }
}