<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Componente global de Livewire para mostrar un modal de confirmación.
 * Actúa como mediador, recibiendo un evento de apertura y disparando 
 * un evento de acción al componente padre al confirmar.
 */
class ConfirmModal extends Component
{
    // Indica si el modal debe mostrarse
    public bool $show = false; 

    // Propiedades de visualización y texto
    public string $title = 'Confirmación Requerida';
    public string $message = '¿Estás seguro de que quieres continuar con esta acción?';
    public string $confirmButtonText = 'Confirmar';
    public string $confirmButtonClass = 'btn-outline-lime'; // Clase por defecto (verde)
    public string $buttonClass = 'btn-outline-red'; // Clase por defecto (verde)

    // El nombre del método público en el componente padre que se llamará al confirmar
    public string $confirmAction = '';

    // El nombre del método público en el componente padre que se llamará al cancelar.
    // Por defecto, solo llama a closeModal (cierre local).
    public string $cancelAction = 'closeModal';

    // Datos adicionales que se necesitan para la acción (ej. ID, clave, etc.)
    public array $params = [];

    // El componente escucha el evento 'openConfirmModal' para mostrarse
    protected $listeners = ['openConfirmModal'];

    /**
     * Llena las propiedades del modal y lo muestra, llamado por el evento 'openConfirmModal'.
     *
     * @param array $data 
     * [ 'title', 'message', 'confirmAction', 'cancelAction', 'confirmButtonText', 'confirmButtonClass', 'params' ]
     */
    public function openConfirmModal(array $data): void
    {
        $this->title = $data['title'] ?? $this->title;
        $this->message = $data['message'] ?? $this->message;
        
        // La acción de confirmación es OBLIGATORIA
        $this->confirmAction = $data['confirmAction'] ?? ''; 
        
        // La acción de cancelación (puede ser 'closeModal' o un método del padre)
        $this->cancelAction = $data['cancelAction'] ?? 'closeModal'; 
        
        $this->confirmButtonText = $data['confirmButtonText'] ?? $this->confirmButtonText;
        $this->confirmButtonClass = $data['confirmButtonClass'] ?? $this->confirmButtonClass;
        $this->buttonClass = $data['buttonClass'] ?? $this->buttonClass;
        $this->params = $data['params'] ?? [];
        
        // Solo mostramos si hay una acción definida
        if (!empty($this->confirmAction)) {
            $this->show = true;
        } else {
            $this->show = false;
        }
    }
    
    /**
     * Cierra el modal localmente.
     */
    public function closeModal(): void
    {
        $this->show = false;
    }

    /**
     * Dispara el evento que ejecuta la acción de confirmación en el componente padre
     * y luego cierra el modal.
     */
    public function executeConfirmAction(): void
    {
        if ($this->confirmAction) {
            // Dispara 'executeAction' que es escuchado por handleGlobalAction en el componente padre
            $this->dispatch('executeAction', action: $this->confirmAction, params: $this->params);
        }
        $this->closeModal();
    }
    
    /**
     * Ejecuta la acción de cancelación definida si no es solo el cierre local.
     */
    public function executeCancelAction(): void
    {
        if ($this->cancelAction && $this->cancelAction !== 'closeModal') {
            // Si la acción de cancelación requiere lógica en el padre (ej. limpiar propiedades)
            $this->dispatch('executeAction', action: $this->cancelAction, params: $this->params);
        }
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.confirm-modal');
    }
}