<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Post; 
use App\Models\ClientProfile;

class DashboardSummary extends Component
{
    public $clientActiveCount;
    public $clientInactiveCount;
    public $employeeCount;
    public $postCount;

    /**
     * Se ejecuta al montar el componente para obtener los datos de resumen.
     */
    public function mount()
    {
        // 1. Clientes
        $this->clientActiveCount = User::where('role', 'cliente')->where('is_active', true)->count();
        $this->clientInactiveCount = User::where('role', 'cliente')->where('is_active', false)->count();

        // 2. Empleados/Staff (incluye empleados y administradores)
        $this->employeeCount = User::whereIn('role', ['empleado'])->count();
        
        // 3. Publicaciones (Asume que el modelo Post existe)
        $this->postCount = Post::count();
    }

    public function render()
    {
        // Retorna la vista de resumen con los datos calculados
        return view('livewire.admin.dashboard-summary');
    }
}
