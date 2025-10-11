<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ClientDataForm;
use App\Livewire\Admin\AdminLogin;
use App\Livewire\Admin\ClientManagement;
use App\Livewire\Admin\EmployeeManagement;
use App\Livewire\Admin\PostManagement;
use App\Livewire\Admin\DashboardSummary;
use App\Livewire\Pages\Dashboard;
use App\Livewire\Pages\VerificationPending;
use App\Livewire\Auth\TrainerRegister; // 🎯 NUEVA IMPORTACIÓN
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; 
use Livewire\Volt\Volt; 

// ------------------------------------------------------------------
// 🏠 RUTA PRINCIPAL
// ------------------------------------------------------------------
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ------------------------------------------------------------------
// 🔒 LOGIN SEPARADO DE ADMINISTRACIÓN Y REGISTRO DE ENTRENADORES
// ------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/admin-login', AdminLogin::class)->name('admin.login');
    
    // 🎯 NUEVA RUTA: Registro dedicado para Entrenadores
    Route::get('/register/trainer', TrainerRegister::class)->name('trainer.register'); 
    
});

// ------------------------------------------------------------------
// 🛡️ ZONA DE ADMINISTRACIÓN (Solo 'administrador')
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->group(function () {
    
    Route::get('/dashboard', DashboardSummary::class)->name('admin.dashboard');
    
    // 1. Gestión de CLIENTES
    Route::get('/clients', ClientManagement::class)->name('admin.clients');
    
    // 2. Gestión de EMPLEADOS/STAFF
    Route::get('/employees', EmployeeManagement::class)->name('admin.employees');
    
    // 3. Ruta de Publicaciones
    Route::get('/posts', PostManagement::class)->name('admin.posts');

    // 4. RUTA DE LOGOUT DEL ADMINISTRADOR (SOLUCIÓN AL ERROR POST/GET)
    Route::post('/logout', function (Request $request) {
        // Ejecuta el logout
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirige específicamente al login de administración
        return redirect()->route('admin.login');
    })->name('admin.logout'); // <-- Nombre de ruta crucial
});

// ------------------------------------------------------------------
// 🧑‍💼 ZONA DE EMPLEADOS/ENTRENADORES (Rol 'empleado' o 'trainer')
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:empleado,trainer'])->prefix('employee')->group(function () {
    // Panel de Entrenadores/Nutriólogos
    Route::view('/dashboard', 'employee.dashboard')->name('employee.dashboard');
});

// ------------------------------------------------------------------
// 🏃 ZONA DE CLIENTES (Rol 'cliente')
// ------------------------------------------------------------------
Route::middleware(['auth', 'role:cliente'])->group(function () {

    Route::get('/client/profile-setup', ClientDataForm::class)->name('profile.setup');
    
    // 2. Ruta de espera después de enviar los datos (Carga componente Livewire)
    Route::get('/verification-pending', VerificationPending::class)->name('verification.pending'); 
    
    // 3. RUTA DEL DASHBOARD CONDICIONAL (Carga componente Livewire, la lógica de redirección debe estar en mount())
    Route::get('/dashboard', Dashboard::class)->name('dashboard'); 
    
    // RUTAS DE CONFIGURACIÓN DEL USUARIO
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
});

// ------------------------------------------------------------------
// RUTAS DE AUTENTICACIÓN ESTÁNDAR (Registro/Login de Clientes)
// ------------------------------------------------------------------
require __DIR__.'/auth.php';
