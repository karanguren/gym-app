<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if ($userRole === 'trainer')
            <!-- Carga el componente Livewire completo del entrenador, pasando el usuario como prop -->
            @livewire('employee.trainer-dashboard', ['user' => $user])
        @elseif ($userRole === 'nutriologo')
            <!-- Carga el componente Livewire completo del nutriólogo, pasando el usuario como prop -->
            @livewire('employee.nutriologo-dashboard', ['user' => $user])
        @else
            <!-- Mensaje de error/bienvenida genérica (Fallback) -->
            <div class="p-8 bg-red-100 text-red-800 rounded-lg shadow-xl">
                <h1 class="text-3xl font-bold">Error de Rol</h1>
                <p>Tu rol no está definido correctamente para este panel. Contacta a soporte.</p>
            </div>
        @endif
    </div>
</div>