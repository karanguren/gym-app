<div class="div-principal">

    <div class="max-w mx-auto sm:px-6 lg:px-8">

        <div class="md:p-8">

            <h1 class="titles-border">Selecciona tu Entrenador Personal</h1>

            {{-- Estado de Asignación --}}
            @php
                $status = $this->clientProfile->assignment_status ?? 'unassigned';
                $assignedTrainer = $this->clientProfile->assignedTrainer;
                $requestedTrainer = $this->clientProfile->requestedTrainer;
                $trainer = $assignedTrainer ?? $requestedTrainer;

                $statusClasses = [
                    'unassigned' => 'bg-gray-100 text-gray-600',
                    'pending' => 'bg-blue-100 text-blue-800 ring-2 ring-red-500/50 animate-pulse',
                    'accepted' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                ];

                $statusText = [
                    'unassigned' => 'Actualmente no tienes un entrenador asignado.',
                    'pending' =>
                        'Solicitud pendiente de aprobación de ' . ($requestedTrainer->name ?? 'un entrenador') . '.',
                    'accepted' =>
                        '¡Felicidades! Estás siendo entrenado por ' .
                        ($assignedTrainer->name ?? 'un entrenador asignado') .
                        '.',
                    'rejected' => 'Tu solicitud fue rechazada. Por favor, selecciona otro entrenador.',
                ];
            @endphp

            {{-- ELIMINAMOS EL BLOQUE DE ESTADO/BOTÓN GLOBAL AQUÍ YA QUE ESTÁ COMENTADO --}}
            {{-- Pero si quieres usarlo, aquí está el bloque descomentado y funcional: --}}
            {{-- <div
                class="mb-8 p-4 sm:p-6 card-tl-ve transition duration-300 {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-600' }}">
                <div class="flex items-center justify-between flex-wrap">
                    <p class="text-sm font-semibold dark:text-white">
                        {{ $statusText[$status] ?? 'Estado desconocido.' }}
                    </p>
                    @if ($status === 'accepted' || $status === 'pending' || $status === 'rejected')
                        <button wire:click="openDetachModal"
                            class="mt-2 sm:mt-0 px-4 py-2 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition duration-150 shadow-md">
                            @if ($status === 'pending')
                                Cancelar Solicitud
                            @else
                                Desvincular Entrenador
                            @endif
                        </button>
                    @endif
                </div>

                @if ($trainer)
                    <p class="mt-2 text-sm italic font-medium dark:text-gray-300">
                        Entrenador: {{ $trainer->name }} ({{ $trainer->email }})
                    </p>
                @endif

                @if ($status === 'rejected')
                    <p class="mt-2 text-sm dark:text-gray-300">
                        Puedes solicitar a otro entrenador de la lista a continuación.
                    </p>
                @endif
            </div> --}}


            {{-- Formulario de Búsqueda --}}
            <div class="mb-6">
                <flux:input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Buscar entrenador por nombre o email..." class:input="!w-full inputs" />
            </div>

            {{-- Lista de Entrenadores --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse ($trainers as $trainerItem)
                    <div class="card-tb-ve-v2 !p-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ $trainerItem->name . ' ' . $trainerItem->last_name }}</h2>
                            <div class="mt-3 text-sm dark:text-gray-400 space-y-1">
                                <p><span class="font-semibold text-gray-700 dark:text-gray-300">Certificaciones:</span>
                                    Certificado en Nutrición, Nivel I F.A. (Placeholder)</p>
                                <p><span class="font-semibold text-gray-700 dark:text-gray-300">Horario
                                        Disponible:</span> L-V 10:00 - 18:00 (Placeholder)</p>
                            </div>

                            @if ($trainerItem->id === $assignedTrainer?->id)
                                <span
                                    class="mt-3 inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 shadow-sm">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    ASIGNADO
                                </span>
                            @elseif ($trainerItem->id === $requestedTrainer?->id && $status === 'pending')
                                <span
                                    class="mt-3 inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 shadow-sm animate-pulse">
                                    SOLICITUD PENDIENTE
                                </span>
                            @endif
                        </div>

                        {{-- Lógica de Botones Corregida --}}
                        <div class="mt-4 flex space-x-2">
                            @php
                                $isAssigned = $trainerItem->id === $assignedTrainer?->id;
                                $isRequested = $trainerItem->id === $requestedTrainer?->id && $status === 'pending';
                                $isRejected = $trainerItem->id === $requestedTrainer?->id && $status === 'rejected';
                                $trainerName = $trainerItem->name . ' ' . $trainerItem->last_name;

                                // Determinar si se mostrará un segundo botón (Cancelar o Desvincular)
                                $hasSecondaryButton = $isRequested || $isAssigned;

                                // Deshabilitar la acción de "Solicitar" si:
                                // 1. Ya está Asignado a este entrenador.
                                // 2. Ya tiene una Solicitud Pendiente a CUALQUIER otro entrenador (incluyendo a este).
                                $isDisabled = $isAssigned || ($status === 'pending' && !$isRequested);

                                // Clases dinámicas
                                $mainButtonWidth = $hasSecondaryButton ? 'w-1/2' : 'w-full';
                                $mainButtonClass = $isDisabled ? 'btn-outline-grey-disabled' : 'btn-outline-lime';
                            @endphp

                            {{-- 1. Botón de Acción Principal (Solicitar / Asignado / Pendiente) --}}
                            {{-- La clase de ancho ahora es dinámica ($mainButtonWidth) --}}
                            <button wire:click="openSelectionModal({{ $trainerItem->id }}, '{{ $trainerName }}')"
                                @if ($isDisabled) disabled @endif
                                class="{{ $mainButtonWidth }} {{ $mainButtonClass }}">

                                @if ($isAssigned)
                                    Actual Entrenador
                                @elseif ($isRequested)
                                    Solicitud Pendiente
                                @else
                                    Solicitar Entrenador
                                @endif
                            </button>

                            {{-- 2. Botón CANCELAR SOLICITUD (Solo si este entrenador es el solicitado y el estado es pending) --}}
                            @if ($isRequested)
                                {{-- Usamos w-1/2 para que comparta el espacio --}}
                                <button wire:click="openDetachModal" class="btn-outline-red w-1/2">
                                    Cancelar Solicitud
                                </button>
                            @endif

                            {{-- 3. Botón DESVINCULAR (Solo si este entrenador es el asignado) --}}
                            @if ($isAssigned)
                                {{-- Usamos w-1/2 para que comparta el espacio --}}
                                <button wire:click="openDetachModal" class="btn-outline-red w-1/2">
                                    Desvincular
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                        <p class="text-gray-500 dark:text-gray-400">No se encontraron entrenadores que coincidan con la
                            búsqueda.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $trainers->links() }}
            </div>

            {{-- MODAL 1: CONFIRMACIÓN DE SELECCIÓN (LIVEWIRE CONTROLADO) --}}
            {{-- <div x-data="{ show: @entangle('showSelectionModal'), selectedTrainerName: '' }" x-show="show" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                style="display: none;">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="show" @click="show = false" wire:click="closeSelectionModal"
                        x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="bg-modal"
                        aria-hidden="true">
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="show" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-70">

                        <div class="card-tb-ve-v2">
                            <svg class="mx-auto h-12 w-12 text-indigo-600 dark:text-indigo-400"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path d="M22 13h-4" />
                                <path d="m15 16-3-3 3-3" />
                            </svg>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-3 text-center"
                                id="selection-title">
                                Confirmar Solicitud</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 mb-6 text-center">
                                Estás a punto de enviar una solicitud para que <span class="font-bold"
                                    x-text="selectedTrainerName"></span> te entrene. Ellos deberán aceptar la solicitud.
                            </p>

                            <div class="flex justify-end space-x-3">
                                <button wire:click="closeSelectionModal" type="button" class="btn-outline-red">
                                    Cancelar
                                </button>
                                <button wire:click="selectTrainer" type="button" class="btn-outline-lime">
                                    Enviar Solicitud
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- ❌ ELIMINAMOS MODAL 2: CONFIRMACIÓN DE DESVINCULACIÓN (CONTROLADO LOCALMENTE) --}}
            {{-- Ahora usamos el Modal Global a través de wire:click="openDetachModal" --}}

        </div>
    </div>
</div>
