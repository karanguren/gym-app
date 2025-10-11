<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class VerificationPending extends Component
{
    // 1. Define el layout para asegurar que el sidebar se cargue.
    // Esta ruta asume que el layout principal está en 'resources/views/components/layouts/app.blade.php'
    public $layout = 'components.layouts.app'; 

    public function mount()
    {
        // Opcional: Puedes añadir lógica aquí para verificar si el usuario
        // ya ha sido verificado. Si lo está, redirige al dashboard.
        
        $user = auth()->user();

        if ($user->profile && $user->profile->is_verified) {
             return redirect()->route('dashboard');
        }
        
        // También puedes añadir una verificación de si existe el perfil,
        // aunque tu lógica principal en web.php ya maneja esto.
    }

    public function render()
    {
        // 2. Carga la vista de Blade que contiene el mensaje de espera
        // La vista de Blade se buscará en 'resources/views/livewire/pages/verification-pending.blade.php'
        return view('livewire.pages.verification-pending');
    }
}