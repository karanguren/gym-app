<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination; 
use App\Models\User;
use App\Models\ClientProfile;
use App\Models\Routine;
use App\Models\RoutineExercise; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Exception;

class TrainerClients extends Component
{
    use WithPagination; 

    protected $paginationTheme = 'tailwind'; 
    
    // Propiedades de paginación separadas para la tabla de solicitudes
    public $page = 1; // Para clientes aceptados (Livewire lo maneja por defecto)
    public $pendingPage = 1; // Para solicitudes pendientes

    public $search = ''; // Ya existe para la tabla principal
    public $pendingSearch = '';

    public string $filterStatus = 'all'; // Por defecto: 'all'
    
    public array $statuses = [
        'all' => 'Mostrar Todos',
        'active' => 'Activos',
        'inactive' => 'Inactivos',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    // Mapeo de la paginación a los atributos de las tablas
    protected $queryString = [
        'page' => ['except' => 1],
        'pendingPage' => ['except' => 1, 'as' => 'p_page'],
    ];

    // MODAL DE DESVINCULACIÓN (EXISTENTE)
    public bool $showDeleteModal = false; 
    public ?int $clientIdToDelete = null; 
    public string $clientNameToDelete = ''; 

    // MODAL DE ASIGNACIÓN DE PLANTILLA (CONSOLIDADO)
    public bool $showAssignTemplateModal = false; 
    public ?int $clientIdToAssign = null;
    public ?int $selectedTemplateId = null; // ID de la plantilla seleccionada
    public string $clientNameToAssign = ''; // Para mostrar en el modal

    // --- PROPIEDADES COMPUTADAS ---

    /**
     * Clientes cuya asignación al entrenador está ACEPTADA.
     */
    #[Computed]
    public function acceptedClients()
    {
        $trainerId = Auth::id();

        if (!$trainerId) {
            return User::query()->whereRaw('1 = 0')->paginate(10);
        }

        // 1. Inicializa la consulta principal (Query Builder)
        $query = User::query()
            // 🚩 Corregido: 'profile' es suficiente si no necesitas pre-cargar otra cosa
            ->with('profile') 
            // CRÍTICO: Aseguramos la selección de todas las columnas de users
            ->select('users.*') 
            ->join('client_profiles', 'users.id', '=', 'client_profiles.user_id')
            // ->where('users.role', 'cliente') // Se puede omitir si ClientProfile solo tiene users con rol 'cliente'
            ->where('client_profiles.assigned_trainer_id', $trainerId) // Solo aceptados
            ->where('client_profiles.assignment_status', 'accepted');

        // 2. Aplica el filtro de búsqueda por nombre/email
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            
            $query->where(function ($q) use ($searchTerm) {
                // 🟢 CORRECTO: Se usan prefijos users. para las columnas de users
                $q->where('users.name', 'like', $searchTerm)
                  ->orWhere('users.last_name', 'like', $searchTerm)
                  ->orWhere('users.email', 'like', $searchTerm);
            });
        }
        
        // 3. APLICA EL FILTRO POR ESTADO (is_active)
        // 🚩 CORRECCIÓN CRÍTICA: Añadir el prefijo 'users.' a is_active
        if ($this->filterStatus === 'active') {
            $query->where('users.is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('users.is_active', false);
        }

        // 4. Retorna la paginación de la consulta
        return $query
            ->orderBy('users.name')
            ->paginate(10);
    }

    /**
     * Solicitudes de clientes PENDIENTES de aceptación.
     */
    #[Computed]
    public function pendingRequests()
    {
        $trainerId = Auth::id();

        if (!$trainerId) {
            // Devuelve una colección vacía paginada si no hay ID
            return User::query()->whereRaw('1 = 0')->paginate(5, ['*'], 'pendingPage'); 
        }

        // 1. Inicializa la consulta principal (Query Builder)
        $query = User::query()
            ->with('profile') 
            ->select('users.*')
            ->join('client_profiles', 'users.id', '=', 'client_profiles.user_id')
            ->where('users.role', 'cliente')
            ->where('client_profiles.requested_trainer_id', $trainerId)
            ->where('client_profiles.assignment_status', 'pending');
            
        // 2. Aplica el filtro de búsqueda solo si hay un término
        if ($this->pendingSearch) {
            $searchTerm = '%' . $this->pendingSearch . '%';
            
            // Aplica la búsqueda directamente en la tabla users
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('last_name', 'like', $searchTerm);
            });
        }

        // 3. Retorna la paginación
        return $query
            ->orderBy('users.name')
            ->paginate(5, ['*'], 'pendingPage'); 
    }

    #[Computed]
    public function pendingRequestsCount(): int
    {
        $trainerId = Auth::id();
        
        if (!$trainerId) {
            return 0;
        }

        $query = ClientProfile::query()
            ->where('requested_trainer_id', $trainerId)
            ->where('assignment_status', 'pending');
        
        // Aplica el filtro de búsqueda si $pendingSearch no está vacío
        if ($this->pendingSearch) {
            $searchTerm = '%' . $this->pendingSearch . '%';
            
            // Usamos whereHas('user', ...) para buscar en la tabla 'users'
            // dado que la consulta comienza en ClientProfile
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('last_name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        return $query->count();
    }

    #[Computed]
    public function availableTemplates(): EloquentCollection
    {
        // Carga solo las rutinas plantillas creadas por el entrenador actual
        return Routine::where('is_template', true)
            ->where('creator_id', Auth::id())
            ->orderBy('name')
            ->get();
    }

    // public function getAcceptedClientsProperty()
    // {
    //     // Inicia la consulta
    //     // Asegúrate de adaptar esta parte para obtener SOLO los clientes asignados al usuario actual.
    //     $query = ClientProfile::query()
    //         // FILTRO BASE: Solo clientes aceptados y asignados (ajusta según tu lógica real)
    //         ->where('assigned_trainer_id', auth()->id())
    //         ->where('assignment_status', 'accepted'); 
        
    //     // Aplica el filtro de búsqueda si $search no está vacío
    //     if ($this->search) {
    //         $searchTerm = '%' . $this->search . '%';
            
    //         // Agrupamos las cláusulas OR para que no interfieran con los filtros base
    //         $query->where(function ($q) use ($searchTerm) {
    //             $q->where('name', 'like', $searchTerm)
    //               ->orWhere('last_name', 'like', $searchTerm)
    //               ->orWhere('email', 'like', $searchTerm); 
    //         });
    //     }
        
    //     // Ordenación por defecto
    //     $query->latest();

    //     // Paginamos los resultados
    //     return $query->paginate(10); // Puedes ajustar el número de ítems por página
    // }
    
    // --- UTILITY METHODS ---

    /**
     * Muestra una notificación Toast en la interfaz de usuario.
     */
    private function showToast(string $message, string $type = 'success'): void
    {
        $this->dispatch('show-toast', [
            'message' => $message,
            'type' => $type
        ]);
    }

    // Método que resetea la paginación de ambas tablas
    public function resetAllPages(): void
    {
        $this->resetPage('page');
        $this->resetPage('pendingPage');
    }

    // --- GESTIÓN DE MODAL DE ASIGNACIÓN DE PLANTILLA ---

    /**
     * Abre el modal de asignación de plantilla.
     */
    public function openAssignTemplateModal(int $clientId, string $clientName = 'el cliente'): void
    {
        // Verificamos que existan plantillas antes de abrir
        if ($this->availableTemplates->isEmpty()) {

            $this->dispatch('notify', message: 'No tienes plantillas de rutina creadas para asignar.', type: 'warning', duration: 3500 );
            return;
        }

        // Limpiamos la selección anterior y fijamos los datos del cliente
        $this->reset(['selectedTemplateId']); 
        $this->clientIdToAssign = $clientId;
        $this->clientNameToAssign = $clientName;
        $this->showAssignTemplateModal = true;
    }

    /**
     * Cierra el modal de asignación de plantilla.
     */
    public function closeAssignTemplateModal(): void
    {
        $this->showAssignTemplateModal = false;
        $this->reset(['clientIdToAssign', 'selectedTemplateId', 'clientNameToAssign']);
        $this->resetValidation(); 
    }

    public function createRoutineForClient(int $userId): void
    {
        // Ruta corregida: 'employee.routines.create-for-client'
        $this->redirect(route('employee.routines.create-for-client', ['userId' => $userId]), navigate: true);
    }

    // --- LÓGICA DE ASIGNACIÓN DE PLANTILLA CONSOLIDADA ---

    /**
     * ASIGNA UNA PLANTILLA COPIANDO SU ESTRUCTURA AL CLIENTE.
     */
    public function assignTemplateToClient(): void
    {
        // 1. Validación
        $this->validate([
            'clientIdToAssign' => 'required|integer|exists:users,id', 
            'selectedTemplateId' => 'required|integer|exists:routines,id', 
        ], [
            'selectedTemplateId.required' => 'Debes seleccionar una plantilla.',
            'clientIdToAssign.required' => 'Error: El cliente no está identificado.',
        ]);

        DB::beginTransaction();
        try {
            // 2. Buscar Plantilla y Cliente
            $template = Routine::findOrFail($this->selectedTemplateId);
            $client = User::with('profile')->findOrFail($this->clientIdToAssign);
            
            // 3. Crear una COPIA de la Rutina Plantilla
            $newRoutine = $template->replicate();
            
            // Modificamos las propiedades de la copia
            $newRoutine->name = 'Rutina Asignada: ' . $template->name;
            $newRoutine->is_template = false; 
            $newRoutine->user_id = $client->id; // Asignar al cliente
            $newRoutine->creator_id = Auth::id();
            $newRoutine->save();

            // 4. Copiar los ejercicios asociados
            $templateExercises = $template->routineExercises()->get();
            
            $routineExercisesData = $templateExercises->map(function ($re) use ($newRoutine) {
                
                // --- CORRECCIÓN CRÍTICA AQUÍ ---
                // Se asegura de que $sets_details se guarde como JSON string si es un array.
                $setsDetails = is_array($re->sets_details) 
                    ? json_encode($re->sets_details) 
                    : $re->sets_details;
                
                // Mapeamos y ajustamos los datos del RoutineExercise
                return [
                    'routine_id' => $newRoutine->id, 
                    'exercise_id' => $re->exercise_id,
                    'order' => $re->order,
                    'sets_target' => $re->sets_target,
                    'reps_target' => $re->reps_target,
                    'weight_target' => $re->weight_target,
                    'sets_details' => $setsDetails, // Usar la cadena JSON
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();

            if (!empty($routineExercisesData)) {
                RoutineExercise::insert($routineExercisesData);
            }
            
            // 5. Asignar la ID de la nueva rutina al perfil del cliente 
            $client->profile->current_routine_id = $newRoutine->id;
            $client->profile->save();

            DB::commit();

            $this->dispatch('notify', message: 'La rutina plantilla ' . $template->name . ' fue asignada a: ' . $client->name . '.', type: 'success', duration: 3500 );

            
            
            // 6. Cierre y Recarga
            $this->closeAssignTemplateModal();
            $this->resetAllPages(); 

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error FATAL al asignar plantilla:', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'client_id' => $this->clientIdToAssign,
                'template_id' => $this->selectedTemplateId,
            ]);
            
            $this->dispatch('notify', message: 'Error interno al asignar la rutina. Revisa los logs del servidor.', type: 'error', duration: 3500 );
        }
    }

    // --- MÉTODOS DE ACCIÓN PRINCIPALES ---

    public function createNewRoutine(int $clientId) 
    {
        $isAssigned = ClientProfile::where('user_id', $clientId)
                                   ->where('assigned_trainer_id', Auth::id())
                                   ->where('assignment_status', 'accepted')
                                   ->exists();
        
        if (!$isAssigned) {
            $this->dispatch('notify', message: 'No puedes crear rutinas a un cliente que no tienes asignado y aceptado.', type: 'error', duration: 3500 );
            return $this->redirect()->back(); 
        }

        return $this->redirect(route('employee.routine.create', [
            'userId' => $clientId
        ]), navigate: true);
    }
    
    // --- LÓGICA DE SOLICITUDES Y DESVINCULACIÓN (SIN CAMBIOS) ---

    public function confirmAcceptRequest(int $clientId): void
    {
        $profile = ClientProfile::with('user')->where('user_id', $clientId)
                                             ->first();

        $clientName = $profile->user->name ?? 'Cliente';
        $clientLastName = $profile->user->last_name ?? 'Desconocido';

        // 1. Prepara los datos que necesita el modal
        $data = [
            'title' => 'Confirmar Asignación',
            'message' => 'Estás a punto de aceptar a ' . $clientName . ' ' . $clientLastName . ', Desea continuar?',
            
            // 2. Define la acción de confirmación (el nombre del método)
            'confirmAction' => 'acceptRequest', 
            
            // 3. Define la acción de cancelación (limpieza)
            'cancelAction' => 'closeModal',
            
            'confirmButtonText' => 'Sí, Aceptar',
            'confirmButtonClass' => 'btn-outline-lime',
            'buttonClass' => 'btn-outline-red',
            
            
            'params' => [
                $clientId
            ]
        ];
        
        // 5. Envía el evento al modal global
        $this->dispatch('openConfirmModal', data: $data);
    }

    public function acceptRequest(int $clientId)
    {
        $trainerId = Auth::id();
        try {
            $profile = ClientProfile::with('user')->where('user_id', $clientId)
                                                 ->where('requested_trainer_id', $trainerId)
                                                 ->where('assignment_status', 'pending')
                                                 ->first();

            if (!$profile) {
                $this->dispatch('notify', message: 'Solicitud no encontrada, ya aceptada o no está pendiente.', type: 'error', duration: 3500 );
                
                return;
            }

            DB::transaction(function () use ($profile) {
                $profile->update([
                    'assigned_trainer_id' => $profile->requested_trainer_id,
                    'requested_trainer_id' => null,
                    'is_verified' => true,
                    'assignment_status' => 'accepted', 
                ]);
            });

            $clientName = $profile->user->name ?? 'Cliente';
            $clientLastName = $profile->user->last_name ?? 'Desconocido';

            $this->dispatch('notify', message: '¡Solicitud aceptada! El cliente ' . $clientName . ' ' . $clientLastName . ' ha sido asignado.', type: 'success', duration: 3500 );

            // Reseteamos ambas paginaciones para asegurar que el cliente pase a la tabla principal
            $this->resetAllPages(); 
            
        } catch (Exception $e) {
            $this->dispatch('notify', message: 'Error al aceptar la solicitud.', type: 'error', duration: 3500 );
        }
    }

    public function confirmRejectRequest(int $clientId): void
    {
        $profile = ClientProfile::with('user')->where('user_id', $clientId)
                                             ->first();

        $clientName = $profile->user->name ?? 'Cliente';
        $clientLastName = $profile->user->last_name ?? 'Desconocido';

        // 1. Prepara los datos que necesita el modal
        $data = [
            'title' => 'Rechazar Asignación',
            'message' => 'Estás a punto de recharzar la solicitud de ' . $clientName . ' ' . $clientLastName . ', Desea continuar?',
            
            // 2. Define la acción de confirmación (el nombre del método)
            'confirmAction' => 'rejectRequest', 
            
            // 3. Define la acción de cancelación (limpieza)
            'cancelAction' => 'closeModal',
            
            'confirmButtonText' => 'Sí, Rechazar',
            'confirmButtonClass' => 'btn-outline-red',
            'buttonClass' => 'btn-outline-lime',
            
            
            'params' => [
                $clientId
            ]
        ];
        
        // 5. Envía el evento al modal global
        $this->dispatch('openConfirmModal', data: $data);
    }

    public function rejectRequest(int $clientId)
    {
        $trainerId = Auth::id();

        try {
            $profile = ClientProfile::with('user')->where('user_id', $clientId)
                                                 ->where('requested_trainer_id', $trainerId)
                                                 ->where('assignment_status', 'pending')
                                                 ->first();

            if (!$profile) {
                $this->dispatch('notify', message: 'Solicitud no encontrada o no está pendiente.', type: 'error', duration: 3500 );

                return;
            }

            $clientName = $profile->user->name ?? 'Cliente';
            $clientLastName = $profile->user->last_name ?? 'Desconocido';

            DB::transaction(function () use ($profile) {
                $profile->update([
                    'requested_trainer_id' => null,
                    'assignment_status' => 'rejected', 
                ]);
            });

            $this->dispatch('notify', message: 'Solicitud de '. $clientName . ' ' . $clientLastName . ' rechazada.', type: 'warning', duration: 3500 );

            $this->resetAllPages(); 

        } catch (Exception $e) {
            $this->dispatch('notify', message: 'Error al rechazar la solicitud.', type: 'error', duration: 3500 );
        }
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

    public function confirmRemoveClient(int $clientId, string $clientName): void
    {
        
        // 1. Prepara los datos que necesita el modal
        $data = [
            'title' => 'Confirmar Desvinculación',
            'message' => 'Estás a punto de desvincular a ' . ($clientName) . '? Esto quitará tu asignación en su perfil, eliminando tu acceso a su historial y la capacidad de asignarle rutinas.',
            
            // 2. Define la acción de confirmación (el nombre del método)
            'confirmAction' => 'removeClient', 
            
            // 3. Define la acción de cancelación (limpieza)
            'cancelAction' => 'closeModal',
            
            'confirmButtonText' => 'Sí, Desvincular',
            'confirmButtonClass' => 'btn-outline-red',
            'buttonClass' => 'btn-outline-lime',
            
            
            'params' => [
                $clientId,
                $clientName
            ]
        ];
        
        // 5. Envía el evento al modal global
        $this->dispatch('openConfirmModal', data: $data);
    }

    public function closeModal(): void
    {
        $this->showDeleteModal = false;
        $this->clientIdToDelete = null;
        $this->clientNameToDelete = '';
    }

    public function removeClient(int $clientId, string $clientName): void
    {

        $trainerId = Auth::id();

        if (is_null($clientId)) {
            $this->closeModal();
            return;
        }


        try {
            $profile = ClientProfile::where('user_id', $clientId)
                                   ->where(function($query) use ($trainerId) {
                                        $query->where('assigned_trainer_id', $trainerId)
                                             ->orWhere('requested_trainer_id', $trainerId);
                                   })
                                   ->first();

            if ($profile) {
                DB::transaction(function () use ($profile) {
                    $profile->update([
                        'assigned_trainer_id' => null,
                        'requested_trainer_id' => null,
                        'current_routine_id' => null, 
                        'is_verified' => false,
                        'assignment_status' => 'unassigned', 
                    ]);
                });
                $this->dispatch('notify', message: 'Cliente '. $clientName . ' desvinculado exitosamente.', type: 'info', duration: 3500 );
            } else {
                $this->dispatch('notify', message: 'El cliente no fue encontrado o no está asociado contigo.', type: 'error', duration: 3500 );
            }
            
            $this->closeModal();
            $this->resetAllPages(); 
            
        } catch (Exception $e) {
            $this->dispatch('notify', message: 'Error al desvincular.', type: 'error', duration: 3500 );
            $this->closeModal();
        }
    }

    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        // El nombre de las propiedades computadas ahora es:
        $this->acceptedClients;
        $this->pendingRequests;

        return view('livewire.employee.trainer-clients')
                    ->title('Mis Clientes Asignados');
    }
}