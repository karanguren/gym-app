<div class="bg-gray-50/80 dark:bg-black/80 min-h-screen">
    <div x-data="{ showToast: false, toastMessage: '', toastType: 'success' }" 
        x-init="@this.on('show-toast', (event) => {
            toastMessage = event[0].message;
            toastType = event[0].type || 'success';
            showToast = true;
            setTimeout(() => showToast = false, 3000);
        })"
        x-show="showToast"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-5 right-5 z-50 p-4 rounded-lg shadow-xl flex items-center space-x-3"
        :class="{
            'bg-green-100 border border-green-400 text-green-700': toastType === 'success',
            'bg-red-100 border border-red-400 text-red-700': toastType === 'error',
            'bg-yellow-100 border border-yellow-400 text-yellow-700': toastType === 'warning',
            'bg-blue-100 border border-blue-400 text-blue-700': toastType === 'info',
        }">
        <p class="font-bold" x-text="toastMessage"></p>
    </div>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        <i class="fas fa-users-cog mr-2 text-indigo-500"></i> Mis Clientes Asignados
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Gestiona las solicitudes de asignación y tus clientes activos.
                    </p>
                </div>

                {{-- Tabla de Clientes --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Cliente
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300 hidden sm:table-cell">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Estado
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse ($this->clients as $client)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $client->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                        {{ $client->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @php
                                            $status = $client->profile?->assignment_status ?? 'unassigned';
                                            $isPending = $status === 'pending';
                                            $isAccepted = $status === 'accepted';
                                        @endphp

                                        @if ($isPending)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-400 text-yellow-900 shadow-sm">
                                                Pendiente
                                            </span>
                                        @elseif ($isAccepted)
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-500 text-white shadow-md">
                                                Asignado
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-500 text-white dark:bg-gray-600">
                                                Desconocido
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center space-x-2">
                                            @if ($isPending)
                                                <button wire:click="acceptRequest({{ $client->id }})" 
                                                        class="px-3 py-1 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-150 ease-in-out text-xs shadow-md">
                                                    <i class="fas fa-check"></i> Aceptar
                                                </button>
                                                <button wire:click="rejectRequest({{ $client->id }})" 
                                                        class="px-3 py-1 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-150 ease-in-out text-xs shadow-md">
                                                    <i class="fas fa-times"></i> Rechazar
                                                </button>
                                            @elseif ($isAccepted)
                                                <button wire:click="assignRoutineToClient({{ $client->id }})" 
                                                        class="px-3 py-1 bg-indigo-500 text-white font-semibold rounded-lg hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out text-xs shadow-md">
                                                    <i class="fas fa-dumbbell"></i> Rutina
                                                </button>
                                                <button wire:click="confirmRemoveClient({{ $client->id }}, '{{ $client->name }}')" 
                                                        class="px-3 py-1 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150 ease-in-out text-xs dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 shadow-md">
                                                    <i class="fas fa-unlink"></i> Desvincular
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        No tienes clientes asignados o solicitudes pendientes.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6">
                    {{ $this->clients->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE CONFIRMACIÓN DE DESVINCULACIÓN  --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-[9999] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            <div class="fixed inset-0 bg-gray-500/50 bg-opacity-70 transition-opacity z-[9990]" aria-hidden="true" wire:click="closeModal"></div>

            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0 z-[9995] relative">
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border-t-4 border-red-500">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                                    Confirmar Desvinculación
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    ¿Estás seguro de que deseas desvincular a <strong class="font-bold text-red-500">{{ $clientNameToDelete }}</strong> de tu lista de clientes? 
                                    Esto eliminará tu asignación y **perderá acceso a sus rutinas**. Esta acción es reversible si el cliente te selecciona de nuevo.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse sm:mt-0 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-700 rounded-b-xl">
                        {{-- Botón de Desvincular --}}
                        <button wire:click="removeClient" type="button" 
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-md px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-150 transform hover:scale-[1.02]">
                            <i class="fas fa-trash-alt mr-2"></i> Sí, Desvincular
                        </button>
                        
                        {{-- Botón de Cancelar --}}
                        <button wire:click="closeModal" type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-100 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition duration-150 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500 dark:border-gray-600">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
