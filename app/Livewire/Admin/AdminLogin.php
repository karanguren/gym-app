<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout; // Para especificar el layout si usas uno
use App\Models\User;

#[Layout('layouts.guest')] // Puedes usar layouts.guest o tu layout base
class AdminLogin extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    
    // Reglas de validación básicas
    protected array $rules = [
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ];

    /**
     * Intenta iniciar sesión y verifica el rol.
     */
    public function login()
    {
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', trans('auth.failed'));
            return;
        }

        $user = Auth::user();
        
        // **VERIFICACIÓN DE ROL CLAVE**
        if ($user->role !== 'administrador') {
            // Si el usuario no es administrador, lo desconectamos inmediatamente.
            Auth::logout();
            $this->addError('email', 'Acceso denegado. Esta área es solo para la Administración.');
            return;
        }
        
        // Si la autenticación es exitosa y el rol es 'administrador', redirigimos.
        return $this->redirect(route('admin.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.admin-login');
    }
}