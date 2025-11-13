<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ClientDataForm;
use App\Livewire\ClientProgress;
use App\Livewire\RoutineBuilder; 
use App\Livewire\ClientRoutines;
use App\Livewire\TrainerSelection;
use App\Livewire\RoutineWorkout;
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
use App\Livewire\Employee\RoutineTemplatesManager;
use App\Livewire\Employee\TrainerClients;
use App\Livewire\Employee\TrainerDashboard;


use App\Livewire\Employee\CreateEditRoutine;

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
// 🔒 LOGIN SEPARADO DE ADMINISTRACIÓN Y REGISTRO DE STAFF
// ------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/admin-login', AdminLogin::class)->name('admin.login');
    
    // 🎯 Registro dedicado para Staff (Entrenadores/Nutriólogos)
    Route::get('/register/staff', TrainerRegister::class)->name('staff.register');
    
});

// ------------------------------------------------------------------
// 🛡️ ZONA DE ADMINISTRACIÓN (Solo 'administrador')
// ------------------------------------------------------------------
Route::middleware(['auth', 'verified', 'role:administrador'])->prefix('admin')->group(function () {
    
    Route::get('/dashboard', DashboardSummary::class)->name('admin.dashboard');
    
    // Gestión de CLIENTES
    Route::get('/clients', ClientManagement::class)->name('admin.clients');
    
    // Gestión de EMPLEADOS/STAFF 
    Route::get('/employees', EmployeeManagement::class)->name('admin.employees');
    
    // Ruta de Publicaciones
    Route::get('/posts', PostManagement::class)->name('admin.posts');

    // RUTA DE LOGOUT DEL ADMINISTRADOR (POST para seguridad)
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
Route::middleware(['auth', 'verified', 'role:trainer,nutriologo'])->prefix('employee')->name('employee.')->group(function () {

    Route::get('/profile-setup', EmployeeProfileSetupForm::class)->name('profile.setup'); 

    Route::get('/dashboard', EmployeeDashboard::class)->name('dashboard'); 

    // LISTA DE CLIENTES
    Route::get('/clients', TrainerClients::class)->name('clients');
    
    // Crear Rutina Plantilla (Creación general)
    Route::get('/rutinas/crear', CreateEditRoutine::class)->name('routines.create');

    // Crear Rutina Exclusiva para un Cliente
    Route::get('/rutinas/crear/cliente/{userId}', CreateEditRoutine::class)->name('routines.create-for-client');

    // Editar Rutina (Plantilla o Exclusiva)
    Route::get('/rutinas/{routineId}/editar', CreateEditRoutine::class)->name('routines.edit');

    // RUTA PARA GESTIONAR PLANTILLAS
    Route::get('/routines/templates', RoutineTemplatesManager::class)
         ->name('routine-templates.index');
            
});


// ------------------------------------------------------------------
// 🏃 ZONA DE CLIENTES (Rol 'cliente')
// ------------------------------------------------------------------
Route::middleware(['auth', 'verified', 'role:cliente'])->group(function () {

    Route::get('/client/profile-setup', ClientDataForm::class)->name('profile.setup');
    
    // Ruta de espera después de enviar los datos
    Route::get('/verification-pending', VerificationPending::class)->name('verification.pending'); 
    
    // RUTA DEL DASHBOARD DEL CLIENTE 
    Route::get('/client/dashboard', Dashboard::class)->name('client.dashboard'); 

    // Ruta para listar las rutinas del cliente
    Route::get('/mis-rutinas', ClientRoutines::class)->name('client.routines');

    Route::get('/armar-rutina', RoutineBuilder::class)->name('client.routine-builder');

    Volt::route('/routine-builder', 'routine-builder')->name('routine.builder');

    // Ruta de entrenamiento
    Route::get('/rutinas/{routine}/entrenar', RoutineWorkout::class)->name('routine.workout')->middleware('auth'); 

    // Ruta para ver el progreso y las estadísticas del cliente
    Route::get('/client/progress', ClientProgress::class)->name('client.progress');

    // SELECCIÓN DE ENTRENADOR
    Route::get('/entrenador', TrainerSelection::class)->name('client.trainer-selection'); 
    
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
        
})->name('dashboard');


// ------------------------------------------------------------------
// RUTAS DE AUTENTICACIÓN ESTÁNDAR (Registro/Login de Clientes)
// ------------------------------------------------------------------
require __DIR__.'/auth.php';
