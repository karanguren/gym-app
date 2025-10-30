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
        $this->clientProfile = Auth::user()->profile->fresh();
    }

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

    /**
     * Proceso para que el cliente seleccione un entrenador.
     * @param int $trainerId El ID del usuario entrenador a seleccionar.
     */
    public function selectTrainer(int $trainerId)
    {
        // 1. Verificar si el cliente tiene un perfil
        if (!$this->clientProfile) {
            $this->showToast('Error: No se encontró tu perfil de cliente.', 'error');
            return;
        }

        // 2. Verificar que el ID no sea el del propio cliente (evitar auto-asignación)
        if ((int)$trainerId === (int)Auth::id()) {
            $this->showToast('No puedes asignarte a ti mismo como entrenador.', 'error');
            return;
        }

        // 3. Verificar si el entrenador existe (opcional, pero buena práctica)
        $trainer = User::find($trainerId);
        if (!$trainer) {
             $this->showToast('Error: Entrenador no encontrado.', 'error');
            return;
        }

        try {
            // Asignamos el ID del entrenador seleccionado al perfil del cliente
            $this->clientProfile->assigned_trainer_id = $trainerId;
            $this->clientProfile->save();

            // Refrescamos la propiedad para que la vista se actualice
            $this->clientProfile->fresh();
            
            $this->showToast('¡Has seleccionado a ' . $trainer->name . ' como tu entrenador!', 'success');

        } catch (\Exception $e) {
            $this->showToast('Error al seleccionar el entrenador: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Proceso para que el cliente desvincule a su entrenador actual.
     */
    public function unselectTrainer()
    {
        // 1. Verificar si el cliente tiene un perfil
        if (!$this->clientProfile || is_null($this->clientProfile->assigned_trainer_id)) {
            $this->showToast('No tienes ningún entrenador asignado para desvincular.', 'info');
            return;
        }

        $trainerName = $this->clientProfile->assignedTrainer->name ?? 'tu entrenador';

        try {
            // Desasignamos (establecemos en NULL)
            $this->clientProfile->assigned_trainer_id = null;
            $this->clientProfile->save();

            // Refrescamos la propiedad
            $this->clientProfile->fresh();

            $this->showToast('Has desvinculado a ' . $trainerName . ' exitosamente.', 'info');

        } catch (\Exception $e) {
            $this->showToast('Error al desvincular el entrenador: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Carga los usuarios que son entrenadores.
     * En un sistema real, usarías roles para filtrar. Aquí asumimos `role = 'trainer'`.
     */
    private function loadTrainers()
    {
        $search = '%' . $this->search . '%';
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
        ]);
    }
}
