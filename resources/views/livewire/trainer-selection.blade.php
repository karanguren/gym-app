<div class="p-6 sm:px-10 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 min-h-screen" 
    x-data="{ 
        showToast: false, 
        toastMessage: '', 
        toastType: 'success',
        showSelectionModal: false,
        selectedTrainerId: null,
        selectedTrainerName: '',
        
        openModal(trainerId, trainerName) {
            this.selectedTrainerId = trainerId;
            this.selectedTrainerName = trainerName;
            this.showSelectionModal = true;
        },
        
        // CORRECCIÓN CLAVE: Llama directamente al método Livewire (@this)
        confirmSelection() {
            if (this.selectedTrainerId) {
                // Llama al método Livewire 'selectTrainer' con el ID.
                // Usamos @this.call para asegurar la ejecución del método del componente.
                @this.call('selectTrainer', this.selectedTrainerId);
                this.showSelectionModal = false;
            }
        }
    }"
    x-init="@this.on('show-toast', (event) => {
        toastMessage = event[0].message;
        toastType = event[0].type || 'success';
        showToast = true;
        setTimeout(() => showToast = false, 3500);
    })">

    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-8">Selecciona tu Entrenador Personal</h1>

    {{-- Estado de Asignación Actual --}}
    <div class="mb-8 p-6 rounded-xl shadow-lg 
        @if ($clientProfile && $clientProfile->assigned_trainer_id)
            bg-indigo-100 dark:bg-indigo-900/50 border-2 border-indigo-400
        @else
            bg-yellow-50 dark:bg-yellow-900/50 border-2 border-yellow-400
        @endif">
        <h2 class="text-xl font-semibold 
            @if ($clientProfile && $clientProfile->assigned_trainer_id) text-indigo-800 dark:text-indigo-200 @else text-yellow-800 dark:text-yellow-200 @endif
            mb-2 flex items-center">
            <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 0 0-9.09 5.86c0 1.25.7 2.45 1.5 3.55 1.7 2.3 3.5 4.1 3.5 5.6V20"/><path d="M18 13.08a10 10 0 0 0-6-1.58"/><path d="M10 20v-2.5c0-.8.7-1.5 1.5-1.5h1.5"/></svg>
            Estado Actual
        </h2>
        
        @if ($clientProfile && $clientProfile->assigned_trainer_id)
            <p class="text-lg text-indigo-700 dark:text-indigo-100">
                Tu entrenador asignado es: 
                <span class="font-bold">{{ $clientProfile->assignedTrainer->name ?? 'Entrenador Desconocido' }}</span>.
            </p>
            <button 
                wire:click="unselectTrainer"
                wire:confirm="¿Estás seguro de que quieres desvincularte de tu entrenador actual?"
                class="mt-3 px-4 py-2 text-sm font-medium rounded-lg text-indigo-700 bg-indigo-200 hover:bg-indigo-300 transition duration-150 dark:bg-indigo-700 dark:hover:bg-indigo-600 dark:text-white"
            >
                Desvincular
            </button>
        @else
            <p class="text-lg text-yellow-700 dark:text-yellow-100">
                Actualmente no tienes un entrenador personal asignado. ¡Busca uno a continuación!
            </p>
        @endif
    </div>

    {{-- ENTRENAMIENTOS ASIGNADOS --}}
    @if ($clientProfile && $clientProfile->assigned_trainer_id && isset($clientRoutines))
        <div class="mb-8 p-6 rounded-xl shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-lime-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M12 18V12"/><path d="M9 15h6"/></svg>
                Entrenamientos Asignados por {{ $clientProfile->assignedTrainer->name ?? 'tu Entrenador' }}
            </h2>

            @forelse ($clientRoutines as $routine)
                <div wire:key="routine-{{ $routine->id }}" class="flex justify-between items-center p-3 mb-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-100 dark:border-gray-600">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $routine->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $routine->exercises_count }} Ejercicios - Creada por: {{ $routine->creator->name ?? 'Desconocido' }}
                        </p>
                    </div>
                    <a href="#" {{-- Reemplazar con route('routine.workout', $routine->id) --}}
                       class="px-3 py-1 text-sm font-medium rounded-lg text-white bg-lime-600 hover:bg-lime-700 transition duration-150 shadow-md">
                        Iniciar Rutina
                    </a>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400 italic">
                    Tu entrenador no ha asignado ninguna rutina activa todavía.
                </p>
            @endforelse
        </div>
    @endif
    {{-- FIN: ENTRENAMIENTOS ASIGNADOS --}}


    {{-- Buscador de Entrenadores --}}
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Buscar Entrenadores Disponibles</h2>
        <input 
            wire:model.live.debounce.300ms="search" 
            type="search" 
            placeholder="Buscar por nombre o correo electrónico..."
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition duration-150"
        >
    </div>

    {{-- Lista de Entrenadores en Grid Responsiva --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse ($trainers as $trainer)
            <div wire:key="trainer-{{ $trainer->id }}" 
                 class="flex flex-col p-4 bg-white dark:bg-gray-800 rounded-xl shadow-md border 
                        @if ($clientProfile && $clientProfile->assigned_trainer_id === $trainer->id) 
                            border-4 border-indigo-500 
                        @else 
                            border-gray-100 dark:border-gray-700 hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 
                        @endif 
                        transition duration-200 h-full">
                
                {{-- Contenido del Card --}}
                <div class="flex-grow">
                    <div class="flex items-center space-x-3 mb-3">
                        {{-- Icono o Avatar Placeholder --}}
                        <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ substr($trainer->name, 0, 1) }}
                        </div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $trainer->name }}</p>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $trainer->email }}</p>

                    {{-- Añadir algo de descripción o especialidad si existiera --}}
                    <p class="text-xs text-gray-600 dark:text-gray-300">
                        Especialidad: Fitness y Pesas.
                    </p>
                </div>
                
                {{-- Botón y Estado --}}
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    @if ($clientProfile && $clientProfile->assigned_trainer_id === $trainer->id)
                        <span class="w-full text-center block px-3 py-1 text-sm font-medium rounded-lg text-indigo-700 bg-indigo-100 dark:bg-indigo-700 dark:text-indigo-100">
                            ASIGNADO
                        </span>
                    @else
                        {{-- Abre el modal con la información del entrenador --}}
                        <button
                            @click="openModal({{ $trainer->id }}, '{{ $trainer->name }}')"
                            class="w-full px-4 py-2 text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 shadow-md"
                        >
                            Seleccionar
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500 italic p-4 bg-white dark:bg-gray-800 rounded-lg col-span-full">
                No se encontraron entrenadores disponibles que coincidan con la búsqueda.
            </p>
        @endforelse
    </div>

    {{-- Paginación --}}
    <div class="mt-6">
        {{ $trainers->links('livewire::tailwind') }} 
    </div>

    {{-- Notificación Toast (Alpine.js) --}}
    <div x-show="showToast" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="fixed bottom-5 right-5 z-50 p-4 rounded-xl shadow-2xl transition-all duration-300 w-80"
        :class="{
            'bg-green-500 text-white': toastType === 'success',
            'bg-red-600 text-white': toastType === 'error',
            'bg-blue-500 text-white': toastType === 'info',
        }">
        <div class="flex items-center space-x-3">
            <span class="font-semibold" x-text="toastMessage"></span>
        </div>
    </div>
    
    {{-- MODAL DE CONFIRMACIÓN DE SELECCIÓN DE ENTRENADOR --}}
    <div x-show="showSelectionModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 p-4" 
         @click.self="showSelectionModal = false" x-cloak>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm" role="dialog" aria-modal="true" aria-labelledby="selection-title">
            <div class="p-6">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-indigo-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 13h-4"/><path d="m15 16-3-3 3-3"/></svg>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-3" id="selection-title">Confirmar Entrenador</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 mb-6">
                        ¿Estás seguro de que deseas seleccionar a <span class="font-bold" x-text="selectedTrainerName"></span> como tu entrenador personal?
                    </p>
                    
                    <div class="flex justify-end space-x-3">
                        <button @click="showSelectionModal = false" 
                                type="button" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300">
                            Cancelar
                        </button>
                        <button @click="confirmSelection()"
                                type="button" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                            Sí, Seleccionar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
