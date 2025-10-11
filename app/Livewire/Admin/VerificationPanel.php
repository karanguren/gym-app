<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\ClientProfile; // Para acceder a los perfiles
use Illuminate\Database\Eloquent\Collection; // Para tipado
use App\Models\User; // Para acceder al modelo User

class VerificationPanel extends Component
{
    /** @var Collection */
    public $pendingClients;

    /**
     * Carga los perfiles de clientes que esperan verificación.
     */
    public function mount()
    {
        // Cargar todos los perfiles que no han sido verificados
        $this->pendingClients = ClientProfile::where('is_verified', false)
                                            ->with('user') // Cargar la relación del usuario
                                            ->get();
    }
    
    /**
     * Aprueba un cliente (cambia is_verified a true).
     */
    public function verifyClient(int $profileId)
    {
        $profile = ClientProfile::find($profileId);

        if ($profile) {
            $profile->is_verified = true;
            $profile->save();

            // Opcional: Notificar al usuario (Email/Notificación)

            // Recargar la lista para que el cliente aprobado desaparezca de la tabla
            $this->pendingClients = $this->pendingClients->except($profileId);
            session()->flash('success', 'Cliente ' . $profile->user->name . ' verificado exitosamente.');
        } else {
            session()->flash('error', 'Perfil no encontrado.');
        }
    }

    /**
     * Rechaza o elimina un perfil y el usuario asociado.
     * Esto podría usarse si el Administrador detecta datos falsos o incompletos.
     */
    public function rejectClient(int $profileId)
    {
        $profile = ClientProfile::find($profileId);

        if ($profile) {
            $userName = $profile->user->name;
            $userId = $profile->user->id;

            // Eliminar el perfil, y debido al onDelete('cascade') en la migración,
            // el usuario asociado también se eliminará.
            User::destroy($userId);

            // Recargar la lista
            $this->pendingClients = $this->pendingClients->except($profileId);
            session()->flash('success', 'Cliente ' . $userName . ' y su cuenta eliminados exitosamente.');
        } else {
            session()->flash('error', 'Perfil no encontrado.');
        }
    }

    public function render()
    {
        return view('livewire.admin.verification-panel');
    }
}