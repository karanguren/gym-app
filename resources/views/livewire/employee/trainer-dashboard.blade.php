<div>
    <!-- Carga de Tailwind CSS para esta vista (asumiendo que estás en un entorno con Tailwind) -->
<script src="https://cdn.tailwindcss.com"></script>
<style>
    :root {
        --color-primary: #1e40af; /* Blue-700 */
        --color-secondary: #fcd34d; /* Amber-300 */
    }
    .card-shadow {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #d1d5db; 
        border-radius: 4px;
    }
</style>

<div class="bg-gray-50 min-h-screen p-4 sm:p-6 lg:p-8">
    <h1 class="text-4xl font-extrabold text-gray-900 mb-6 border-b-4 border-yellow-400 pb-2 inline-block">
        Panel de Entrenador
    </h1>
    <p class="text-gray-600 mb-8">Bienvenido de vuelta, {{ $user->name }}. Administra tus clientes y asigna rutinas.</p>

    <!-- METRICAS / STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Tarjeta de Clientes Totales -->
        <div class="bg-white p-6 rounded-xl card-shadow border-t-4 border-blue-700">
            <p class="text-sm font-medium text-gray-500">Total de Clientes</p>
            <p class="text-4xl font-bold text-gray-900 mt-1">{{ $totalClients }}</p>
            <p class="text-sm text-gray-500 mt-2">Clientes activos asignados</p>
        </div>

        <!-- Tarjeta de Nuevos Clientes -->
        <div class="bg-white p-6 rounded-xl card-shadow border-t-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">Nuevos Clientes (24h)</p>
            <p class="text-4xl font-bold text-green-600 mt-1">{{ $newClientsCount }}</p>
            @if($newClientsCount > 0)
                <p class="text-sm text-green-500 mt-2">¡Felicidades, tienes nuevos clientes!</p>
            @else
                <p class="text-sm text-gray-500 mt-2">Mantente al tanto de nuevas asignaciones.</p>
            @endif
        </div>

        <!-- Tarjeta de Rutinas Creadas -->
        <div class="bg-white p-6 rounded-xl card-shadow border-t-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500">Rutinas Creadas</p>
            <p class="text-4xl font-bold text-gray-900 mt-1">{{ count($routines) }}</p>
            <p class="text-sm text-gray-500 mt-2">Archivos de entrenamiento disponibles.</p>
        </div>
    </div>

    <!-- TABLA DE CLIENTES -->
    <div class="bg-white p-6 rounded-xl card-shadow">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Clientes Asignados</h2>
            <div class="mt-4 md:mt-0">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre..." 
                       class="w-full md:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition duration-150">
            </div>
        </div>

        <!-- Contenedor de la tabla con scrollbar para móvil -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Rutina Actual
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($clients as $clientProfile)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $clientProfile->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                {{ $clientProfile->user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($clientProfile->currentRoutine)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $clientProfile->currentRoutine->name }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Sin Asignar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="openAssignModal({{ $clientProfile->user->id }})"
                                    class="text-blue-600 hover:text-blue-900 bg-blue-100 px-3 py-1 rounded-full text-xs font-semibold transition duration-150 ease-in-out hover:bg-blue-200">
                                    Asignar Rutina
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron clientes asignados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $clients->links() }}
        </div>
    </div>
    
    <!-- MODAL DE ASIGNACIÓN DE RUTINA -->
    <!-- Este modal utiliza Livewire/Alpine.js para su manejo, usando un evento 'open-modal' -->
    <div x-data="{ open: false }" 
        x-on:open-modal.window="if ($event.detail.name === 'assign-routine') open = true"
        x-on:close-modal.window="if ($event.detail.name === 'assign-routine') open = false"
        x-show="open" 
        style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Fondo Oscuro -->
            <div x-show="open" x-transition.opacity.duration.300ms 
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Contenido del Modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div x-show="open" x-transition.duration.300ms
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Asignar Rutina
                    </h3>
                    <div class="mt-4">
                        <p class="text-sm text-gray-500 mb-4">
                            Selecciona una de las rutinas que has creado para asignarla al cliente.
                        </p>
                        
                        <!-- Selector de Rutinas -->
                        <div class="mb-4">
                            <label for="routine-select" class="block text-sm font-medium text-gray-700">Rutina</label>
                            <select wire:model="selectedRoutineId" id="routine-select"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">-- Selecciona una rutina --</option>
                                @foreach($routines as $routine)
                                    <option value="{{ $routine->id }}">{{ $routine->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @error('selectedRoutineId') 
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                        @enderror

                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="assignRoutine" type="button" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                        Confirmar Asignación
                    </button>
                    <button x-on:click="$dispatch('close-modal', { name: 'assign-routine' })" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Toast/Mensaje de Notificación (Placeholder) -->
    <div x-data="{ show: false, title: '', message: '', type: 'success' }" 
         x-on:toast-message.window="show = true; title = $event.detail.title; message = $event.detail.message; type = $event.detail.type; setTimeout(() => { show = false }, 3000)"
         x-show="show"
         x-transition.duration.500ms
         style="display: none;"
         class="fixed bottom-5 right-5 z-50 p-4 rounded-lg shadow-lg text-white font-medium"
         :class="{ 'bg-green-500': type === 'success', 'bg-red-500': type === 'error' }">
        <strong x-text="title"></strong>: <span x-text="message"></span>
    </div>

</div>
<script>
    // Se requiere Alpine.js para el manejo básico de modales (asumiendo que está disponible)
</script>

</div>
