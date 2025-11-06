<div class="md:p-8">
        
        <h1 class="titles-b">
            🏋️ Dashboard de Entrenador
        </h1>

        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden p-6 sm:p-10 mb-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                    Mis Clientes Asignados ({{ $assignedClients->count() }})
                </h2>
            </div>

            @if ($assignedClients->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">Aún no tienes clientes asignados.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($assignedClients as $client)
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl shadow border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                            
                            <p class="text-xl font-semibold text-gray-800 dark:text-white mb-3">{{ $client->name }}</p>
                            
                            <div class="space-y-2">
                                <a href="#" class="text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition block">
                                    Ver Ficha del Cliente (En desarrollo)
                                </a>

                                {{-- Botón para crear una rutina exclusiva --}}
                                <button 
                                    {{-- Llama al método Livewire que ahora usa la ruta 'employee.routines.create-for-client' --}}
                                    wire:click="createRoutineForClient({{ $client->id }})" 
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition"
                                    wire:loading.attr="disabled"
                                >
                                    Crear Rutina Exclusiva
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden p-6 sm:p-10">
            <div class="flex justify-between items-center mb-6 border-b pb-4 dark:border-gray-700">
                <h2 class="text-2xl font-bold text-lime-600 dark:text-lime-400">
                    Plantillas de Rutina ({{ $routineTemplates->count() }})
                </h2>
                
                {{-- Botón para crear nueva plantilla --}}
                <button 
                    {{-- Llama al método Livewire que ahora usa la ruta 'employee.routines.create' --}}
                    wire:click="createTemplate" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 dark:focus:ring-offset-gray-800 transition"
                    wire:loading.attr="disabled"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Crear Nueva Plantilla
                </button>
            </div>

            @if ($routineTemplates->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">Aún no has creado ninguna plantilla de rutina.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($routineTemplates as $template)
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-5 rounded-xl shadow border border-lime-300/50 dark:border-lime-700/50 flex flex-col justify-between">
                            
                            <p class="text-lg font-semibold text-gray-800 dark:text-white mb-2">{{ $template->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Creada: {{ $template->created_at->diffForHumans() }}</p>
                            
                            <div class="mt-4 flex space-x-3">
                                {{-- Botón de edición --}}
                                <button 
                                    {{-- Llama al método Livewire que ahora usa la ruta 'employee.routines.edit' --}}
                                    wire:click="editTemplate({{ $template->id }})" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-500 hover:bg-indigo-600 transition"
                                    wire:loading.attr="disabled"
                                >
                                    Editar
                                </button>
                                
                                {{-- Botón para usar como base (clonar) --}}
                                <button 
                                    {{-- Usa el mismo método de edición --}}
                                    wire:click="editTemplate({{ $template->id }})" 
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-white hover:bg-gray-100 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 transition"
                                    wire:loading.attr="disabled"
                                >
                                    Usar como Base
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

</div>