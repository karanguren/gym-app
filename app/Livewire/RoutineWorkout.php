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

    // Propiedades para el modal de INSTRUCCIONES
    public bool $showInstructionsModal = false;
    public ?array $selectedExerciseDetails = null; // Almacena nombre e instrucciones

    // Propiedades para el modal de CONFIRMACIÓN DE ELIMINACIÓN
    public bool $showDeleteConfirmationModal = false;
    public ?int $setRoutineExerciseId = null;
    public ?int $setIndexToDelete = null;

    // ------------------------------------------------------------------
    // CICLO DE VIDA
    // ------------------------------------------------------------------

    public function mount(Routine $routine)
    {
        $this->routine = $routine;

        if (Auth::id() !== $routine->user_id) {
            abort(403, 'No puedes acceder a esta rutina.');
        }

        // Aseguramos que la relación 'routineExercises' esté cargada con 'exercise'
        $this->routine->load('routineExercises.exercise');
        
        // Inicializa workoutData a partir de la planificación (RoutineExercises)
        $initialData = [];
        
        foreach ($routine->routineExercises as $re) {
            
            // CRÍTICO: Manejar si $re->sets_details ya es un array (por Eloquent Casting) o si es una cadena JSON.
            $setsDetails = $re->sets_details;
            $details = null;

            if (is_array($setsDetails)) {
                // Caso 1: El casting de Eloquent está activo, ya es un array.
                $details = $setsDetails;
            } elseif (is_string($setsDetails) && !empty($setsDetails)) {
                // Caso 2: El casting no está activo, es una cadena JSON que debemos decodificar.
                // Manejo de errores: Si el JSON es inválido, $details será null.
                $details = json_decode($setsDetails, true);
            }
                
            // Aplicar el fallback si $details es nulo o no un array válido
            if (empty($details) || !is_array($details)) {
                $details = [ // Fallback: 1 set
                    [
                        'reps' => $re->target_reps ?? 10, 
                        'kg' => $re->target_weight ?? 0.0
                    ]
                ];
            }
                
            // $re->id es el RoutineExercise ID, usado como clave para $workoutData
            $initialData[$re->id] = $this->prepareInitialSets($details);
        }
        
        $this->workoutData = $initialData;
    }

    private function prepareInitialSets(array $setsDetails): array
    {
        $preparedSets = [];
        foreach ($setsDetails as $set) {
            // Aseguramos que los resultados iniciales sean iguales a los objetivos
            $targetReps = $set['reps'] ?? 10;
            $targetKg = $set['kg'] ?? 0.0;
            
            $preparedSets[] = [
                'target_reps' 	=> $targetReps,
                'target_kg' 	=> $targetKg,
                'done' 			=> false,
                'result_reps' 	=> $targetReps, // Se inicializa con el target
                'result_kg' 	=> $targetKg, 	// Se inicializa con el target
            ];
        }
        return $preparedSets;
    }
    
    // ------------------------------------------------------------------
    // PROPIEDADES COMPUTADAS
    // ------------------------------------------------------------------
    
    /**
     * Obtiene la colección de RoutineExercise, incluyendo el modelo Exercise.
     */
    #[Computed]
    public function routineExercises()
    {
        // Retorna la relación ya cargada en mount().
        // Si no se llamó a mount() o si la rutina no está cargada, asegura la carga.
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
                    return false; // Encontró al menos un set incompleto
                }
            }
        }
        
        return true; // Todos los sets están completos
    }


    // ------------------------------------------------------------------
    // LÓGICA DEL CRONÓMETRO 
    // ------------------------------------------------------------------

    public function startTimer(): void
    {
        if (!$this->isRunning) {
            $this->isRunning = true;
            $this->dispatch('show-toast', ['message' => '¡Entrenamiento iniciado!', 'type' => 'success']);
        }
    }

    public function pauseTimer(): void
    {
        if ($this->isRunning) {
            $this->isRunning = false;
            $this->dispatch('show-toast', ['message' => 'Entrenamiento en pausa.', 'type' => 'info']);
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
    
    public function showInstructions(int $routineExerciseId): void
    {
        // Usamos la propiedad computada para buscar el ejercicio
        $re = $this->routineExercises->firstWhere('id', $routineExerciseId);

        if ($re && $re->exercise) {
            $this->selectedExerciseDetails = [
                'name' => $re->exercise->name,
                'instructions' => $re->exercise->instructions ?? $re->exercise->description ?? 'No hay instrucciones disponibles para este ejercicio.', 
            ];
            $this->showInstructionsModal = true;
        }
    }

    public function closeInstructionsModal(): void
    {
        $this->showInstructionsModal = false;
        $this->selectedExerciseDetails = null;
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
            $this->dispatch('show-toast', ['message' => '¡Set completado!', 'type' => 'success']);
        } else {
            $this->dispatch('show-toast', ['message' => 'Set marcado como incompleto.', 'type' => 'info']);
        }

        // Reevaluar la propiedad computada tras el cambio
        $this->isEverySetCompleted();
    }
    
    public function confirmRemoveSet(int $routineExerciseId, int $setIndex): void
    {
        $this->setRoutineExerciseId = $routineExerciseId;
        $this->setIndexToDelete = $setIndex;
        $this->showDeleteConfirmationModal = true;
    }

    public function cancelRemoveSet(): void
    {
        $this->showDeleteConfirmationModal = false;
        $this->setRoutineExerciseId = null;
        $this->setIndexToDelete = null;
    }

    public function removeSet(): void 
    {
        if ($this->setRoutineExerciseId === null || $this->setIndexToDelete === null) {
            $this->cancelRemoveSet();
            return;
        }
        
        $routineExerciseId = $this->setRoutineExerciseId;
        $setIndex = $this->setIndexToDelete;
        
        if (isset($this->workoutData[$routineExerciseId][$setIndex])) {
            // Aseguramos que no se elimine el último set
            if (count($this->workoutData[$routineExerciseId]) <= 1) {
                $this->dispatch('show-toast', ['message' => 'Debes mantener al menos un set por ejercicio.', 'type' => 'error']);
                $this->cancelRemoveSet();
                return;
            }

            unset($this->workoutData[$routineExerciseId][$setIndex]);
            // Reindexar el array para evitar problemas con Livewire
            $this->workoutData[$routineExerciseId] = array_values($this->workoutData[$routineExerciseId]);
            $this->dispatch('show-toast', ['message' => 'Set eliminado de la sesión.', 'type' => 'info']);
        }
        
        $this->cancelRemoveSet(); // Cerrar el modal y limpiar propiedades
        $this->isEverySetCompleted(); // Reevaluar el estado
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
        $this->dispatch('show-toast', ['message' => 'Set extra agregado. No olvides rellenar los datos.', 'type' => 'info']);
        $this->isEverySetCompleted(); // Reevaluar el estado
    }
    
    public function finishWorkout(): void
    {
        // ------------------------------------------------------------------
        // RESTRICCIÓN AGREGADA
        // ------------------------------------------------------------------
        if (!$this->isEverySetCompleted) {
            $this->dispatch('show-toast', [
                'message' => '¡Faltan sets por completar! Marca todos los sets con el ícono de check para finalizar.', 
                'type' => 'error'
            ]);
            // Detenemos la ejecución
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
                // Esto no debería suceder si isEverySetCompleted es true, pero es un buen fallback.
                session()->flash('warning', 'Entrenamiento finalizado. No se registró ningún set completado.');
                $this->redirect(route('client.routines'), navigate: true);
                return;
            }

            session()->flash('success', "¡Entrenamiento de '{$this->routine->name}' registrado y guardado con éxito! Duración: {$this->formattedTime}.");
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
        // CRÍTICO: Pasamos la propiedad computada explícitamente a la vista
        return view('livewire.routine-workout', [
            'routineExercises' => $this->routineExercises, 
        ])->title('Entrenamiento: ' . $this->routine->name);
    }
}
