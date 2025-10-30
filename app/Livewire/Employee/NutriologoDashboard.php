<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use App\Models\User;

/**
 * Componente que maneja la lógica y la interfaz del nutriólogo.
 */
class NutriologoDashboard extends Component
{
    // Propiedad inyectada desde el componente padre
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
        // Aquí se cargaría la lógica específica del Nutriólogo (planes, métricas, etc.)
    }
    
    public function render()
    {
        return view('livewire.employee.nutriologo-dashboard');
    }
}