<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="md:p-8">

            <h1 class="titles mb-4">Gestión de Clientes y Solicitudes</h1>

            @php
                $pendingClients = $this->pendingRequests->filter(
                    fn($client) => $client->profile?->assignment_status === 'pending',
                );
                $acceptedClients = $this->acceptedClients->filter(
                    fn($client) => $client->profile?->assignment_status === 'accepted',
                );
            @endphp


            <!-- Sección de Solicitudes Pendientes -->
            <div class="mb-10">
                <div class="div-table-principal mb-8">

                    <div class="p-5 flex flex-wrap items-center justify-between gap-2 ">
                        <h2 class="title-table">
                            Solicitudes Pendientes
                        </h2>
                        <span
                            class="px-3 py-1 text-xs sm:text-sm font-bold text-white bg-yellow-500 rounded-full shadow whitespace-nowrap">
                            {{ $this->pendingRequests->total() }} Pendientes
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="tables">
                            <thead class="tables-th">
                                <tr>
                                    <th class="text-left text-table-th">Cliente</th>
                                    {{-- <th class="text-left text-table-th">Email</th> --}}
                                    <th class="text-center text-table-th">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="tables-tbody">
                                @forelse ($pendingClients as $client)
                                    <tr class="hover:bg-lime/10 transition">
                                        <td class="px-6 py-3 text-xs text-gray-600 dark:text-white">
                                            {{ $client->name }} {{ $client->last_name }}
                                        </td>
                                        <td class="px-6 py-3 text-xs">
                                            <div class="flex flex-wrap items-center justify-center gap-2 sm:flex-nowrap">
                                                <button wire:click="confirmAcceptRequest({{ $client->id }})"
                                                    class="btn-outline-lime">
                                                    Aceptar
                                                </button>
                                                <button wire:click="confirmRejectRequest({{ $client->id }})"
                                                    class="btn-outline-red">
                                                    Rechazar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-center text-sm text-gray-500">
                                        No hay solicitudes de clientes pendientes en este momento.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Bloque de Paginación --}}
                    <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                       {{ $this->pendingRequests->links() }}
                    </div>

                </div>
            </div>

            <!-- Sección de Clientes Asignados (Con Paginación Visualmente Mejorada) -->
            <div class="mb-6 ">

                <div class="div-table-principal">

                    <div class="p-5 flex flex-wrap items-center justify-between gap-2 ">
                        <h2 class="title-table">
                             Mis Clientes Asignados ({{ $this->acceptedClients->total() }})
                        </h2>
                    </div>

                    <div class="p-5 bg-white dark:bg-[#1a1a1a]/90 border-b border-gray-200 dark:border-zinc-800 flex flex-col sm:flex-row gap-4 items-center">
                        <div class="flex-grow w-full sm:w-auto">
                            <flux:input wire:model.live.debounce.300ms="search" type="text"
                                placeholder="Buscar por nombre o email..." class:input="!w-full inputs" />
                        </div>

                        <div class="w-full sm:w-48 flex-shrink-0 relative">
                            <flux:select wire:model.live="filterStatus" class="!w-full inputs">
                                @foreach ($statuses as $key => $label)
                                    <flux:select.option value="{{ $key }}">{{ $label }}</flux:select.option>
                                @endforeach
                            </flux:select>

                        </div>
                    </div>

                    {{-- TABLA DE CLIENTES --}}
                    <div class="overflow-x-auto">
                        <table class="tables">
                            <thead class="tables-th">
                                <tr>
                                    <th class="text-left text-table-th">
                                        Nombre</th>
                                    <th class="text-left text-table-th">
                                        Altura</th>
                                    <th class="text-left text-table-th">
                                        Peso</th>
                                    <th class="text-center text-table-th w-1/4">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="tables-tbody">
                                    @forelse ($acceptedClients as $client)
                                        <tr class="dark:hover:bg-[#1a1a1a]/60 hover:bg-gray-50 transition">
                                            <td class="px-6 py-3 text-xs text-gray-800 dark:text-gray-300">
                                                {{ $client->name }} {{ $client->last_name }}
                                            </td>
                                            <td class="px-6 py-3 text-xs text-gray-800 dark:text-gray-300">
                                                {{ $client->profile->height }}
                                            </td>
                                            <td class="px-6 py-3 text-xs text-gray-800 dark:text-gray-300">
                                                {{ $client->profile->weight }} 
                                            </td>
                                            <!-- Bloque de Acciones: 3 Botones -->
                                            <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex flex-col space-y-2 sm:flex-row sm:justify-end sm:space-x-2 sm:space-y-0">

                                                    <!-- Botón 1: Crear Rutina (Acción principal) -->
                                                    <button wire:click="createRoutineForClient({{ $client->id }})"
                                                        class="btn-outline-lime whitespace-nowrap px-3 py-1 text-xs">
                                                        Crear Rutina
                                                    </button>

                                                    <!-- Botón 2: Asignar Plantilla (Acción secundaria) -->
                                                    <button wire:click="openAssignTemplateModal({{ $client->id }})"
                                                        class="btn-outline-grey whitespace-nowrap px-3 py-1 text-xs">
                                                        Asignar Plantilla
                                                    </button>

                                                    <!-- Botón 3: Desvincular Cliente (Acción de peligro) -->
                                                    <button
                                                        wire:click="confirmRemoveClient({{ $client->id }}, '{{ $client->name }}')"
                                                        class="btn-outline-red whitespace-nowrap px-3 py-1 text-xs">
                                                        Desvincular
                                                    </button>
                                                </div>
                                            </td>
                                            <!-- Fin del Bloque de Acciones -->
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No tienes clientes asignados y aceptados.
                                            </td>
                                        </tr>
                                    @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                        {{ $this->acceptedClients->links() }}
                    </div>
                </div>
            </div>


            <!-- Modal de Asignar Plantilla -->
            <div x-cloak x-data="{ open: @entangle('showAssignTemplateModal') }" x-show="open" class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title-assign" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                    <!-- Overlay -->
                    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="bg-modal"
                        aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="open" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        class="relative z-70 inline-block align-bottom rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                        <form wire:submit.prevent="assignTemplateToClient">
                            <div class="modal-card">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                        <h3 class="text-xl font-bold text-center" id="modal-title-assign">
                                            Asignar Rutina Plantilla
                                        </h3>
                                        <div class="mt-4">
                                            <p class="text-sm text-gray-500 mb-4">
                                                Selecciona la plantilla de rutina que deseas copiar y asignar al
                                                cliente.
                                            </p>
                                            <flux:select :label="__('Seleccionar Plantilla')"
                                                wire:model.live="selectedTemplateId" id="new_client_type"
                                                class="!w-full inputs" required>
                                                <flux:select.option value="">-- Selecciona una plantilla --
                                                </flux:select.option>
                                                @foreach ($this->availableTemplates as $template)
                                                    <flux:select.option value="{{ $template->id }}">
                                                        {{ $template->name }}</flux:select.option>
                                                @endforeach
                                            </flux:select>
                                            @error('selectedTemplateId')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenedor de Botones (Footer) - Botones uno al lado del otro en escritorio -->
                                <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">

                                    <!-- Botón de Acción Principal (Confirmar) -->
                                    <button type="submit" wire:loading.attr="disabled"
                                        wire:target="assignTemplateToClient"
                                        class="w-full btn-outline-lime sm:ml-3 sm:w-auto">

                                        <!-- Indicador de Carga (Spinner) -->
                                        <span wire:loading wire:target="assignTemplateToClient"
                                            class="flex items-center">
                                            Procesando...
                                        </span>

                                        <!-- Texto Normal -->
                                        <span wire:loading.remove wire:target="assignTemplateToClient">
                                            Confirmar Asignación
                                        </span>
                                    </button>

                                    <!-- Botón de Acción Secundaria (Cancelar) -->
                                    <button wire:click="closeAssignTemplateModal" type="button"
                                        class="w-full sm:w-auto btn-outline-red mt-3 sm:mt-0">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>