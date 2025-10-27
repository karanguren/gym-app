<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ClientDataForm;
use App\Livewire\ClientProgress;
use App\Livewire\RoutineBuilder; 
use App\Livewire\ClientRoutines;
use App\Livewire\Admin\AdminLogin;
use App\Livewire\Admin\PostManagement;
use App\Livewire\Admin\DashboardSummary;
use App\Livewire\Admin\ClientManagement;
use App\Livewire\Admin\EmployeeManagement;
use App\Livewire\Pages\Dashboard;
use App\Livewire\Pages\VerificationPending;
use App\Livewire\Auth\TrainerRegister;
use App\Livewire\Employee\EmployeeDashboard;
use App\Livewire\Employee\EmployeeProfileSetupForm; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; 
use Livewire\Volt\Volt; 
use App\Livewire\RoutineWorkout;



// ------------------------------------------------------------------
// 🏠 RUTA PRINCIPAL
// ------------------------------------------------------------------
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ------------------------------------------------------------------
// 🔒 LOGIN SEPARADO DE ADMINISTRACIÓN Y REGISTRO DE STAFF
// ------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/admin-login', AdminLogin::class)->name('admin.login');
    
    // 🎯 Registro dedicado para Staff (Entrenadores/Nutriólogos)
    Route::get('/register/staff', TrainerRegister::class)->name('staff.register');
    // Puedes crear una ruta similar para Nutriólogos si el registro es diferente:
    // Route::get('/register/nutriologo', NutriologoRegister::class)->name('nutriologo.register'); 
    
});

// ------------------------------------------------------------------
// 🛡️ ZONA DE ADMINISTRACIÓN (Solo 'administrador')
// ------------------------------------------------------------------
Route::middleware(['auth', 'verified', 'role:administrador'])->prefix('admin')->group(function () {
    
    Route::get('/dashboard', DashboardSummary::class)->name('admin.dashboard');
    
    // 1. Gestión de CLIENTES
    Route::get('/clients', ClientManagement::class)->name('admin.clients');
    
    // 2. Gestión de EMPLEADOS/STAFF (Ahora maneja Trainer y Nutriologo)
    Route::get('/employees', EmployeeManagement::class)->name('admin.employees');
    
    // 3. Ruta de Publicaciones
    Route::get('/posts', PostManagement::class)->name('admin.posts');

    // 4. RUTA DE LOGOUT DEL ADMINISTRADOR (POST para seguridad)
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    })->name('admin.logout');
});

// ------------------------------------------------------------------
// 🧑‍💼 ZONA DE EMPLEADOS (Roles 'trainer' y 'nutriologo') - UNIFICADA
// ------------------------------------------------------------------
Route::middleware(['auth', 'verified', 'role:trainer,nutriologo'])->prefix('employee')->group(function () {

    Route::get('/profile-setup', EmployeeProfileSetupForm::class)->name('employee.profile.setup');

    Route::get('/dashboard', EmployeeDashboard::class)->name('employee.dashboard');
});


// ------------------------------------------------------------------
// 🏃 ZONA DE CLIENTES (Rol 'cliente')
// ------------------------------------------------------------------
Route::middleware(['auth', 'verified', 'role:cliente'])->group(function () {

    Route::get('/client/profile-setup', ClientDataForm::class)->name('profile.setup');
    
    // 2. Ruta de espera después de enviar los datos
    Route::get('/verification-pending', VerificationPending::class)->name('verification.pending'); 
    
    // 3. RUTA DEL DASHBOARD ESPECÍFICO DEL CLIENTE (Mantiene tu componente Dashboard.php)
    Route::get('/client/dashboard', Dashboard::class)->name('client.dashboard'); 

    // 🎯 AÑADIDA: Ruta para listar las rutinas del cliente
    Route::get('/mis-rutinas', ClientRoutines::class)
        ->name('client.routines');

    Route::get('/armar-rutina', RoutineBuilder::class)->name('client.routine-builder');

    Volt::route('/routine-builder', 'routine-builder')->name('routine.builder');

    // 🎯 CORREGIDA: Ajustada para usar un slug más claro y evitar conflictos
    Route::get('/rutinas/{routine}/entrenar', RoutineWorkout::class)
    ->name('routine.workout')
    ->middleware('auth'); // El middleware ya está en el grupo, pero se deja por claridad

    // Ruta para ver el progreso y las estadísticas del cliente
    Route::get('/client/progress', ClientProgress::class)->name('client.progress');
    
    // RUTAS DE CONFIGURACIÓN DEL USUARIO
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
});

// ------------------------------------------------------------------
// 🔄 RUTA GENÉRICA /dashboard (Punto de Redirección Centralizado)
// ------------------------------------------------------------------
// Esta ruta es la HOME después del login y redirige al panel correcto basado en el rol.
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isTrainer()) {
        return redirect()->route('employee.dashboard'); 
    } elseif ($user->isNutriologo()) {
        return redirect()->route('nutrition.dashboard'); 
    }
    
    // Por defecto, redirige al dashboard del cliente
    return redirect()->route('client.dashboard'); 
        
})->name('dashboard'); // Esta ruta ya NO carga el componente Dashboard, ¡solo redirige!


// ------------------------------------------------------------------
// RUTAS DE AUTENTICACIÓN ESTÁNDAR (Registro/Login de Clientes)
// ------------------------------------------------------------------
require __DIR__.'/auth.php';
