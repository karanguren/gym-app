<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeManagement extends Component
{
    use WithPagination;

    // Propiedades para filtros y búsqueda
    public $filterRole = 'all'; 
    public $search = '';
    
    // Propiedades para la creación de un nuevo usuario
    public $showCreateModal = false;
    public $newName = '';
    public $newEmail = '';
    public $newRole = 'trainer'; 
    public $newPassword = '';

    // Roles disponibles para Asignar
    public array $staffRoles = [
        'trainer' => 'Entrenador (Staff)',
        'nutriologo' => 'Nutriólogo (Staff)',
        'administrador' => 'Administrador',
        'cliente' => 'Cliente', 
    ];
    
    // --- Modal State ---
    public bool $showConfirmationModal = false;
    public string $modalTitle = '';
    public string $modalMessage = '';
    public string $modalAction = ''; // 'toggleStatus', 'deleteUser', or 'roleChange'
    public int $targetUserId = 0;
    public string $targetUserName = '';
    public string $pendingNewRole = ''; // <-- NUEVA PROPIEDAD para almacenar el rol pendiente
    // --- End Modal State ---


    /**
     * Reglas de validación para el formulario de creación.
     */
    protected function rules()
    {
        $creationRoles = array_diff(array_keys($this->staffRoles), ['cliente']);
        
        return [
            'newName' => 'required|string|max:255',
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
        // El Blade ya filtra Trainer y Nutriologo, pero se añade una capa de seguridad
        if (!array_key_exists($newRole, $this->staffRoles) || $newRole === 'administrador' || $newRole === 'cliente') {
            session()->flash('error', 'Rol no válido o restringido para esta acción.');
            return;
        }
        
        // 3. Obtener etiquetas para el mensaje
        $oldRoleLabel = $this->staffRoles[$user->role] ?? 'Rol Desconocido';
        $newRoleLabel = $this->staffRoles[$newRole] ?? 'Rol Desconocido';

        // 4. Establecer estado del modal
        $this->modalTitle = 'Confirmar Cambio de Rol';
        $this->modalMessage = "Estás a punto de cambiar el rol de **{$userName}** (actualmente {$oldRoleLabel}) a **{$newRoleLabel}**.\n\n¿Estás segura de realizar este cambio?";
        
        $this->modalAction = 'roleChange';
        $this->targetUserId = $userId;
        $this->targetUserName = $userName;
        $this->pendingNewRole = $newRole; // Guarda el nuevo rol
        
        $this->showConfirmationModal = true;
    }

    public function confirmToggleActiveStatus(int $userId, string $userName)
    {
        $user = User::find($userId);
        if (!$user || $user->id === Auth::id() || $user->role === 'cliente') {
            session()->flash('error', 'No se puede modificar este usuario.');
            return;
        }

        $isActive = $user->is_active;
        $action = $isActive ? 'Inactivar' : 'Activar';
        $message = $isActive 
            ? "Estás a punto de **INACTIVAR** a **{$userName}** ({$user->role}). Esto suspenderá su acceso al sistema."
            : "Estás a punto de **ACTIVAR** a **{$userName}** ({$user->role}). Esto restaurará su acceso al sistema.";

        $this->modalTitle = $action . ' Staff';
        $this->modalMessage = $message;
        $this->modalAction = 'toggleStatus';
        $this->targetUserId = $userId;
        $this->targetUserName = $userName;
        $this->showConfirmationModal = true;
    }

    public function confirmDeleteUser(int $userId, string $userName)
    {
        $user = User::find($userId);
        if (!$user || $user->id === Auth::id()) {
            session()->flash('error', 'No puedes eliminar esta cuenta o el usuario no existe.');
            return;
        }
        
        $this->modalTitle = 'Eliminar Cuenta Permanentemente';
        $this->modalMessage = "Estás a punto de **ELIMINAR PERMANENTEMENTE** la cuenta de **{$userName}**. Esta acción no se puede deshacer. ¿Deseas continuar?";
        $this->modalAction = 'deleteUser';
        $this->targetUserId = $userId;
        $this->targetUserName = $userName;
        $this->showConfirmationModal = true;
    }

    /**
     * Ejecuta la acción pendiente confirmada en el modal.
     */
    public function executeModalAction()
    {
        $userId = $this->targetUserId;
        $userName = $this->targetUserName;
        $action = $this->modalAction;
        
        if ($userId === 0 || empty($action)) {
            session()->flash('error', 'Error de seguridad: Acción no definida.');
            return;
        }

        if ($action === 'toggleStatus') {
            $user = User::find($userId);

            if (!$user) {
                session()->flash('error', 'Usuario no encontrado.');
                return;
            }

            $user->is_active = !$user->is_active;
            $user->save();
            
            $status = $user->is_active ? 'Activado ✅' : 'Inactivado 🚫';
            session()->flash('success', 'Usuario ' . $userName . ' ha sido ' . $status . '.');

        } elseif ($action === 'deleteUser') {
            $user = User::find($userId);
            
            if (!$user) {
                 session()->flash('error', 'Usuario no encontrado.');
                return;
            }

            $user->delete();
            session()->flash('success', 'Usuario ' . $userName . ' eliminado permanentemente.');
            
        } elseif ($action === 'roleChange') {
             // Llama al método de actualización de rol con los datos guardados
             $this->updateRole($userId, $this->pendingNewRole);
        }
        
        $this->resetPage();
    }
    
    // ----------------------------------------------------------------------
    // Lógica principal de gestión
    // ----------------------------------------------------------------------

    /**
     * Crea un nuevo usuario Staff/Admin.
     */
    public function createUser()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->newName,
            'email' => $this->newEmail,
            'role' => $this->newRole,
            'password' => Hash::make($this->newPassword),
            'is_active' => true, 
            'email_verified_at' => now(), 
        ]);

        $this->reset(['newName', 'newEmail', 'newPassword', 'newRole', 'showCreateModal']);
        session()->flash('success', 'Cuenta de ' . $user->role . ' creada exitosamente: ' . $user->name . '.');
    }


    /**
     * Cambia el rol de un usuario. Ahora solo se llama desde executeModalAction.
     */
    public function updateRole(int $userId, string $newRole)
    {
        $user = User::find($userId);

        if (!$user || $user->id === Auth::id()) {
            session()->flash('error', 'No se puede modificar este usuario o no existe.');
            return;
        }
        
        if (!array_key_exists($newRole, $this->staffRoles)) {
            session()->flash('error', 'Rol no válido.');
            return;
        }

        $user->role = $newRole;
        $user->save();
        
        // Uso de $this->staffRoles para obtener el label completo
        session()->flash('success', 'Rol de ' . $user->name . ' cambiado a ' . $this->staffRoles[$newRole] . ' exitosamente.');
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