<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use App\Models\User;
use App\Models\ClientProfile;
use App\Models\Routine;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Componente que maneja la lógica y la interfaz del entrenador.
 * Proporciona listado de clientes, creación/asignación de rutinas, y estadísticas rápidas.
 */
class TrainerDashboard extends Component
{
    use WithPagination;

    // Propiedad inyectada desde el componente padre (employee.dashboard)
    public User $user;
    
    // Filtros y estados de la vista
    public $search = '';
    public $perPage = 10;
    
    // Datos de la lógica de asignación
    public $routines = [];
    public $selectedRoutineId = null;
    public $clientToAssignId = null;

    // Métricas
    public $newClientsCount = 0;
    public $totalClients = 0;

    /**
     * Inicializa el componente con el usuario autenticado y métricas clave.
     * @param User $user El modelo del usuario entrenador (empleado).
     */
    public function mount(User $user)
    {
        $this->user = $user;
        $trainerId = $this->user->id;

        // Cargar rutinas que este entrenador ha creado para el selector
        $this->routines = Routine::where('creator_id', $trainerId)->get(['id', 'name']);
        
        // Contar el total de clientes asignados
        $this->totalClients = ClientProfile::where('assigned_trainer_id', $trainerId)->count();
        
        // Contar nuevos clientes vinculados en el último día para notificación
        $this->newClientsCount = ClientProfile::where('assigned_trainer_id', $trainerId)
                                              ->where('created_at', '>=', now()->subDay())
                                              ->count();
    }
    
    /**
     * Resetea la paginación cuando cambia el término de búsqueda.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Abre el modal de asignación y establece el cliente objetivo.
     * @param int $clientId ID del cliente al que se asignará la rutina.
     */
    public function openAssignModal($clientId)
    {
        $this->clientToAssignId = $clientId;
        $this->dispatch('open-modal', name: 'assign-routine');
    }

    /**
     * Asigna la rutina seleccionada al cliente objetivo.
     */
    public function assignRoutine()
    {
        if (!$this->clientToAssignId || !$this->selectedRoutineId) {
            // Manejar error o mostrar un mensaje
            $this->dispatch('toast-message', title: 'Error', message: 'Selecciona una rutina válida.', type: 'error');
            return;
        }

        $clientProfile = ClientProfile::where('user_id', $this->clientToAssignId)
                                      ->where('assigned_trainer_id', $this->user->id)
                                      ->first();

        if ($clientProfile) {
            $clientProfile->update(['current_routine_id' => $this->selectedRoutineId]);
            
            // Cerrar modal y notificar éxito
            $this->dispatch('close-modal', name: 'assign-routine');
            $this->dispatch('toast-message', title: 'Éxito', message: 'Rutina asignada correctamente.', type: 'success');

        } else {
            $this->dispatch('toast-message', title: 'Error', message: 'Cliente no encontrado o no asignado a ti.', type: 'error');
        }
        
        // Resetear selección
        $this->clientToAssignId = null;
        $this->selectedRoutineId = null;
    }


    /**
     * Renderiza la vista y pasa los clientes paginados.
     */
    public function render()
    {
        // Consulta para clientes asignados, con búsqueda y paginación
        $clients = ClientProfile::query()
            ->where('assigned_trainer_id', $this->user->id)
            ->when($this->search, function ($query) {
                // Filtra por nombre de usuario (relación con la tabla users)
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->with('user', 'currentRoutine') // Incluye la relación de usuario y rutina actual
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.employee.trainer-dashboard', [
            'clients' => $clients,
        ]);
    }
}
