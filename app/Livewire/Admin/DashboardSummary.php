<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Post; 
// use App\Models\ClientProfile; // No usado, se mantiene por si acaso

class DashboardSummary extends Component
{
    public $clientActiveCount;
    public $clientInactiveCount;
    
    public $trainerTotalCount;
    public $trainerActiveCount;
    
    public $nutriologoTotalCount;
    public $nutriologoActiveCount;
    
    // Contador de Publicaciones
    public $postCount;


    /**
     * Se ejecuta al montar el componente para obtener los datos de resumen.
     */
    public function mount()
    {
        // 1. Clientes
        $this->clientActiveCount = User::where('role', 'cliente')->where('is_active', true)->count();
        $this->clientInactiveCount = User::where('role', 'cliente')->where('is_active', false)->count();

        // 2. Entrenadores
        $this->trainerTotalCount = User::where('role', 'trainer')->count();
        $this->trainerActiveCount = User::where('role', 'trainer')->where('is_active', true)->count();
        
        // 3. Nutriólogos
        $this->nutriologoTotalCount = User::where('role', 'nutriologo')->count();
        $this->nutriologoActiveCount = User::where('role', 'nutriologo')->where('is_active', true)->count();
        
        // 4. Publicaciones
        $this->postCount = Post::count();
    }

    public function render()
    {
        // Retorna la vista de resumen con los datos calculados
        return view('livewire.admin.dashboard-summary');
    }
}