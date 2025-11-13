<div class="md:p-8">

    {{-- Título Principal --}}
    <h1 class="titles-border">
        Bienvenido, <span class="text-primary">{{ Auth::user()->name }} {{ Auth::user()->last_name }}</span>!
    </h1>

    <div class="container mx-auto mt-8">

        {{-- 1. Notificación de Solicitudes Pendientes (Se mantiene) --}}
        @if (!$pendingClientRequests->isEmpty())
            <div class="mb-10 p-5 bg-red-50 dark:bg-orange-950 border border-orange-300 dark:border-orange-800 rounded-xl shadow-lg flex justify-between items-center flex-wrap gap-4">
                <div class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-600 dark:text-orange-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-lg font-semibold text-orange-800 dark:text-orange-200">
                        Tienes <span class="font-bold text-xl">{{ $pendingClientRequests->count() }}</span> Solicitud(es) de Cliente Pendiente(s).
                    </p>
                </div>
                
                {{-- Botón de Acción, ahora llama al método Livewire --}}
                <button wire:click="goToClientManagement"
                    class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 transition shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 dark:focus:ring-offset-gray-900"
                >
                    Revisar Solicitudes
                </button>
            </div>
        @endif

        {{-- 2. Cards de Resumen (Clientes Activos y Plantillas) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            
            {{-- Card 1: Clientes Activos (Se utiliza $assignedClients) --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Clientes Activos
                        </p>
                        <p class="mt-1 text-4xl font-extrabold text-indigo-600 dark:text-indigo-400">
                            {{ $assignedClients->count() }}
                        </p>
                    </div>
                    {{-- Ícono de Usuario --}}
                    <svg class="h-10 w-10 text-indigo-400 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div class="mt-4">
                    {{-- Botón para ver la lista de clientes --}}
                    <button wire:click="goToClientManagement"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                    >
                        Ver todos los clientes &rarr;
                    </button>
                </div>
            </div>

            {{-- Card 2: Plantillas Creadas con Botón de Creación (Se utiliza $routineTemplates) --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Plantillas de Rutina
                        </p>
                        <p class="mt-1 text-4xl font-extrabold text-lime-600 dark:text-lime-400">
                            {{ $routineTemplates->count() }}
                        </p>
                    </div>
                    {{-- Ícono de Rutina/Ejercicio --}}
                    <svg class="h-10 w-10 text-lime-400 dark:text-lime-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="mt-4 flex space-x-3">
                    {{-- NUEVO BOTÓN: Ver Mis Rutinas (Índice) --}}
                    <button wire:click="goToRoutinesIndex" 
                        class="w-1/2 inline-flex items-center justify-center px-3 py-2 border border-lime-600 dark:border-lime-500 text-sm font-medium rounded-lg text-lime-600 dark:text-lime-200 bg-transparent hover:bg-lime-50 dark:hover:bg-lime-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 dark:focus:ring-offset-gray-800 transition"
                        wire:loading.attr="disabled"
                    >
                        Mis Rutinas
                    </button>
                    
                    {{-- Botón original: Crear nueva plantilla --}}
                    <button wire:click="createTemplate" 
                        class="w-1/2 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500 dark:focus:ring-offset-gray-800 transition"
                        wire:loading.attr="disabled"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Crear Nueva
                    </button>
                </div>
            </div>

            {{-- Card 3: Solicitudes Pendientes (Opcional, si quieres una card de resumen en lugar del banner) --}}
            @if ($pendingClientRequests->count() > 0)
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Solicitudes Pendientes
                        </p>
                        <p class="mt-1 text-4xl font-extrabold text-orange-600 dark:text-orange-400">
                            {{ $pendingClientRequests->count() }}
                        </p>
                    </div>
                    {{-- Ícono de Notificación --}}
                    <svg class="h-10 w-10 text-orange-400 dark:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div class="mt-4">
                    {{-- Botón para ir a gestión de clientes --}}
                    <button wire:click="goToClientManagement"
                        class="text-sm font-medium text-orange-600 hover:text-orange-500 dark:text-orange-400 dark:hover:text-orange-300 transition-colors"
                    >
                        Revisar Solicitudes &rarr;
                    </button>
                </div>
            </div>
            @endif


        </div>
        
        <hr class="dark:border-gray-700 my-6">

        {{-- 3. Lista de Plantillas Creadas (Detalle Rápido) --}}
        {{-- <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Últimas Plantillas Creadas</h2>
        
        @if ($routineTemplates->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">Aún no has creado ninguna plantilla de rutina.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($routineTemplates->take(6) as $template)
                    <div class="card-tb-ve-v2">
                        <p class="text-lg font-semibold text-gray-800 dark:text-white mb-2">{{ $template->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Creada:
                            {{ $template->created_at->diffForHumans() }}</p>

                        <div class="mt-4 flex space-x-3">
                            <button wire:click="editTemplate({{ $template->id }})"
                                class="btn-outline-lime"
                                wire:loading.attr="disabled">
                                Usar como Base
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if ($routineTemplates->count() > 6)
                <div class="mt-8 text-center">
                    <button wire:click="createTemplate"
                        class="text-base font-medium text-lime-600 hover:text-lime-500 dark:text-lime-400 dark:hover:text-lime-300 transition-colors"
                    >
                        Ver todas las plantillas ({{ $routineTemplates->count() }}) &rarr;
                    </button>
                </div>
            @endif
        @endif --}}

    </div>


</div>