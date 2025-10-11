<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class EmployeeManagement extends Component
{
    use WithPagination;

    public $filterRole = 'all'; // 'all', 'empleado', 'administrador'
    public $search = '';
    
    // Roles disponibles para el Staff
    public array $roles = [
        'empleado' => 'Empleado (Entrenador/Nutriólogo)',
        'administrador' => 'Administrador',
    ];

    public function updatingFilterRole()
    {
        $this->resetPage();
    }
    
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Cambia el rol de un usuario (solo entre empleado y administrador).
     */
    public function updateRole(int $userId, string $newRole)
    {
        $user = User::find($userId);

        if (!$user || $user->id === Auth::id()) {
            session()->flash('error', 'No se puede modificar este usuario o no existe.');
            return;
        }

        if (!array_key_exists($newRole, $this->roles) && $newRole !== 'cliente' && $newRole !== 'inactivo') {
            session()->flash('error', 'Rol no válido para este panel.');
            return;
        }

        $user->role = $newRole;
        $user->save();
        
        session()->flash('success', 'Rol de ' . $user->name . ' cambiado a ' . $this->roles[$newRole] . '.');
    }

    /**
     * Elimina un usuario.
     */
    public function deleteUser(int $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            session()->flash('error', 'Usuario no encontrado.');
            return;
        }

        $userName = $user->name;
        $user->delete();

        session()->flash('success', 'Usuario ' . $userName . ' eliminado permanentemente.');
    }

    public function render()
    {
        $query = User::query()
                    ->where('id', '!=', Auth::id())
                    ->whereIn('role', ['empleado', 'administrador'])
                    ->orderBy('name');

        if ($this->filterRole !== 'all') {
            $query->where('role', $this->filterRole);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        $users = $query->paginate(10); 

        return view('livewire.admin.employee-management', [
            'users' => $users,
        ]);
    }
}
