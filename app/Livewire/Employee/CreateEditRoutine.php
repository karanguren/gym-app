<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed; 
use Livewire\Attributes\Validate; 
use App\Models\Routine;
use App\Models\Exercise; 
use App\Models\User;
use App\Models\RoutineExercise;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Validation\ValidationException; 
use Illuminate\Support\Collection; 

class CreateEditRoutine extends Component
{
    // --- ESTADO PRINCIPAL ---
    public ?Routine $routine = null; 
    
    // ID del cliente al que se asignará la rutina. 
    // Corresponde a 'user_id' en tu nuevo esquema si la rutina es exclusiva.
    public ?int $targetUserId = null; // Usado para la creación/edición de una rutina de cliente.

    // ID del cliente seleccionado en el dropdown (Solo si targetUserId es null y queremos asignar)
    public ?int $selectedClientId = null; 

    // Lista de clientes disponibles para asignación
    public array $availableClients = []; 
    
    // Datos del formulario
    #[Validate('required|string|min:5|max:100', as: 'Nombre de la Rutina')]
    public string $name = '';
    
    // Propiedad 'notes' (Nuevo en tu esquema)
    public ?string $notes = null;

    // Controla si la rutina es una plantilla general (true) o asignada/exclusiva (false)
    public bool $isTemplateMode = true; 
    
    // ... Otras propiedades ...
    public bool $showModal = false;
    public $selectedExerciseDetails = null;
    
    // Estructura de la rutina: Array de ejercicios (SECUECIAL)
    #[Validate('required|array|min:1', message: 'Debes añadir al menos un ejercicio a la rutina.', as: 'Ejercicios de la Rutina')]
    public array $routineData = [];
    
    public array $availableExercises = [];
    // public bool $showExerciseModal = false;
    // public ?Exercise $selectedExerciseDetails = null;

    // --- REGLAS DE VALIDACIÓN ---
    protected array $rules = [
        'name' => 'required|string|min:5|max:100',
        'notes' => 'nullable|string|max:500', // Nueva regla
        'routineData' => 'required|array|min:1',
        'routineData.*.exercise_id' => 'required|exists:exercises,id',
        'routineData.*.sets' => 'required|array|min:1',
        'routineData.*.sets.*.reps' => 'required|integer|min:1|max:999',
        'routineData.*.sets.*.kg' => 'nullable|numeric|min:0|max:999',
    ];


    // --- PROPIEDADES COMPUTADAS ---
    
    /**
     * Obtiene el modelo del usuario objetivo si estamos en modo de asignación.
     */
    #[Computed]
    public function targetUser(): ?User
    {
        // Usa targetUserId si está presente (creación directa) o selectedClientId (dropdown)
        $id = $this->targetUserId ?? $this->selectedClientId;
        return $id ? User::find($id) : null;
    }
    
    // --- MÉTODOS DE INICIALIZACIÓN ---

    public function mount(?int $routineId = null, ?int $userId = null): void
    {
        $trainerId = Auth::id();

        // 1. CARGA DE CLIENTES (CORREGIDA)
        $clients = User::select('users.id', 'users.name')
            ->join('client_profiles', 'users.id', '=', 'client_profiles.user_id')
            ->where('client_profiles.assigned_trainer_id', $trainerId)
            ->get();
        
        $this->availableClients = $clients->pluck('name', 'id')->toArray();
        
        // 2. Establecer el ID del cliente objetivo (fijado por URL)
        $this->targetUserId = $userId;
        
        // 3. Cargar rutina para edición
        if ($routineId) {
            // LÍNEA CORREGIDA: Usar 'user' en lugar de 'client'
            $this->routine = Routine::with('user')->find($routineId); 
            
            if (!$this->routine) {
                session()->flash('error', 'La rutina solicitada no fue encontrada.');
            }
        } else {
            $this->routine = null;
        }
        
        // 4. Determinar el modo y rellenar datos
        if ($this->routine && $this->routine->exists) {
            // Modo Edición
            $this->name = $this->routine->name;
            $this->notes = $this->routine->notes; // NUEVO
            $this->isTemplateMode = $this->routine->is_template; 
            
            // Si es una rutina de cliente, FIJA el targetUserId para mantener el contexto
            if ($this->routine->user_id) { 
                $this->targetUserId = $this->routine->user_id;
                $this->isTemplateMode = false; // Una rutina con user_id no es plantilla
                $this->selectedClientId = null; // Limpiar el dropdown de asignación
            } else {
                // Si es plantilla, habilita la opción de asignación por dropdown (selectedClientId)
                $this->isTemplateMode = true; 
                $this->targetUserId = null; // Limpiar el target fijo
                $this->selectedClientId = null; // Limpiar el dropdown de asignación
            }
            $this->loadRoutineDataForEditing();
        } else {
            // Modo Creación
            if ($this->targetUserId) {
                // CREACIÓN DE RUTINA EXCLUSIVA (Punto 2)
                $this->isTemplateMode = false; // No es plantilla
                $targetUser = $this->targetUser; 
                $userNameDisplay = $targetUser ? $targetUser->name : 'ID ' . $this->targetUserId;
                $this->name = 'Rutina Exclusiva para ' . $userNameDisplay;
            } else {
                // CREACIÓN DE PLANTILLA GENERAL
                $this->isTemplateMode = true;
                $this->name = 'Nueva Plantilla de Rutina';
                $this->selectedClientId = null;
            }
            
            if (empty($this->routineData)) {
                $this->addEmptyExercise();
            }
        }
        
        // 5. Cargar ejercicios disponibles
        $this->availableExercises = Exercise::select('id', 'name')->orderBy('name')->get()->toArray();
    }

    public function updatedRoutineData(string $key, $value): void
    {
        // Verificamos si la clave que cambió es un exercise_id
        if (str_contains($key, '.exercise_id')) {
            // Ejemplo de $key: routineData.0.exercise_id -> queremos obtener el índice '0'
            $parts = explode('.', $key);
            $index = $parts[1]; // Índice del ejercicio dentro de $routineData

            // Buscamos el nombre del ejercicio en el array $availableExercises
            $exercise = collect($this->availableExercises)->firstWhere('id', $value);

            if ($exercise) {
                // Actualizamos el nombre en el array para que se muestre en la vista (si se usa)
                $this->routineData[$index]['exercise_name'] = $exercise['name'];
            } else {
                // Si se selecciona la opción vacía, limpiamos el nombre
                $this->routineData[$index]['exercise_name'] = 'Selecciona un ejercicio';
            }
            
        }
        
        // Si se actualizan sets, podríamos querer re-validar ese campo
        if (str_contains($key, '.sets')) {
            $this->validateOnly($key);
        }
    }

    
    // --- LÓGICA DE GUARDADO REFACTORIZADA ---
    public function saveRoutine()
    {
        $rules = $this->rules;
        
        // 1. REGLAS DE VALIDACIÓN CONDICIONALES
        if ($this->isTemplateMode) {
            // Si es plantilla, no se requiere cliente.
        } else {
            // Modo Rutina Exclusiva: Requiere ID de cliente.
            if (!$this->targetUserId) {
                $rules['selectedClientId'] = 'required|integer|exists:users,id';
            }
        }

        $messages = [
            'routineData.*.exercise_id.required' => 'Debes seleccionar un ejercicio para cada elemento de la rutina.',
            'selectedClientId.required' => 'Debes seleccionar un cliente para asignar esta rutina.',
        ];

        try {
            $this->validate($rules, $messages);
        } catch (ValidationException $e) {
            $this->dispatch('toast-message', 
                title: 'Error de Formulario', 
                message: 'Por favor, revisa los campos marcados en rojo.', 
                type: 'error'
            );
            throw $e; 
        }

        DB::beginTransaction();
        try {
            $isEditing = $this->routine && $this->routine->exists;
            
            // --- CÁLCULO CRÍTICO de user_id y is_template ---
            
            $clientRoutineId = null;
            if (!$this->isTemplateMode) {
                // Si el toggle está en "Rutina Exclusiva", asignamos el ID del cliente.
                $clientRoutineId = $this->targetUserId ?? $this->selectedClientId;
            } 
            
            // La rutina es plantilla si el campo user_id es NULL.
            $finalIsTemplate = $clientRoutineId === null; 

            // ----------------------------------------------------

            // 2. Creación / Actualización de la Rutina
            $data = [
                'name' => $this->name,
                'notes' => $this->notes, 
                'user_id' => $clientRoutineId, 
                'is_template' => $finalIsTemplate, 
                'creator_id' => Auth::id(), 
            ];
            
            if ($isEditing) {
                // Modo Edición
                $this->routine->update($data);
                $routineModel = $this->routine;
                
                $flashTitle = $finalIsTemplate ? 'Plantilla Actualizada' : 'Rutina Actualizada';
                $flashMessage = 'La rutina fue actualizada con éxito.';

            } else {
                // Modo Creación
                $routineModel = Routine::create($data);
                $flashTitle = $finalIsTemplate ? 'Plantilla Creada' : 'Rutina Asignada';
                // Determina el nombre del cliente para el mensaje flash
                $assignedClientName = $this->targetUser ? 
                                        $this->targetUser->name : 
                                        ($this->selectedClientId ? $this->availableClients[$this->selectedClientId] : null);

                $flashMessage = $finalIsTemplate 
                    ? 'La nueva plantilla ha sido guardada.'
                    : 'La rutina exclusiva ha sido asignada a ' . $assignedClientName . '.';
            }

            // 3. Sincronización de Ejercicios
            RoutineExercise::where('routine_id', $routineModel->id)->delete();
            // ... (Lógica de inserción de RoutineExercise) ...
            
            $routineExercisesData = [];
            $order = 1;

            foreach ($this->routineData as $exerciseData) {
                $sets = $exerciseData['sets'];
                $firstSet = $sets[0]; 
                
                $setsDetailsJson = json_encode(array_map(function($set) {
                    $set['kg'] = is_numeric($set['kg']) ? (float)$set['kg'] : null;
                    return $set;
                }, $sets)); 
                
                $routineExercisesData[] = [
                    'routine_id' => $routineModel->id,
                    'exercise_id' => $exerciseData['exercise_id'],
                    'order' => $order++,
                    'sets_target' => count($sets), 
                    'reps_target' => $firstSet['reps'], 
                    'weight_target' => $firstSet['kg'] ?? 0, 
                    'sets_details' => $setsDetailsJson, 
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            RoutineExercise::insert($routineExercisesData);

            DB::commit();

            $this->dispatch('toast-message', title: $flashTitle, message: $flashMessage, type: 'success');
            
            // 4. Redirección
            $this->redirect(route('employee.dashboard'), navigate: true); 

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar/editar rutina: ' . $e->getMessage(), ['exception' => $e]); 
            $this->dispatch('toast-message', title: 'Error Fatal', message: 'Hubo un error inesperado al guardar la rutina.', type: 'error');
        }
    }
    
    protected function loadRoutineDataForEditing(): void
    {
        $this->routineData = [];
        
        // Asumiendo que el modelo Routine tiene una relación 'exercises' que devuelve RoutineExercise
        $routineExercises = $this->routine->routineExercises() // <-- CAMBIADO A routineExercises()
            ->with('exercise') // Ahora sí busca la relación 'exercise' en RoutineExercise
            ->orderBy('order')
            ->get();

        foreach ($routineExercises as $re) {
            $setsDetails = $re->sets_details; 
            
            $this->routineData[] = [
                'exercise_id' => $re->exercise_id,
                'exercise_name' => $re->exercise->name,
                'notes' => $re->notes, 
                'sets' => $setsDetails ?? [
                    ['reps' => $re->reps_target, 'kg' => $re->weight_target],
                ],
            ];
        }

        if (empty($this->routineData)) {
            $this->addEmptyExercise();
        }
    }

    /**
     * Añade una estructura de ejercicio vacía al array de la rutina.
     */
    public function addEmptyExercise(): void
    {
        // Esta es la estructura mínima requerida para un nuevo ejercicio
        $this->routineData[] = [
            'exercise_id' => null,
            'exercise_name' => 'Selecciona un ejercicio',
            'notes' => null,
            'sets' => [
                // El primer set por defecto
                ['reps' => 10, 'kg' => 2] 
            ]
        ];
    }

    /**
     * Elimina un ejercicio de la rutina por su índice.
     */
    public function removeExercise(int $index): void
    {
        // Si es el último ejercicio, simplemente lo limpiamos
        if (count($this->routineData) === 1) {
            $this->routineData[0] = [
                'exercise_id' => null,
                'exercise_name' => 'Selecciona un ejercicio',
                'notes' => null,
                'sets' => [['reps' => 10, 'kg' => 2]]
            ];
        } else {
            // Elimina el ejercicio del array
            unset($this->routineData[$index]);
            // Reindexa el array
            $this->routineData = array_values($this->routineData);
        }
        
        $this->validateOnly('routineData'); // Revalida el mínimo de 1 ejercicio
    }

    /**
     * Añade una serie (set) a un ejercicio específico.
     */
    public function addSet(int $exerciseIndex): void
    {
        // Clona el último set para mantener valores consistentes (ej. 10 reps, 50kg)
        $lastSet = end($this->routineData[$exerciseIndex]['sets']);
        
        $this->routineData[$exerciseIndex]['sets'][] = [
            'reps' => $lastSet['reps'] ?? 10,
            'kg' => $lastSet['kg'] ?? 2,
        ];
    }

    /**
     * Elimina una serie (set) de un ejercicio específico.
     */
    public function removeSet(int $exerciseIndex, int $setIndex): void
    {
        // Debe haber al menos un set
        if (count($this->routineData[$exerciseIndex]['sets']) > 1) {
            unset($this->routineData[$exerciseIndex]['sets'][$setIndex]);
            // Reindexa el array de sets
            $this->routineData[$exerciseIndex]['sets'] = array_values($this->routineData[$exerciseIndex]['sets']);
        } else {
            $this->dispatch('toast-message', title: 'Error', message: 'Cada ejercicio debe tener al menos una serie.', type: 'error');
        }
    }


    // --- LÓGICA DE DETALLES Y MODALES (SI SE USAN) ---

    /**
     * Abre el modal de detalles del ejercicio (si se usa la lógica del modal)
     */
    public function showExerciseDetails(int $exerciseId): void
    {
        $exercise = Exercise::find($exerciseId);

        if ($exercise) {
            $this->selectedExerciseDetails = $exercise;
            $this->showModal = true;
        }
    }

    /**
     * Cierra el modal de detalles del ejercicio
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedExerciseDetails = null;
    }
    
    public function render()
    {
        $title = $this->routine ? 'Editar Rutina: ' . $this->name : 'Crear Nueva Rutina';
        
        return view('livewire.employee.create-edit-routine', [
            'clients' => $this->availableClients,
            'targetUser' => $this->targetUser,
        ])->title($title);
    }
}