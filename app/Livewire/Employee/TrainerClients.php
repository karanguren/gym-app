<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination; 
use App\Models\User;
use App\Models\ClientProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Exception;

class TrainerClients extends Component
{
    use WithPagination; 

    protected $paginationTheme = 'tailwind'; 
    
    // PROPIEDADES PARA EL MODAL DE DESVINCULACIÓN
    public bool $showDeleteModal = false; 
    public ?int $clientIdToDelete = null; 
    public string $clientNameToDelete = ''; 

    // --- UTILITY METHODS ---

    /**
     * Muestra una notificación Toast en la interfaz de usuario.
     * @param string $message El mensaje a mostrar.
     * @param string $type El tipo de notificación ('success', 'error', 'warning', 'info').
     */
    private function showToast(string $message, string $type = 'success'): void
    {
        $this->dispatch('show-toast', [
            'message' => $message,
            'type' => $type
        ]);
    }

    // --- MÉTODOS DE ACCIÓN PRINCIPALES ---

    /**
     * Redirige al entrenador a la página de asignación/construcción de rutinas 
     * para el cliente especificado.
     * @param int $clientId El ID del cliente al que se asignará la rutina.
     */
     public function assignRoutineToClient(int $clientId) 
    {
        // 1. Validar que el cliente esté realmente asignado a este entrenador y aceptado
        $isAssigned = ClientProfile::where('user_id', $clientId)
                                   ->where('assigned_trainer_id', Auth::id())
                                   ->where('assignment_status', 'accepted')
                                   ->exists();
        
        if (!$isAssigned) {
            $this->showToast('No puedes asignar rutinas a un cliente que no tienes asignado y aceptado.', 'error');
            return $this->redirect()->back(); 
        }

        // 2. Redirigir a la vista de asignación/construcción de la rutina.
        // Asumo que 'assign.routine' es una ruta válida.
        return $this->redirect(route('assign.routine', [
            'client' => $clientId
        ]), navigate: true);
    }
    
    // --- MÉTODOS DE MANEJO DE SOLICITUDES ---

    /**
     * Acepta la solicitud del cliente. Asigna al entrenador y cambia el status a 'accepted'.
     * @param int $clientId El ID del cliente.
     */
    public function acceptRequest(int $clientId)
    {
        $trainerId = Auth::id();

        try {
            // CRÍTICO: Usamos ->with('user') para cargar el nombre del cliente para el toast
            $profile = ClientProfile::with('user')->where('user_id', $clientId)
                                                 ->where('requested_trainer_id', $trainerId)
                                                 ->where('assignment_status', 'pending')
                                                 ->first();

            if (!$profile) {
                $this->showToast('Solicitud no encontrada, ya aceptada o no está pendiente.', 'error');
                return;
            }

            // Usamos una transacción para asegurar la consistencia de los datos
            DB::transaction(function () use ($profile) {
                $profile->update([
                    'assigned_trainer_id' => $profile->requested_trainer_id, // Asignamos formalmente
                    'requested_trainer_id' => null, // Limpiamos la solicitud
                    'is_verified' => true, // Lo marcamos como verificado al aceptar
                    'assignment_status' => 'accepted', 
                ]);
            });

            $clientName = $profile->user->name ?? 'Cliente Desconocido';
            $this->showToast("¡Solicitud aceptada! El cliente '{$clientName}' ha sido asignado.", 'success');
            $this->resetPage(); 
            
        } catch (Exception $e) {
            $this->showToast('Error al aceptar la solicitud: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Rechaza la solicitud del cliente. 
     * Limpia el ID solicitado y establece el status a 'rejected'.
     * @param int $clientId El ID del cliente.
     */
    public function rejectRequest(int $clientId)
    {
        $trainerId = Auth::id();

        try {
            // CRÍTICO: Usamos ->with('user') para cargar el nombre del cliente para el toast
            $profile = ClientProfile::with('user')->where('user_id', $clientId)
                                                 ->where('requested_trainer_id', $trainerId)
                                                 ->where('assignment_status', 'pending')
                                                 ->first();

            if (!$profile) {
                $this->showToast('Solicitud no encontrada, ya rechazada o no está pendiente.', 'error');
                return;
            }

            $clientName = $profile->user->name ?? 'Cliente Desconocido';

            // Transacción para asegurar la consistencia de los datos
            DB::transaction(function () use ($profile) {
                $profile->update([
                    'requested_trainer_id' => null, // Limpiamos el ID del entrenador solicitado
                    'assignment_status' => 'rejected', // Nuevo status
                ]);
            });

            $this->showToast("Solicitud de '{$clientName}' rechazada.", 'warning');
            $this->resetPage(); 

        } catch (Exception $e) {
            $this->showToast('Error al rechazar la solicitud: ' . $e->getMessage(), 'error');
        }
    }

    // --- MÉTODOS DE LA LÓGICA DEL MODAL DE DESVINCULACIÓN ---
    
    /**
     * Muestra el modal de confirmación de desvinculación.
     */
    public function confirmRemoveClient(int $clientId, string $clientName): void
    {
        $this->clientIdToDelete = $clientId;
        $this->clientNameToDelete = $clientName;
        $this->showDeleteModal = true;
    }

    /**
     * Cierra el modal y limpia las propiedades.
     */
    public function closeModal(): void
    {
        $this->showDeleteModal = false;
        $this->clientIdToDelete = null;
        $this->clientNameToDelete = '';
    }

    /**
     * Ejecuta la desvinculación (quita la asignación) del cliente.
     */
    public function removeClient(): void
    {
        if (is_null($this->clientIdToDelete)) {
            $this->closeModal();
            return;
        }

        $clientId = $this->clientIdToDelete;
        $trainerId = Auth::id();
        $clientName = $this->clientNameToDelete;

        try {
            // Buscamos el perfil del cliente que está asignado O pendiente a nosotros
            $profile = ClientProfile::where('user_id', $clientId)
                                   // Aseguramos que solo desvinculemos clientes que nos pertenecen
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
                        'is_verified' => false,
                        'assignment_status' => 'unassigned', // Lo dejamos libre
                    ]);
                });
                $this->showToast("Cliente '{$clientName}' desvinculado exitosamente.", 'info');
            } else {
                $this->showToast('El cliente no fue encontrado o no está asociado contigo.', 'error');
            }
            
            $this->closeModal();
            $this->resetPage(); 
            
        } catch (Exception $e) {
            $this->showToast('Error al desvincular: ' . $e->getMessage(), 'error');
            $this->closeModal();
        }
    }

    // --- PROPIEDADES COMPUTADAS ---

    #[Computed]
    public function clients()
    {
        $trainerId = Auth::id();

        if (!$trainerId) {
            return User::query()->whereRaw('1 = 0')->paginate(10);
        }

        return User::query()
            // CORRECCIÓN CLAVE: Usamos 'profile' para la carga ansiosa
            ->with('profile') 
            ->select('users.*')
            
            // Usamos join para filtrar eficientemente
            ->join('client_profiles', 'users.id', '=', 'client_profiles.user_id')
            ->where('users.role', 'cliente')
            
            // Consulta que incluye tanto asignados ('accepted') como pendientes ('pending')
            ->where(function ($query) use ($trainerId) {
                // 1. Clientes Asignados a mí (Status Accepted)
                $query->where('client_profiles.assigned_trainer_id', $trainerId)
                      ->where('client_profiles.assignment_status', 'accepted')
                      
                      // O
                      
                      // 2. Clientes con Solicitud Pendiente hacia mí
                      ->orWhere(function ($q) use ($trainerId) {
                          $q->where('client_profiles.requested_trainer_id', $trainerId)
                            ->where('client_profiles.assignment_status', 'pending');
                      });
            })
            
            ->orderBy('users.name')
            ->paginate(10); 
    }

    /**
     * Renderiza la vista del componente.
     */
    public function render()
    {
        return view('livewire.employee.trainer-clients')
                    ->title('Mis Clientes Asignados');
    }
}
