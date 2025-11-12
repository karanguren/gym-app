<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\ClientProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Collection; 
use Illuminate\Support\Facades\DB;
use App\Mail\ProfileVerified; 
use App\Mail\ProfileRejected; 
use Illuminate\Support\Facades\Mail; 

class ClientManagement extends Component
{
    use WithPagination;

    // --- Propiedades de Filtro y Búsqueda ---
    public $filterStatus = 'all'; 
    public $search = '';

    // --- Propiedades de Estado del Modal ---
    public $showConfirmationModal = false;
    public $modalTitle = '';
    public $modalMessage = '';
    public $modalActionMethod = ''; 
    public $targetId = null; 

    // --- PROPIEDADES para Cambio de Tipo de Cliente ---
    public $showTypeChangeModal = false;
    public $clientToEditId = null; 
    public $clientToEditName = '';
    public $currentClientType = ''; 
    public $newClientType = ''; 
    
    // Tipos de cliente disponibles para el select
    public $availableClientTypes = [
        'regular' => 'Regular',
        'personalized' => 'Personalizado',
    ];

    
    
    // Configuración para que la paginación use los estilos de Tailwind
    protected $paginationTheme = 'tailwind';

    
    // =======================================================
    // HELPERS DEL MODAL Y TOAST
    // =======================================================

    public function closeModal()
    {
        $this->showConfirmationModal = false;
        $this->reset(['modalTitle', 'modalMessage', 'modalActionMethod', 'targetId']);
        $this->closeTypeChangeModal(); 
    }

    public function closeTypeChangeModal(): void
    {
        $this->reset([
            'showTypeChangeModal', 
            'clientToEditId', 
            'clientToEditName', 
            'currentClientType', 
            'newClientType'
        ]);
    }

    protected function dispatchToast(string $type, string $message): void
    {
        $this->dispatch('show-toast', ['type' => $type, 'message' => $message]);
    }

    public function updatingFilterStatus()
    {
        $this->resetPage('mainPage'); 
    }
    
    public function updatingSearch()
    {
        $this->resetPage('mainPage');
    }

    // =======================================================
    // NUEVO MÉTODO CENTRALIZADO PARA LLAMAR DESDE EL MODAL
    // =======================================================
    public function executeModalAction()
    {
        if (empty($this->modalActionMethod)) {
            $this->dispatchToast('error', 'No se ha definido una acción para ejecutar.');
            $this->closeModal();
            return;
        }

        if (!method_exists($this, $this->modalActionMethod)) {
            $this->dispatchToast('error', 'El método especificado no existe en el componente.');
            $this->closeModal();
            return;
        }

        $this->{$this->modalActionMethod}();
    }

    // ------------------------------------------------------------------
    // LÓGICA DE MODAL GLOBAL
    // ------------------------------------------------------------------
    
    protected $listeners = [
        'executeAction' => 'handleGlobalAction', // Captura el evento de ejecución
    ];

    public function handleGlobalAction(string $action, array $params = []): void
    {
        if (method_exists($this, $action)) {
            
            call_user_func_array([$this, $action], $params);
            
        } else {
            Log::warning("Acción global no implementada: $action");
        }
    }

    // =======================================================
    // LÓGICA DE CONFIRMACIÓN Y ACCIÓN
    // =======================================================

    public function toggleProfileVerificationStatus(int $userId, string $userName)
    {
        $user = User::with('profile')->find($userId); 

        if (!$user || !$user->profile || $user->id === Auth::id()) {
            $this->dispatchToast('error', 'No se puede modificar el perfil o no existe.');
            $this->closeModal();
            return;
        }
        
        try {
            DB::beginTransaction(); 

            // Determinar el nuevo estado de verificación (Toggle)
            $newVerifiedStatus = !$user->profile->is_verified;
            $user->profile->is_verified = $newVerifiedStatus;
            
            $action = $newVerifiedStatus ? 'aprobado' : 'rechazado';
            
            if ($newVerifiedStatus) {
                // Si se aprueba, se establece el rol a 'cliente' y se asegura que esté activo
                $user->role = 'cliente';
                $user->is_active = true;
                $user->save(); // Guardar cambios del User
            }

            $user->profile->save(); // Guardar cambio de is_verified
            
            DB::commit(); 

            if ($newVerifiedStatus) {
                // Si el perfil fue aprobado (o verificado)
                Mail::to($user->email)->send(new ProfileVerified($user));
            } else {
                // Si el perfil fue rechazado (o la verificación fue revocada)
                Mail::to($user->email)->send(new ProfileRejected($user));
            }

            $userNameDisplay = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
            $userNameDisplay = $userNameDisplay !== '' ? $userNameDisplay : $user->email;

            $this->dispatch('notify', message: 'Perfil de ' . $userNameDisplay . ' ha sido ' . $action . ' exitosamente.', type: 'success', duration: 3500 );
            
        } catch (Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage()); 
            $this->dispatch('notify', message: 'Error en la base de datos al guardar la verificación. Intenta de nuevo.', type: 'error', duration: 3500 );
        }

        // Ya no necesitamos loadPendingClients(), Livewire re-renderizará
        $this->resetPage('pendingPage'); // Restablece la paginación de pendientes (si aplica)
        $this->closeModal();
    }

    /**
     * FUNCIÓN DEDICADA: Solo se encarga de alternar el estado de actividad general (is_active).
     * Esta función se llama cuando el perfil ya está verificado.
     */
    public function toggleActivityStatus(int $userId, string $userName)
    {
        $user = User::with('profile')->find($userId); 

        if (!$user || $user->id === Auth::id()) {
            $this->dispatchToast('error', 'No se puede modificar este usuario o no existe.');
            $this->closeModal();
            return;
        }

        try {
            DB::beginTransaction();

            // Solo alternamos el estado de actividad general
            $newActiveStatus = !$user->is_active;
            $user->is_active = $newActiveStatus;
            $user->save();

            DB::commit();

            $action = $newActiveStatus ? 'activado' : 'desactivado';
            $userNameDisplay = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
            $userNameDisplay = $userNameDisplay !== '' ? $userNameDisplay : $user->email;

            $this->dispatch('notify', message: 'Cliente ' . $userNameDisplay . ' ha sido ' . $action . ' exitosamente.', type: 'success', duration: 3500 );
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', message: 'Error en la base de datos al guardar la actividad. Intenta de nuevo.', type: 'error', duration: 3500 );
        }

        // Livewire re-renderizará
        $this->closeModal();
    }

    public function confirmToggleVerification(int $userId, string $userName): void
    {

        $user = User::with('profile')->find($userId);
        
        if (!$user) {
            $this->dispatch('notify', message: 'El cliente no puede ser modificado.', type: 'error', duration: 3500 );
            return;
        }

        // --- IMPORTANTE: Se pasan los parámetros directamente en el 'params' del evento ---
        $params = [$userId, $userName];

        if ($user->profile && !$user->profile->is_verified) {
            // Caso 1: Aprobación de Perfil (y Activación)
            $data = [
                'title' => 'Confirmar verificación de Perfil',
                'message' => 'Estás a punto de **VERIFICAR** el perfil de ' . $userName . '. Esto marcará el perfil como Verificado. ¿Confirmas la aprobación?',
                'confirmAction' => 'toggleProfileVerificationStatus', 
                'cancelAction' => 'closeModal',
                'confirmButtonText' => 'Sí, verificar',
                'confirmButtonClass' => 'btn-outline-lime',
                'buttonClass' => 'btn-outline-red',
                'params' => $params // Pasa el ID y el Nombre
            ];
            
            $this->dispatch('openConfirmModal', data: $data);

        } else {
            // Caso 2: Activación/Desactivación General
            $isActive = $user->is_active;

            if ($isActive) {
                // Desactivación General
                $data = [
                    'title' => 'Confirmar Desactivación General',
                    'message' => 'Estás a punto de **DESACTIVAR** al cliente ' . $userName . '. Esto lo inhabilita para acceder al sistema. ¿Confirmas la desactivación?',
                    'confirmAction' => 'toggleActivityStatus', 
                    'cancelAction' => 'closeModal',
                    'confirmButtonText' => 'Sí, Desactivar', // Texto del botón actualizado
                    'confirmButtonClass' => 'btn-outline-red', // Clase del botón actualizada
                    'buttonClass' => 'btn-outline-lime', // Clase del botón actualizada
                    'params' => $params // Pasa el ID y el Nombre
                ];
            } else {
                // Activación General
                $data = [
                    'title' => 'Confirmar Activación General',
                    'message' => 'Estás a punto de **ACTIVAR** al cliente ' . $userName . '. Esto le permite acceder al sistema. ¿Confirmas la activación?',
                    'confirmAction' => 'toggleActivityStatus', 
                    'cancelAction' => 'closeModal',
                    'confirmButtonText' => 'Sí, Activar', // Texto del botón actualizado
                    'confirmButtonClass' => 'btn-outline-lime',
                    'buttonClass' => 'btn-outline-red',
                    'params' => $params // Pasa el ID y el Nombre
                ];
            }

            $this->dispatch('openConfirmModal', data: $data);
        }
        
    }

    /**
     * Rechaza un perfil pendiente.
     */
    public function confirmRejectClient(int $profileId, string $userName)
    {

        $data = [
            'title' => 'Confirmar Rechazo de Verificación',
            'message' => 'Estás a punto de **RECHAZAR** la información de verificación del cliente ' . $userName . '. Esto **ELIMINARÁ** su perfil de cliente y **DESACTIVARÁ** su cuenta. ¿Deseas continuar?',
            
            'confirmAction' => 'executeRejectClient', 
            
            'cancelAction' => 'closeModal',
            
            'confirmButtonText' => 'Sí, Rechazar',
            'confirmButtonClass' => 'btn-outline-red',
            'buttonClass' => 'btn-outline-lime',
            
            'params' => [
                $profileId,
                $userName
            ]
        ];
        
        // 5. Envía el evento al modal global
        $this->dispatch('openConfirmModal', data: $data);

    }

    public function executeRejectClient(int $profileId, string $userName)
    {

        $profile = ClientProfile::with('user')->find($profileId);

        if ($profile) {

            $userName = trim(($profile->user->name ?? '') . ' ' . ($profile->user->last_name ?? ''));
            $userName = $userName !== '' ? $userName : ($profile->user->email ?? 'Cliente');

            $profile->delete(); 

            if ($profile->user) {
                
                $profile->user->role = 'cliente';
                $profile->user->is_active = false;
                $profile->user->save();
            }

            // $this->dispatchToast('success', 'Verificación de cliente ' . $userName . ' rechazada. Perfil eliminado y cuenta desactivada.');
            $this->dispatch('notify', message: 'Verificación de cliente ' . $userName . ' ha sido  rechazada. Perfil eliminado y cuenta desactivada.', type: 'success', duration: 3500 );
            // Livewire re-renderizará
            $this->resetPage('pendingPage'); // Restablece la paginación de pendientes
        } else {
            $this->dispatchToast('error', 'Perfil no encontrado o ya procesado.');
        }

        $this->closeModal();
    }

    public function confirmDeleteUser(int $userId, string $userName)
    {
        $data = [
            'title' => 'Confirmar Eliminación de Usuario',
            'message' => 'Estás a punto de ELIMINAR permanentemente al usuario ' . $userName . '. Esta acción no se puede deshacer. ¿Deseas continuar?',
            
            'confirmAction' => 'executeDeleteUser', 
            
            'cancelAction' => 'closeModal',
            
            'confirmButtonText' => 'Sí, Eliminar',
            'confirmButtonClass' => 'btn-outline-red',
            'buttonClass' => 'btn-outline-lime',
            
            'params' => [
                $userId,
                $userName
            ]
        ];
        
        // 5. Envía el evento al modal global
        $this->dispatch('openConfirmModal', data: $data);

    }
    
    public function executeDeleteUser(int $userId, string $userName)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->dispatchToast('error', 'Usuario no encontrado.');
            $this->closeModal();
            return;
        }
        
        if ($user->id === Auth::id()) {
            $this->dispatchToast('error', 'No puedes eliminar tu propia cuenta.');
            $this->closeModal();
            return;
        }

        $userName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
        $userName = $userName !== '' ? $userName : $user->email;

        $user->delete(); 
        
        // Livewire re-renderizará
        $this->dispatch('notify', message: 'Usuario ' . $userName . ' eliminado permanentemente.', type: 'success', duration: 3500 );
        $this->closeModal();
    }

    // =======================================================
    // NUEVA FUNCIONALIDAD: CAMBIO DE TIPO DE CLIENTE
    // =======================================================

    /**
     * Abre el modal para cambiar el tipo de cliente, cargando los datos.
     * @param int $userId
     */
    public function openTypeChangeModal(int $userId): void
    {
        $user = User::find($userId);

        if (!$user || !$user->isClient()) {
            $this->dispatchToast('error', 'El usuario no es un cliente válido.');
            $this->closeTypeChangeModal();
            return;
        }

        $this->clientToEditId = $userId;
        $this->clientToEditName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
        $this->currentClientType = $user->client_type;
        // Inicializar el nuevo tipo con el actual para la selección predeterminada
        $this->newClientType = $user->client_type; 
        $this->showTypeChangeModal = true;
    }


    /**
     * cambio de tipo de cliente.
     */
    public function updateClientType()
    {
        $this->validate([
            'newClientType' => 'required|in:regular,personalized',
        ]);

        $user = User::find($this->clientToEditId);

        if (!$user || !$user->isClient()) {

            $this->dispatch('notify', message: 'Error al encontrar el cliente para actualizar.', type: 'error', duration: 3500 );

            $this->closeTypeChangeModal();
            return;
        }

        if ($user->client_type === $this->newClientType) {
            $this->dispatchToast('info', 'El tipo de cliente ya es ' . $this->availableClientTypes[$this->newClientType] . '. No se realizó ningún cambio.');
            $this->closeTypeChangeModal();
            return;
        }

        $user->client_type = $this->newClientType;
        $user->save();

        $this->dispatch('notify', message: 'Tipo de cliente de ' . $this->clientToEditName . ' actualizado a ' . $this->availableClientTypes[$this->newClientType] . ' exitosamente.', type: 'success', duration: 3500 );

        $this->closeTypeChangeModal();
    }

    /**
     * MÉTODO RENDER CENTRALIZADO CON LÓGICA DE PAGINACIÓN DUAL
     */
    public function render()
    {
        $statuses = [
            'all' => 'Mostrar Todos',
            'active' => 'Usuarios Activos', 
            'inactive' => 'Usuarios Inactivos', 
        ];

        // 1. Consulta para la TABLA PRINCIPAL ($users)
        $query = User::query()
            ->where('role', 'cliente')
            ->with('profile')
            ->where('id', '!=', Auth::id())
            ->orderBy('name');

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        // Paginación principal. Usamos 'mainPage' como nombre de paginador
        $users = $query->paginate(10, ['*'], 'mainPage'); 

        // 2. Consulta para la TABLA DE PENDIENTES ($pendingClients)
        $pendingClients = ClientProfile::where('is_verified', false)
            ->whereHas('user')
            ->with('user')
            // Usamos un tamaño más pequeño y 'pendingPage' como nombre de paginador
            ->paginate(5, ['*'], 'pendingPage'); 
            
        // El conteo total para el badge
        $pendingCount = $pendingClients->total(); 

        return view('livewire.admin.client-management', [
            'users' => $users, 
            'statuses' => $statuses, 
            'pendingClients' => $pendingClients, // Pasamos el paginador
            'pendingCount' => $pendingCount,     // Pasamos el conteo total
        ]);
    }
}