<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Facades\Auth; // <-- ¡Asegúrate de importar Auth!

class Dashboard extends Component
{
    // Esta línea funcionará una vez Livewire controle la ruta
    public $layout = 'components.layouts.app'; 

    // 👇 AQUÍ SE MUEVE TODA LA LÓGICA CONDICIONAL
    public function mount()
    {
        $user = Auth::user();
        $isVerified = $user->is_verified ?? $user->profile?->is_verified ?? false;

        // 1. Redirección si no existe el perfil
        if (!$user->profile()->exists()) {
            return redirect()->route('profile.setup');
        }
        
        // 2. Redirección si no está verificado
        if (!$isVerified) {
            return redirect()->route('verification.pending');
        }

        // Si llega hasta aquí, todo está bien y se renderiza el dashboard con el layout.
    }
    // 👆 Fin de la lógica movida

    public function render()
    {
        // Se renderiza la vista de Blade
        return view('livewire.pages.dashboard');
    }
}