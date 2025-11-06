<?php

namespace App\Livewire;

use App\Models\Routine;
use Livewire\Component;
use App\Models\WorkoutLog;
use App\Models\RoutineExercise; 
use App\Models\WorkoutSet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed; 
use App\Models\Exercise;

class RoutineWorkout extends Component
{
    // ------------------------------------------------------------------
    // PROPIEDADES PÚBLICAS
    // ------------------------------------------------------------------
    public Routine $routine;

    // Propiedad para almacenar el progreso de la sesión de entrenamiento
    public array $workoutData = []; 

    // Propiedades del cronómetro
    public $isRunning = false; 
    public $seconds = 0;
    
    // Propiedades para el modal de CONFIRMACIÓN DE ELIMINACIÓN
    public bool $showDeleteConfirmationModal = false;
    public ?int $setRoutineExerciseId = null;
    public ?int $setIndexToDelete = null;
    
    // Propiedades para el modal de FINALIZACION
    public bool $showFinalizeModal = false;

    // Propiedades para el modal de INSTRUCCIONES
    public bool $showModal = false;
    public $selectedExerciseDetails = null;

    // ------------------------------------------------------------------
    // CICLO DE VIDA
    // ------------------------------------------------------------------

    public function mount(Routine $routine)
    {
        $this->routine = $routine;

        if (Auth::id() !== $routine->user_id) {
            abort(403, 'No puedes acceder a esta rutina.');
        }

        $this->routine->load('routineExercises.exercise');
        
        $initialData = [];
        
        foreach ($routine->routineExercises as $re) {
            
            $setsDetails = $re->sets_details;
            $details = null;

            if (is_array($setsDetails)) {
                $details = $setsDetails;
            } elseif (is_string($setsDetails) && !empty($setsDetails)) {
                $details = json_decode($setsDetails, true);
            }
                
            if (empty($details) || !is_array($details)) {
                $details = [ 
                    [
                        'reps' => $re->target_reps ?? 10, 
                        'kg' => $re->target_weight ?? 0.0
                    ]
                ];
            }
                
            $initialData[$re->id] = $this->prepareInitialSets($details);
        }
        
        $this->workoutData = $initialData;
    }

    private function prepareInitialSets(array $setsDetails): array
    {
        $preparedSets = [];
        foreach ($setsDetails as $set) {
            $targetReps = $set['reps'] ?? 10;
            $targetKg = $set['kg'] ?? 0.0;
            
            $preparedSets[] = [
                'target_reps' 	=> $targetReps,
                'target_kg' 	=> $targetKg,
                'done' 			=> false,
                'result_reps' 	=> $targetReps, 
                'result_kg' 	=> $targetKg, 	
            ];
        }
        return $preparedSets;
    }
    
    #[Computed]
    public function routineExercises()
    {
        if (!$this->routine->relationLoaded('routineExercises')) {
            $this->routine->load('routineExercises.exercise');
        }

        return $this->routine->routineExercises;
    }

    /**
     * Devuelve true si TODOS los sets de TODOS los ejercicios están marcados como 'done'.
     *
     * @return bool
     */
    #[Computed]
    public function isEverySetCompleted(): bool
    {
        if (empty($this->workoutData)) {
            return false;
        }

        foreach ($this->workoutData as $exerciseSets) {
            foreach ($exerciseSets as $set) {
                if (!$set['done']) {
                    return false; 
                }
            }
        }
        
        return true; 
    }


    // ------------------------------------------------------------------
    // LÓGICA DEL CRONÓMETRO 
    // ------------------------------------------------------------------

    public function startTimer(): void
    {
        if (!$this->isRunning) {
            $this->isRunning = true;
            $this->dispatch('notify', message: '¡Entrenamiento iniciado!', type: 'success', duration: 3500 );
    
        }
    }

    public function pauseTimer(): void
    {
        if ($this->isRunning) {
            $this->isRunning = false;
            $this->dispatch('notify', message: 'Entrenamiento en pausa.', type: 'info', duration: 3500 );
        }
    }

    /**
     * Llamado por wire:poll cada segundo.
     */
    public function updateTimer(): void
    {
       if ($this->isRunning) {
           $this->seconds++;
       }
    }

    /**
     * Propiedad computada para el tiempo formateado.
     */
    #[Computed]
    public function formattedTime(): string
    {
        $h = floor($this->seconds / 3600);
        $m = floor(($this->seconds % 3600) / 60);
        $s = $this->seconds % 60;

        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
    
    // ------------------------------------------------------------------
    // LÓGICA DE MODALES DE EJERCICIO 
    // ------------------------------------------------------------------
    
   public function showExerciseDetails(int $exerciseId): void
    {
        $exercise = Exercise::find($exerciseId);
        
        if ($exercise) {
            $this->selectedExerciseDetails = $exercise;
            $this->showModal = true;
        } 
    }

    public function closeInstructionsModal(): void
    {
        $this->showModal = false;
        $this->selectedExerciseDetails = null;
    }

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

    // ------------------------------------------------------------------
    // LÓGICA DE MODAL DE FINALIZACIÓN (NUEVOS MÉTODOS)
    // ------------------------------------------------------------------
    
    // public function openFinalizeModal(): void
    // {
    //     // Solo abrimos si el requisito de finalización se cumple (opcional, pero útil)
    //     if ($this->isEverySetCompleted) {
    //         $this->showFinalizeModal = true;
    //     } else {
    //         // Esto solo se dispara si alguien quita el disabled del botón en el HTML.
    //         $this->dispatch('notify', message: '¡Faltan sets por completar! Marca todos los sets con el ícono de check para finalizar.', type: 'error', duration: 3500 );
    //     }
    // }

    

    public function openFinalizeModal(): void
    {
        if ($this->isEverySetCompleted) {
            $data = [
                'title' => 'Finalizar Entrenamiento',
                'message' => '¿Estás seguro de que quieres finalizar este entrenamiento? Se registrará tu progreso y el tiempo total.',
                
                // 2. Define la acción de confirmación (el nombre del método)
                'confirmAction' => 'finishWorkout', // ¡Este es el método grande!
                
                'cancelAction' => 'doNothing', 
                'confirmButtonText' => 'Confirmar',
                'confirmButtonClass' => 'btn-outline-ve',
                'buttonClass' => 'btn-outline-ro',
            ];
            
            $this->dispatch('openConfirmModal', data: $data);
        }
    }

    public function closeFinalizeModal(): void
    {
        $this->showFinalizeModal = false;
    }

    // ------------------------------------------------------------------
    // LÓGICA DE GESTIÓN DE SETS
    // ------------------------------------------------------------------

    public function toggleSetCompleted(int $routineExerciseId, int $setIndex): void
    {
        // Usamos & para modificar el array directamente
        $set = &$this->workoutData[$routineExerciseId][$setIndex];
        
        // Saneamiento de datos
        $set['result_reps'] = max(0, (int) $set['result_reps']);
        $set['result_kg'] = max(0.0, (float) $set['result_kg']);

        $set['done'] = !$set['done'];
        
        if ($set['done']) {
            $this->dispatch('notify', message: '¡Set completado!', type: 'success', duration: 3500 );
        } else {
            $this->dispatch('notify', message: 'Set marcado como incompleto.', type: 'info', duration: 3500 );
        }

        // Reevaluar la propiedad computada tras el cambio
        $this->isEverySetCompleted();
    }
    
    // public function confirmRemoveSet(int $routineExerciseId, int $setIndex): void
    // {
    //     $this->setRoutineExerciseId = $routineExerciseId;
    //     $this->setIndexToDelete = $setIndex;
    //     $this->showDeleteConfirmationModal = true;
    // }

    public function confirmRemoveSet(int $routineExerciseId, int $setIndex): void
    {
        // 1. Prepara los datos que necesita el modal
        $data = [
            'title' => 'Confirmar Eliminación',
            'message' => '¿Estás seguro de que deseas eliminar permanentemente el Set #' . ($setIndex + 1) . '?',
            
            // 2. Define la acción de confirmación (el nombre del método)
            'confirmAction' => 'removeSet', 
            
            // 3. Define la acción de cancelación (limpieza)
            'cancelAction' => 'cancelRemoveSet',
            
            'confirmButtonText' => 'Sí, Eliminar',
            'confirmButtonClass' => 'btn-outline-ro',
            'buttonClass' => 'btn-outline-ve',
            
            // 4. Parámetros que necesita 'removeSet' para funcionar
            'params' => [
                $routineExerciseId, // parámetro 1
                $setIndex           // parámetro 2
            ]
        ];
        
        // 5. Envía el evento al modal global
        $this->dispatch('openConfirmModal', data: $data);
    }

    public function cancelRemoveSet(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->setRoutineExerciseId = null;
        $this->setIndexToDelete = null;
    }

    public function removeSet(int $routineExerciseId, int $setIndex): void 
    {

        if (isset($this->workoutData[$routineExerciseId][$setIndex])) {
            if (count($this->workoutData[$routineExerciseId]) <= 1) {
                $this->dispatch('notify', message: 'Debes mantener al menos un set por ejercicio.', type: 'error', duration: 3500 );
                return;
            }

            unset($this->workoutData[$routineExerciseId][$setIndex]);
            $this->workoutData[$routineExerciseId] = array_values($this->workoutData[$routineExerciseId]);
            $this->dispatch('notify', message: 'Set eliminado de la sesión.', type: 'info', duration: 3500);
        }
        
        $this->setRoutineExerciseId = null;
        $this->setIndexToDelete = null; 
        
        $this->isEverySetCompleted(); 
    }

    public function addSet(int $routineExerciseId): void
    {
        $currentSets = $this->workoutData[$routineExerciseId] ?? [];
        
        // Usamos el target del último set o valores por defecto
        $lastSet = end($currentSets); 
        $lastTargetReps = $lastSet['target_reps'] ?? 10;
        $lastTargetKg = $lastSet['target_kg'] ?? 0.0;

        $newSet = [
            'target_reps' 	=> $lastTargetReps,
            'target_kg' 	=> $lastTargetKg,
            'done' 			=> false,
            'result_reps' 	=> $lastTargetReps,
            'result_kg' 	=> $lastTargetKg,
        ];

        $this->workoutData[$routineExerciseId][] = $newSet;
        $this->dispatch('notify', message: 'Set extra agregado. No olvides rellenar los datos.', type: 'info', duration: 3500 );
        $this->isEverySetCompleted(); // Reevaluar el estado
    }
    
    public function finishWorkout(): void
    {
        // ------------------------------------------------------------------
        // RESTRICCIÓN AGREGADA
        // ------------------------------------------------------------------
        if (!$this->isEverySetCompleted) {
            $this->dispatch('notify', message: '¡Faltan sets por completar! Marca todos los sets con el ícono de check para finalizar.', type: 'error', duration: 3500 );
            $this->showFinalizeModal = false; 
            return; 
        }

        // ------------------------------------------------------------------


        $this->pauseTimer();

        $hasCompletedSets = false;
        $exerciseProgressForLog = []; // Lo mantenemos por si quieres guardar el JSON de historial
        $errors = [];

        try {
            // 1. Crear el registro de entrenamiento (WorkoutLog) para obtener el ID
            $log = WorkoutLog::create([
                'user_id' => Auth::id(),
                'routine_id' => $this->routine->id,
                'duration_seconds' => $this->seconds,
                'completed_at' => now(), // Aseguramos que completed_at se registre
                // El campo 'details' se llenará después de registrar los sets, si es necesario.
            ]);
            
            // 2. Iterar sobre los datos y crear los WorkoutSets (persistiendo el detalle)
            foreach ($this->workoutData as $reId => $sets) {
                // Usamos la propiedad computada
                $routineExercise = $this->routineExercises->firstWhere('id', $reId);
                
                if (!$routineExercise) {
                    $errors[] = "RoutineExercise ID $reId no encontrado.";
                    continue;
                }

                $nextTargetDetails = [];
                $setNumber = 1;

                foreach ($sets as $set) {
                    // Saneamiento de datos antes de guardar
                    $reps = max(0, (int) $set['result_reps']);
                    $kg = max(0.0, (float) $set['result_kg']);
                    $done = (bool) $set['done'];

                    if ($done) {
                        $hasCompletedSets = true;
                    }
                    
                    // CRÍTICO: 2.1 Guardar el set en la tabla WORKOUT_SETS
                    WorkoutSet::create([
                        'workout_log_id' => $log->id, // Vinculado al log
                        'exercise_id' => $routineExercise->exercise_id, // ID del ejercicio para el progreso
                        'set_number' => $setNumber++,
                        'weight' => $kg, // Mapeo de result_kg -> weight (columna de la BD)
                        'reps' => $reps, // Mapeo de result_reps -> reps (columna de la BD)
                        'notes' => null, // Dejamos nulo si no hay campo de notas
                        'completed' => $done, // Mapeo de done -> completed (columna de la BD)
                    ]);
                    
                    // 2.2 Preparar data para actualizar el plan (RoutineExercise)
                    $nextTargetDetails[] = [
                        'reps' => $reps,
                        'kg' => $kg,
                    ];
                    
                    // 2.3 Preparar data para el JSON de historial (si se quiere mantener)
                    $setsForLog[] = [
                        'reps' => $reps, 'kg' => $kg, 'done' => $done,
                    ];
                } // Fin del bucle de sets
                
                // 3. Actualizar sets_details de RoutineExercise (el plan) para la próxima sesión
                $routineExercise->update([
                    'sets_details' => json_encode($nextTargetDetails), // Guardar solo reps/kg como JSON
                ]);
                
                // Llenar el historial (JSON) para el log
                $exerciseProgressForLog[$reId] = [
                    'name' => $routineExercise->exercise->name ?? 'Ejercicio Desconocido',
                    'sets' => $setsForLog,
                ];

            } // Fin del bucle de RoutineExercise

            // 4. Actualizar el campo 'details' del WorkoutLog con el JSON de historial (opcional, pero buena práctica)
            $log->update([
                'details' => json_encode($exerciseProgressForLog), 
            ]);


            // 5. Validación final y redirección
            if (!empty($errors)) {
                Log::warning('Errores al actualizar RoutineExercises en finishWorkout: ' . implode(', ', $errors));
            }
            
            if (!$hasCompletedSets) {
                session()->flash('warning', 'Entrenamiento finalizado. No se registró ningún set completado.');
                $this->redirect(route('client.routines'), navigate: true);
                return;
            }

            $this->dispatch('notify', message: '¡Entrenamiento de ' . $this->routine->name . ' registrado y guardado con éxito! Duración: ' . $this->formattedTime . '.', type: 'success', duration: 3500 );
            
            $this->showFinalizeModal = false;

            $this->redirect(route('client.routines'), navigate: true);

        } catch (\Exception $e) {
            Log::error('Error al guardar el log de entrenamiento o los sets: ' . $e->getMessage());
            session()->flash('error', 'Error al guardar el registro. Intenta de nuevo. (Detalles: ' . $e->getMessage() . ')');
            $this->redirect(route('client.routines'), navigate: true);
        }
    }

    // ------------------------------------------------------------------
    // RENDER
    // ------------------------------------------------------------------
    public function render()
    {
        return view('livewire.routine-workout', ['routineExercises' => $this->routineExercises,])->title('Entrenamiento: ' . $this->routine->name);
    }
}
