<div>
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="bg-white dark:bg-[#1a1a1a]/85 overflow-hidden shadow-xl sm:rounded-lg p-6">

        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">
            Bienvenido a tu Panel de Control
        </h1>

        @if (Auth::user()->role === 'administrador' || Auth::user()->role === 'trainer')
            <!-- Vista de Administrador/Empleado: Botones de Gestión -->
            <div class="p-6 bg-indigo-50 dark:bg-indigo-900 rounded-lg shadow-inner">
                <h2 class="text-2xl font-bold text-indigo-700 dark:text-indigo-300 mb-4">
                    Panel de Administración
                </h2>
                <p class="text-gray-700 dark:text-gray-300 mb-6">
                    Funciones administrativas y de gestión para {{ Auth::user()->role }}.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- 1. Registrar Entrenadores/Empleados -->
                    <a href="{{ route('admin.register-employee') }}" class="flex items-center space-x-3 p-4 bg-white dark:bg-gray-700 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">Registrar Entrenador/Empleado</span>
                    </a>
                    
                    <!-- 2. Verificar Clientes -->
                    <a href="{{ route('admin.verify-clients') }}" class="flex items-center space-x-3 p-4 bg-white dark:bg-gray-700 rounded-lg shadow-md hover:shadow-lg transition duration-300">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.103A9.5 9.5 0 1111 2.378"></path></svg>
                        <span class="font-semibold text-gray-800 dark:text-gray-100">Verificar Clientes (Pendientes)</span>
                    </a>
                </div>
            </div>
        
        @elseif (Auth::user()->role === 'cliente' && Auth::user()->profile && Auth::user()->profile->is_verified)
            <p class="text-lg text-green-600 dark:text-green-400">
                ¡ Aquí encontrarás tus rutinas y planes !.
            </p>
            
            <!-- INCLUSIÓN DEL COMPONENTE LIVEWIRE PARA RUTINAS -->
            <livewire:client-routines />

        @elseif (Auth::user()->profile && !Auth::user()->profile->is_verified)
            <!-- Si el perfil existe pero no está verificado, se muestra el mensaje de espera -->
            <div class="p-4 mt-6 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200 rounded-lg" role="alert">
                <p class="font-bold">Verificación Pendiente</p>
                <p>Tu perfil está en revisión. Recibirás una notificación cuando tengas acceso completo.</p>
            </div>
        @else
            <!-- Esto no debería ocurrir si RouteServiceProvider::HOME está configurado correctamente, 
                pero es un buen fallback -->
            <p class="text-lg text-red-600 dark:text-red-400">
                Advertencia: Parece que tu perfil no está completo. Por favor, completa tu información.
            </p>
        @endif
        

    </div>
</div>

</div>