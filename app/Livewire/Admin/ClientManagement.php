<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\ClientProfile;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Collection;

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

    // --- Propiedades de Clientes Pendientes ---
    /** @var Collection */
    public $pendingClients; 
    public $pendingCount = 0; 
    
    // Configuración para que la paginación use los estilos de Tailwind
    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->loadPendingClients();
    }
    
    private function loadPendingClients()
    {
        // LOGICA DE PENDIENTES: Solo cargamos perfiles NO VERIFICADOS que tienen un usuario asociado
        $this->pendingClients = ClientProfile::where('is_verified', false)
            ->whereHas('user')
            ->with('user')
            ->get();
            
        $this->pendingCount = $this->pendingClients->count();
    }

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
        $this->resetPage();
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
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

    // =======================================================
    // LÓGICA DE CONFIRMACIÓN Y ACCIÓN
    // =======================================================

    /**
     * Alterna el estado de actividad general (is_active) del usuario.
     * Si se llama desde la tabla de pendientes (con un perfil asociado), 
     * también marca el perfil como verificado y cambia el rol a 'cliente'.
     */
    public function toggleVerification()
    {
        $userId = $this->targetId;
        $user = User::with('profile')->find($userId); // Cargamos el perfil para la verificación

        if (!$user || $user->id === Auth::id()) {
            $this->dispatchToast('error', 'No se puede modificar este usuario o no existe.');
            $this->closeModal();
            return;
        }

        // 1. Siempre alternamos el estado de actividad general (Desactivar/Activar)
        $newActiveStatus = !$user->is_active;
        $user->is_active = $newActiveStatus;
        
        $action = $newActiveStatus ? 'activado' : 'desactivado';
        
        // 2. LÓGICA DE APROBACIÓN DE PERFIL:
        // Si el usuario tiene un perfil que NO está verificado Y se está activando (Aprobar),
        // realizamos la verificación del perfil y cambio de rol.
        if ($user->profile && !$user->profile->is_verified && $newActiveStatus) {
            $user->profile->is_verified = true;
            $user->profile->save();
            // Aseguramos que el rol sea 'cliente' si se aprueba
            $user->role = 'cliente';
            $action = 'aprobado y activado';
        }

        $user->save();

        $this->loadPendingClients(); 
        $userName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
        $userName = $userName !== '' ? $userName : $user->email;

        $this->dispatchToast('success', 'Cliente ' . $userName . ' ha sido ' . $action . ' exitosamente.');
        $this->closeModal();
    }

    public function confirmToggleVerification(int $userId, string $userName): void
    {
        $user = User::with('profile')->find($userId);

        if (!$user) {
            $this->dispatchToast('error', 'El cliente no puede ser modificado.');
            return;
        }
        
        $this->targetId = $userId;
        $this->modalActionMethod = 'toggleVerification'; 
        
        // Si el perfil no está verificado, la acción principal es la APROBACIÓN (desde la tabla de pendientes)
        if ($user->profile && !$user->profile->is_verified) {
            $this->modalTitle = 'Confirmar Aprobación de Perfil';
            $this->modalMessage = "Estás a punto de **APROBAR** el perfil de **{$userName}**.\nEsto marcará el perfil como 'Verificado'.\n¿Confirmas la aprobación?";
        } else {
            // Si el perfil ya está verificado, la acción es la ACTIVACIÓN/DESACTIVACIÓN general (desde la tabla principal)
            $isActive = $user->is_active;

            if ($isActive) {
                $this->modalTitle = 'Confirmar Desactivación General';
                $this->modalMessage = "Estás a punto de **DESACTIVAR** al cliente **{$userName}**.\nEsto lo inhabilita para acceder al sistema.\n¿Confirmas la desactivación?";
            } else {
                $this->modalTitle = 'Confirmar Activación General';
                $this->modalMessage = "Estás a punto de **ACTIVAR** al cliente **{$userName}**.\nEsto le permite acceder al sistema.\n¿Confirmas la activación?";
            }
        }

        $this->showConfirmationModal = true;
    }

    /**
     * Rechaza un perfil pendiente: elimina el perfil (para sacarlo de la cola de pendientes)
     * y establece la cuenta de usuario como inactiva.
     */
    public function confirmRejectClient(int $profileId, string $userName)
    {
        $this->targetId = $profileId;
        $this->modalTitle = 'Confirmar Rechazo de Verificación';
        $this->modalMessage = "Estás a punto de **RECHAZAR** la información de verificación del cliente **{$userName}**.\nEsto **ELIMINARÁ** su perfil de cliente y **DESACTIVARÁ** su cuenta.\n¿Deseas continuar?";
        $this->modalActionMethod = 'executeRejectClient';
        $this->showConfirmationModal = true;
    }

    public function executeRejectClient()
    {
        $profile = ClientProfile::with('user')->find($this->targetId);

        if ($profile) {
            $userName = trim(($profile->user->name ?? '') . ' ' . ($profile->user->last_name ?? ''));
            $userName = $userName !== '' ? $userName : ($profile->user->email ?? 'Cliente');

            $profile->delete(); // Elimina el perfil (lo saca de la cola de pendientes)

            if ($profile->user) {
                // Restablece el rol y la actividad de la cuenta
                $profile->user->role = 'cliente';
                $profile->user->is_active = false; // <-- DESACTIVAR
                $profile->user->save();
            }

            $this->dispatchToast('success', 'Verificación de cliente ' . $userName . ' rechazada. Perfil eliminado y cuenta desactivada.');
            $this->loadPendingClients();
        } else {
            $this->dispatchToast('error', 'Perfil no encontrado o ya procesado.');
        }

        $this->closeModal();
    }

    public function confirmDeleteUser(int $userId, string $userName)
    {
        $this->targetId = $userId;
        $this->modalTitle = 'Confirmar Eliminación de Usuario';
        $this->modalMessage = "Estás a punto de ELIMINAR permanentemente al usuario **{$userName}**.\nEsta acción no se puede deshacer.\n¿Deseas continuar?";
        $this->modalActionMethod = 'executeDeleteUser';
        $this->showConfirmationModal = true;
    }
    
    public function executeDeleteUser()
    {
        $user = User::find($this->targetId);
        
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
        
        $this->loadPendingClients();
        $this->dispatchToast('success', 'Usuario ' . $userName . ' eliminado permanentemente.');
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
            $this->dispatchToast('error', 'Error al encontrar el cliente para actualizar.');
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

        $this->dispatchToast('success', 'Tipo de cliente de ' . $this->clientToEditName . ' actualizado a ' . $this->availableClientTypes[$this->newClientType] . ' exitosamente.');

        $this->closeTypeChangeModal();
    }

    public function render()
    {
        $statuses = [
            'all' => 'Mostrar Todos',
            'active' => 'Usuarios Activos', 
            'inactive' => 'Usuarios Inactivos', 
        ];

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
        
        $users = $query->paginate(10); 

        return view('livewire.admin.client-management', [
            'users' => $users, 
            'statuses' => $statuses, 
        ]);
    }
}
