<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class EmployeeManagement extends Component
{
    use WithPagination;

    // Propiedades para filtros y búsqueda
    public $filterRole = 'all'; 
    public $search = '';
    
    // Propiedades para la creación de un nuevo usuario
    public $showCreateModal = false;
    public $newName = '';
    public $newLastname = '';
    public $newEmail = '';
    public $newRole = 'trainer'; 
    public $newPassword = '';

    // Roles disponibles para Asignar
    public array $staffRoles = [
        'trainer' => 'Entrenador',
        'nutriologo' => 'Nutriólogo',
        'administrador' => 'Administrador',
        'cliente' => 'Cliente', 
    ];
    
    // --- Modal State ---
    public bool $showConfirmationModal = false;
    public string $modalTitle = '';
    public string $modalMessage = '';
    public string $modalAction = '';
    public int $targetUserId = 0;
    public string $targetUserName = '';
    public string $pendingNewRole = ''; 
    // --- End Modal State ---


    /**
     * Reglas de validación para el formulario de creación.
     */
    protected function rules()
    {
        $creationRoles = array_diff(array_keys($this->staffRoles), ['cliente']);
        
        return [
            'newName' => 'required|string|max:255',
            'newLastname' => 'required|string|max:255',
            'newEmail' => 'required|string|email|max:255|unique:users,email',
            'newRole' => ['required', 'string', Rule::in($creationRoles)],
            'newPassword' => ['required', 'string', Password::min(8)],
        ];
    }
    
    public function updatingFilterRole()
    {
        $this->resetPage();
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    // ----------------------------------------------------------------------
    // Lógica del Modal de Confirmación
    // ----------------------------------------------------------------------

    public function closeModal()
    {
        // Se resetean todas las propiedades, incluyendo el rol pendiente
        $this->reset([
            'showConfirmationModal', 
            'modalTitle', 
            'modalMessage', 
            'modalAction', 
            'targetUserId', 
            'targetUserName',
            'pendingNewRole'
        ]);
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

    

    /**
     * Prepara y abre el modal para confirmar el cambio de rol.
     */
    public function confirmRoleChange(int $userId, string $newRole, string $userName)
    {
        $user = User::find($userId);
        
        // 1. Validaciones básicas de seguridad y existencia
        if (!$user || $user->id === Auth::id()) {
            session()->flash('error', 'No se puede modificar el rol de esta cuenta o el usuario no existe.');
            return;
        }
        
        // 2. Validar que el rol sea uno permitido para Staff (no Admin o Cliente si no está en el SELECT)
        if (!array_key_exists($newRole, $this->staffRoles) || $newRole === 'administrador' || $newRole === 'cliente') {
            session()->flash('error', 'Rol no válido o restringido para esta acción.');
            return;
        }
        
        // 3. Obtener etiquetas para el mensaje
        $oldRoleLabel = $this->staffRoles[$user->role] ?? 'Rol Desconocido';
        $newRoleLabel = $this->staffRoles[$newRole] ?? 'Rol Desconocido';

        // 4. Establecer estado del modal
        $data = [
            'title' => 'Confirmar Cambio de Rol',
            'message' => 'Estás a punto de cambiar el rol de ' . $userName . '. (actualmente ' . $oldRoleLabel . ') a: ' . $newRoleLabel . '¿Estás segura de realizar este cambio?',
            
            'confirmAction' => 'roleChange',
            
            'cancelAction' => 'closeModal', 
            'confirmButtonText' => 'Cambiar',
            'confirmButtonClass' => 'btn-outline-lime',
            'buttonClass' => 'btn-outline-red',

            'params' => [
                $userId,
                $newRole,
                $userName
            ]
        ];

        $this->dispatch('openConfirmModal', data: $data);
    }

    public function confirmToggleActiveStatus(int $userId, string $userName)
    {
        $user = User::find($userId);
        if (!$user || $user->id === Auth::id() || $user->role === 'cliente') {
            session()->flash('error', 'No se puede modificar este usuario.');
            return;
        }

        $isActive = $user->is_active;
        $action = $isActive ? 'Desactivar' : 'Activar';
        $message = $isActive 
            ? "Estás a punto de **DESACTIVAR** a **{$userName}** ({$user->role}). Esto suspenderá su acceso al sistema."
            : "Estás a punto de **ACTIVAR** a **{$userName}** ({$user->role}). Esto restaurará su acceso al sistema.";

        $data = [
            'title' => $action . ' Staff',
            'message' => $message,
            
            'confirmAction' => 'toggleStatus',
            
            'cancelAction' => 'closeModal', 
            'confirmButtonText' => $action,
            'confirmButtonClass' => $isActive ? 'btn-outline-red' : 'btn-outline-lime',
            'buttonClass' => $isActive ? 'btn-outline-lime' : 'btn-outline-red',

            'params' => [
                $userId,
                $userName
            ]
        ];

        $this->dispatch('openConfirmModal', data: $data);
    }

    public function confirmDeleteUser(int $userId, string $userName)
    {
        $user = User::find($userId);
        if (!$user || $user->id === Auth::id()) {
            $this->dispatch('notify', message: 'No puedes eliminar esta cuenta o el usuario no existe.', type: 'error', duration: 3500 );
            return;
        }

        $data = [
            'title' => 'Eliminar Cuenta Permanentemente',
            'message' => 'Estás a punto de **ELIMINAR PERMANENTEMENTE** la cuenta de ' . $userName . '. Esta acción no se puede deshacer. ¿Deseas continuar?',
            
            'confirmAction' => 'deleteUser',
            
            'cancelAction' => 'closeModal', 
            'confirmButtonText' => 'Eliminar',
            'confirmButtonClass' => 'btn-outline-red',
            'buttonClass' => 'btn-outline-lime',

            'params' => [
                $userId,
                $userName
            ]
        ];
        
        $this->dispatch('openConfirmModal', data: $data);
    }
    
    // ----------------------------------------------------------------------
    // Lógica principal de gestión
    // ----------------------------------------------------------------------

    /**
     * elimina un usuario.
     */
    private function deleteUser(int $userId): void
    {
        $user = User::find($userId);
        
        if (!$user) {
            session()->flash('error', 'Usuario no encontrado.');
            return;
        }

        $userName = $this->targetUserName;
        $user->delete();
        $this->dispatch('notify', message: 'Usuario' . $userName . 'eliminado permanentemente.', type: 'success', duration: 3500 );
    }

    /**
     * Alterna el estado activo/inactivo de un usuario.
     */
    private function toggleStatus(int $userId, string $userName)
    {
        $user = User::find($userId);

        if (!$user) {
            $this->dispatch('notify', message: 'Usuario no encontrado.', type: 'error', duration: 3500 );
            return;
        }

        $user->is_active = !$user->is_active;
        $user->save();
        
        $status = $user->is_active ? 'Activado ✅' : 'Desactivado 🚫';
        $this->dispatch('notify', message: 'Usuario ' . $userName . ' ha sido ' . $status . '.', type: 'success', duration: 3500 );
        $this->resetPage();
    }

    /**
     * Crea un nuevo usuario Staff/Admin.
     */
    public function createUser()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->newName,
            'last_name' => $this->newLastname,
            'email' => $this->newEmail,
            'role' => $this->newRole,
            'password' => Hash::make($this->newPassword),
            'is_active' => true, 
            'email_verified_at' => now(), 
        ]);

        $this->reset(['newName', 'newLastname', 'newEmail', 'newPassword', 'newRole', 'showCreateModal']);
        $this->dispatch('notify', message: 'Cuenta de ' . $user->role . ' creada exitosamente: ' . $user->name . ' ' . $user->last_name . '.', type: 'success', duration: 3500 );

    }


    /**
     * Cambia el rol de un usuario.
     */
    private function roleChange(int $userId, string $newRole, string $userName)
    {
        $user = User::find($userId);

        if (!$user || $user->id === Auth::id()) {
            $this->dispatch('notify', message: 'No se puede modificar este usuario o no existe.', type: 'error', duration: 3500 );
            return;
        }
        
        // Validación del rol
        $allowedRoles = array_diff(array_keys($this->staffRoles), ['administrador', 'cliente']);
        if (!in_array($newRole, $allowedRoles)) {
            $this->dispatch('notify', message: 'Rol no válido.', type: 'error', duration: 3500 );
            return;
        }

        $user->role = $newRole;
        $user->save();
        
        $this->dispatch('notify', message: 'Rol de ' . $user->name . ' cambiado a ' . ($this->staffRoles[$newRole] ?? $newRole) . ' exitosamente.', type: 'success', duration: 3500 );
        $this->resetPage();
    }
    
    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        $staffDisplayRoles = ['trainer', 'nutriologo', 'administrador']; 

        $query = User::query()
                     ->where('id', '!=', Auth::id())
                     ->whereIn('role', $staffDisplayRoles) 
                     ->orderBy('name');

        if ($this->filterRole !== 'all' && in_array($this->filterRole, $staffDisplayRoles)) {
            $query->where('role', $this->filterRole);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        $users = $query->paginate(10); 

        // Etiquetas más cortas para la tabla
        $roleLabels = [
            'trainer' => 'Entrenador',
            'nutriologo' => 'Nutriólogo',
            'administrador' => 'Administrador',
            'cliente' => 'Cliente', 
        ];

        return view('livewire.admin.employee-management', [
            'users' => $users,
            'roleLabels' => $roleLabels
        ]);
    }
}