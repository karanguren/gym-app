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
        // $user = Auth::user();
    }
    // 👆 Fin de la lógica movida

    public function render()
    {
        // Se renderiza la vista de Blade
        return view('livewire.pages.dashboard');
    }
}