<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TrainerSelection extends Component
{
    use WithPagination;

    // Propiedades para la búsqueda
    public $search = '';
    
    // Mensaje de éxito/error (usando el Toast que se define en la vista)
    public $toastMessage = '';
    public $toastType = 'success';
    
    // Propiedad para el perfil del cliente actual
    public $clientProfile = null;

    // --- PROPIEDADES AÑADIDAS PARA GESTIÓN DE MODALES CON LIVEWIRE ---
    // Usadas para controlar la visibilidad del modal de solicitud. Se enlaza con @entangle en la vista.
    public bool $showSelectionModal = false;
    // Usada para controlar la visibilidad del modal de desvinculación. Se enlaza con @entangle en la vista.
    public bool $showDetachModal = false; 
    // Almacena el ID del entrenador que el usuario quiere seleccionar.
    public ?int $selectedTrainerId = null; 
    // -----------------------------------------------------------------

    // Mantener la paginación de Livewire limpia para la búsqueda
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    /**
     * Inicializa el perfil del cliente.
     */
    public function mount()
    {
        // Cargar el perfil del usuario autenticado (el cliente)
        $this->clientProfile = Auth::user()->profile;
    }

    /**
     * Listener para eventos (aunque lo haremos directo con dispatch).
     */
    protected $listeners = ['assignmentUpdated' => 'refreshProfile'];

    public function refreshProfile()
    {
        // Refresca el perfil del cliente de la base de datos
        $this->clientProfile = Auth::user()->profile->fresh();
    }

    /**
     * Envía un evento a Alpine.js para mostrar la notificación Toast.
     */
    public function showToast($message, $type = 'success')
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
        // Emitir un evento para que Alpine.js muestre la notificación
        $this->dispatch('show-toast', [
            'message' => $message,
            'type' => $type
        ]);
    }

    // --- MÉTODOS DE CONTROL DEL MODAL DE SELECCIÓN ---

    /**
     * Abre el modal de confirmación para seleccionar un entrenador.
     */
    public function openSelectionModal(int $trainerId, string $trainerName)
    {
        // 1. Verificar el estado de la asignación antes de abrir el modal
        if ($this->clientProfile->assignment_status === 'pending') {
            $this->showToast('Ya tienes una solicitud pendiente. Por favor, espera la respuesta o desvincula al entrenador.', 'warning');
            return;
        }

        // 2. Almacenar el ID y abrir el modal
        $this->selectedTrainerId = $trainerId;
        // Alpine.js recibirá el nombre a través del click y el estado de showSelectionModal vía entangle
        $this->showSelectionModal = true;
    }

    /**
     * Cierra el modal de confirmación de selección.
     */
    public function closeSelectionModal()
    {
        $this->showSelectionModal = false;
        $this->selectedTrainerId = null;
    }

    // --- MÉTODOS DE CONTROL DEL MODAL DE DESVINCULACIÓN ---

    /**
     * Abre el modal de confirmación para desvincular al entrenador.
     */
    public function openDetachModal()
    {
        // 1. Verificar si hay algo que desvincular (accepted, pending o rejected)
        if (!in_array($this->clientProfile->assignment_status, ['accepted', 'pending', 'rejected'])) {
            $this->showToast('No tienes un entrenador asignado o una solicitud pendiente/rechazada para desvincular.', 'info');
            return;
        }

        // 2. Abrir el modal
        $this->showDetachModal = true;
    }

    /**
     * Cierra el modal de desvinculación.
     */
    public function closeDetachModal()
    {
        $this->showDetachModal = false;
    }

    // --- MÉTODOS DE LÓGICA DE NEGOCIO ---

    /**
     * Ejecuta la lógica para enviar la solicitud de entrenador.
     * Se llama al confirmar el modal de selección.
     */
    public function selectTrainer()
    {
        if (!$this->selectedTrainerId) {
            $this->showToast('Error: No se seleccionó un entrenador válido.', 'error');
            $this->closeSelectionModal();
            return;
        }

        try {
            // Asignar el nuevo entrenador como 'requested' y cambiar el estado
            // La línea 'requested_trainer_id' se ve CORRECTA aquí.
            $this->clientProfile->update([
                'requested_trainer_id' => $this->selectedTrainerId,
                'assignment_status' => 'pending',
                'assigned_trainer_id' => null, // Asegurar que el asignado es nulo si se está pidiendo uno nuevo
            ]);

            // Obtener el nombre para el toast
            $trainerName = User::find($this->selectedTrainerId)->name ?? 'un entrenador';
            
            // Cerrar modal y notificar
            $this->closeSelectionModal();
            $this->clientProfile->fresh();
            
            $this->showToast('Solicitud enviada a ' . $trainerName . ' exitosamente. Esperando aprobación.', 'success');

        } catch (\Exception $e) {
            // Si Laravel está lanzando una excepción, ¡revísala en el log! Podría ser la asignación masiva.
            $this->showToast('Error al solicitar entrenador: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Ejecuta la lógica para desvincular al entrenador actual.
     * Se llama al confirmar el modal de desvinculación.
     */
    public function detachTrainer()
    {
        try {
            $status = $this->clientProfile->assignment_status;
            
            // Usamos un bloque try-catch seguro para el nombre del entrenador
            try {
                $trainerName = $this->clientProfile->assignedTrainer->name ?? $this->clientProfile->requestedTrainer->name ?? 'el entrenador';
            } catch (\Exception $e) {
                // Si la relación no existe o es nula, usamos el valor por defecto
                $trainerName = 'el entrenador';
            }

            if (!in_array($status, ['accepted', 'pending', 'rejected'])) {
                 $this->showToast('No tienes un entrenador asignado o una solicitud pendiente/rechazada para desvincular.', 'info');
                 $this->closeDetachModal();
                 return;
            }

            // Limpiar ambos campos y establecer el estado a 'unassigned'
            $this->clientProfile->update([
                'assigned_trainer_id' => null,
                'requested_trainer_id' => null,
                'assignment_status' => 'unassigned',
            ]);

            $this->closeDetachModal();
            $this->clientProfile->fresh();

            $this->showToast('Has desvinculado a ' . $trainerName . ' exitosamente.', 'info');

        } catch (\Exception $e) {
            $this->showToast('Error al desvincular el entrenador: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Carga los usuarios que son entrenadores.
     */
    private function loadTrainers()
    {
        $search = '%' . $this->search . '%';
        // Asumiendo que 'assigned_trainer_id' está en clientProfile, usamos el ID del entrenador asignado.
        $currentTrainerId = $this->clientProfile->assigned_trainer_id ?? 0;

        return User::query()
            // Filtro por rol: Asume que tienes una columna 'role' en 'users'
            ->where('role', 'trainer') 
            ->where('id', '!=', Auth::id()) // Excluir al cliente actual
            // Filtrar por nombre o email
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', $search)
                      ->orWhere('email', 'like', $search);
            })
            // Ordenar de modo que el entrenador actualmente asignado (si existe) aparezca primero.
            ->orderByRaw('id = ? DESC', [$currentTrainerId])
            ->orderBy('name')
            ->paginate(10);
    }

    /**
     * Renderiza la vista.
     */
    public function render()
    {
        $trainers = $this->loadTrainers();

        return view('livewire.trainer-selection', [
            'trainers' => $trainers,
        ])->title('Selección de Entrenador');
    }
}
