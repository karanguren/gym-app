<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8">
            <h1 class="text-4xl font-extrabold text-[#7bcb01] dark:text-[#7bcb01] mb-4">
                👋 Bienvenido(a), {{ $user->name }}
            </h1>

            <!-- Mensaje de bienvenida condicional por rol específico -->
            @if ($user->isTrainer())
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                    Tu panel de Entrenador está listo. Aquí podrás gestionar rutinas y ver el progreso de tus clientes.
                </p>
                <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/50 rounded-lg border border-blue-200 dark:border-blue-700">
                    <h2 class="text-xl font-semibold text-blue-700 dark:text-blue-300">Funciones de Entrenador:</h2>
                    <ul class="list-disc list-inside mt-2 text-gray-600 dark:text-gray-400">
                        <li>Creación y Asignación de Rutinas.</li>
                        <li>Seguimiento de Asistencia y Rendimiento.</li>
                        <li>Comunicación directa con clientes.</li>
                    </ul>
                </div>
            @elseif ($user->isNutriologo())
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                    Tu panel de Nutriólogo está listo. Aquí podrás gestionar planes de alimentación y hacer seguimiento nutricional.
                </p>
                <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/50 rounded-lg border border-green-200 dark:border-green-700">
                    <h2 class="text-xl font-semibold text-green-700 dark:text-green-300">Funciones de Nutriólogo:</h2>
                    <ul class="list-disc list-inside mt-2 text-gray-600 dark:text-gray-400">
                        <li>Elaboración de Planes Nutricionales.</li>
                        <li>Revisión de Diarios de Alimentación.</li>
                        <li>Aprobación y Verificación de datos de clientes.</li>
                    </ul>
                </div>
            @endif
            
            <a href="{{ route('profile.edit') }}" wire:navigate 
               class="mt-8 inline-block px-6 py-2 bg-[#7bcb01] text-white font-semibold rounded-lg shadow-md hover:bg-[#7bcb01]/80 transition duration-150">
               Ir a Configuración de Perfil
            </a>
            
        </div>
    </div>
</div>