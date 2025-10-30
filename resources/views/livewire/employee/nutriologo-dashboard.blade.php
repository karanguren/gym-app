<div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-8">
    <h1 class="text-4xl font-extrabold text-green-600 dark:text-green-400 mb-4">
        👋 ¡Hola, Nutriólogo(a) {{ $user->name }}!
    </h1>

    <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
        Tu panel de Nutriólogo está en construcción. Aquí gestionarás planes de alimentación, harás seguimiento nutricional y aprobarás datos de clientes.
    </p>
    
    <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/50 rounded-lg border border-green-200 dark:border-green-700">
        <h2 class="text-xl font-semibold text-green-700 dark:text-green-300">Próximas Funciones:</h2>
        <ul class="list-disc list-inside mt-2 text-gray-600 dark:text-gray-400">
            <li>Elaboración y Asignación de Planes Nutricionales.</li>
            <li>Revisión de Diarios de Alimentación.</li>
            <li>Dashboard de progreso de métricas (peso, IMC).</li>
        </ul>
    </div>

    <a href="{{ route('profile.edit') }}" wire:navigate 
        class="mt-8 inline-block px-6 py-2 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition duration-150">
        Ir a Configuración de Perfil
    </a>
</div>

</div>
