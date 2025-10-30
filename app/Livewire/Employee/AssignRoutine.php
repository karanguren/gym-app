<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Routine;
use App\Models\RoutineExercise;
use App\Models\User;
use App\Models\Exercise; // Asegúrate de importar el modelo Exercise
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignRoutine extends Component
{
    // --- PROPIEDADES PÚBLICAS Y STATE ---
    public ?Routine $currentRoutine = null;
    public ?User $targetUser = null; 
    public int $routineId = 0;
    
    // El ID del cliente que será asignado, debe ser !== 0 para que el botón de submit se habilite
    public int $userId = 0; 
    
    // Almacena los sets/reps/kg, replicando la estructura de RoutineBuilder
    public array $routineData = [];
    
    // Propiedades para el modal de detalles del ejercicio
    public bool $showModal = false;
    public ?Exercise $selectedExerciseDetails = null;

    // --- MÉTODOS DE INICIALIZACIÓN ---

    public function mount(int $routineId, int $userId = null): void
    {
        // 1. Cargar la rutina a asignar (incluyendo los detalles del ejercicio relacionado)
        $this->currentRoutine = Routine::with(['exercises' => function($query) {
            $query->orderBy('order')->with('exercise');
        }])->findOrFail($routineId);
        
        $this->routineId = $routineId;

        // 2. Cargar el usuario (si se está re-asignando o preseleccionando)
        if ($userId) {
            $this->targetUser = User::findOrFail($userId);
            $this->userId = $userId;
        }

        // 3. Inicializar routineData a partir de los datos de la rutina existente
        // CLAVE: Usamos 'exercises.exercise_id' como clave y decodificamos 'sets_details'
        $this->routineData = $this->currentRoutine->exercises->mapWithKeys(function ($routineExercise) {
            // Asumiendo que 'sets_details' está casteado a 'array' en RoutineExercise Model
            $setsDetails = $routineExercise->sets_details ?? []; 
            
            if (is_string($setsDetails)) {
                $setsDetails = json_decode($setsDetails, true) ?? [];
            }
            // Si no hay sets, inicializa con uno por defecto
            if (empty($setsDetails)) {
                $setsDetails = [['reps' => 10, 'kg' => 0.0]];
            }

            return [
                $routineExercise->exercise_id => $setsDetails
            ];
        })->toArray();
    }
    
    // --- LÓGICA DE MANIPULACIÓN DE SETS (REPLICADA) ---
    
    /**
     * Añade un nuevo set al final de un ejercicio.
     */
    public function addSet(int $exerciseId): void
    {
        // Obtiene la configuración del último set como base, o usa un default
        $lastSet = end($this->routineData[$exerciseId]);

        $newSet = [
            'reps' => $lastSet['reps'] ?? 10,
            'kg' => $lastSet['kg'] ?? 0.0,
        ];

        $this->routineData[$exerciseId][] = $newSet;
    }

    /**
     * Elimina un set por su índice.
     */
    public function removeSet(int $exerciseId, int $setIndex): void
    {
        if (count($this->routineData[$exerciseId]) > 1) {
            unset($this->routineData[$exerciseId][$setIndex]);
            
            // Reindexar el array para evitar problemas con Livewire
            $this->routineData[$exerciseId] = array_values($this->routineData[$exerciseId]);
        }
    }
    
    // --- LÓGICA DEL MODAL (REPLICADA) ---
    
    /**
     * Muestra los detalles de un ejercicio específico en un modal.
     */
    public function showExerciseDetails(int $exerciseId): void
    {
        // Busca el ejercicio dentro de la rutina cargada
        $routineExercise = $this->currentRoutine->exercises
            ->firstWhere('exercise_id', $exerciseId);
            
        if ($routineExercise && $routineExercise->exercise) {
            $this->selectedExerciseDetails = $routineExercise->exercise;
            $this->showModal = true;
        } else {
            session()->flash('error', 'Detalles del ejercicio no encontrados.');
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedExerciseDetails = null;
    }

    // --- REGLAS DE VALIDACIÓN Y ASIGNACIÓN (MANTENIDA) ---

    protected function rules(): array
    {
        return [
            'userId' => 'required|integer|exists:users,id|min:1', // userId > 0
            'routineData' => 'required|array|min:1', 
            'routineData.*.*.reps' => 'required|integer|min:1|max:500', 
            'routineData.*.*.kg' => 'nullable|numeric|min:0|max:5000', 
        ];
    }

    public function assignRoutine(): void
    {
        // Lógica de asignación idéntica a la versión anterior.
        if (!Auth::user() || !Auth::user()->isTrainer()) {
             session()->flash('error', 'No tienes permiso para realizar esta acción.');
             return;
        }

        $this->validate(); 

        try {
            // Aseguramos que el usuario objetivo esté cargado para el mensaje de éxito
            $this->targetUser = User::findOrFail($this->userId); 

            DB::beginTransaction();

            // 1. CLONAR la rutina principal para el usuario asignado
            $assignedRoutine = Routine::create([
                'user_id' => $this->userId, 
                'trainer_id' => Auth::id(), 
                'name' => $this->currentRoutine->name . ' - Asignada',
                'is_template' => false, 
            ]);

            $routineExercisesData = [];
            $order = 1;

            // 2. Preparar los datos para la inserción en la tabla `routine_exercises`
            foreach ($this->currentRoutine->exercises as $originalRoutineExercise) {
                $exerciseId = $originalRoutineExercise->exercise_id;
                
                // Usamos los datos PERSONALIZADOS del array routineData
                $sets = $this->routineData[$exerciseId] ?? [['reps' => 10, 'kg' => 0.0]];
                
                $targetSets = count($sets); 
                $firstSet = $sets[0];
                
                // *** LÓGICA REPLICADA: SERIALIZACIÓN A JSON ***
                $setsDetailsJson = json_encode($sets);
                
                $routineExercisesData[] = [
                    'routine_id'    => $assignedRoutine->id,
                    'exercise_id'   => $exerciseId,
                    'order'         => $order++,
                    'sets_target'   => $targetSets, 
                    'reps_target'   => $firstSet['reps'], 
                    'weight_target' => $firstSet['kg'],
                    'sets_details'  => $setsDetailsJson, // Campo crucial
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            RoutineExercise::insert($routineExercisesData);

            DB::commit();

            session()->flash('success', '¡Rutina "' . $assignedRoutine->name . '" asignada exitosamente al usuario ' . $this->targetUser->name . '!');
            
            $this->redirect(route('employee.dashboard'), navigate: true); 

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            // Re-lanzar para que Livewire maneje la validación
            throw $e; 
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al asignar rutina: ' . $e->getMessage()); 
            session()->flash('error', 'Hubo un error al asignar la rutina. Intenta de nuevo.');
        }
    }

    public function render()
    {
        // Lógica para obtener la lista de clientes (asumiendo que están disponibles)
        $clients = User::where('role', 'client')->get(); 
        
        return view('livewire.assign-routine', [
            'clients' => $clients,
        ])->title('Asignar Rutina'); 
    }
}
