<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8" x-data="{
        showFinalizeModal: false,
        toast: { show: false, message: '', type: 'success' },
    
        showToast(payload) {
            const data = Array.isArray(payload) && payload.length > 0 ? payload[0] : payload;
    
            if (data && data.message) {
                this.toast.message = data.message;
                this.toast.type = data.type || 'success';
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3000);
            }
        }
    }" x-init="@this.on('show-toast', (data) => showToast(data))" x-cloak>
        <div class="md:p-8">

            @if (session()->has('success') || session()->has('error') || session()->has('warning'))
                <div class="p-4 mb-4 text-sm rounded-lg 
                    @if (session()->has('success')) text-green-800 bg-green-50 dark:bg-green-900/50 dark:text-green-300 @endif
                    @if (session()->has('error')) text-red-800 bg-red-50 dark:bg-red-900/50 dark:text-red-300 @endif
                    @if (session()->has('warning')) text-yellow-800 bg-yellow-50 dark:bg-yellow-900/50 dark:text-yellow-300 @endif"
                    role="alert">
                    {{ session('success') ?? (session('error') ?? session('warning')) }}
                </div>
            @endif

            <header
                class="mb-8 border-b dark:border-gray-700 pb-4 flex flex-col sm:flex-row justify-between sm:items-center space-y-3 sm:space-y-0">
                <div>
                    <h1 class="titles">
                        Arma tu Rutina
                    </h1>
                    <p class="description">
                        Selecciona los ejercicios que deseas incluir en tu plan de entrenamiento.
                    </p>
                </div>

                <div class="w-full sm:w-auto">
                    @if ($currentView === 'selector')
                        <button wire:click="changeView('routine')" class="w-full sm:w-auto btn-outline-ve"
                            {{ count($this->selectedRoutineIds) === 0 ? 'disabled' : '' }}>
                            Ver Rutina ({{ count($this->selectedRoutineIds) }})
                        </button>
                    @else
                        <button wire:click="changeView('selector')" class="w-full sm:w-auto btn-outline-ve">
                            Volver al Selector
                        </button>
                    @endif
                </div>
            </header>

            @if ($currentView === 'selector')
                <div class="space-y-10">
                    <div class="mb-6">
                        <flux:input wire:model.live.debounce.300ms="searchQuery" type="text"
                            placeholder="Buscar ejercicio por nombre..." class:input="!w-full inputs" />
                    </div>

                    @if ($this->filteredExercises->isEmpty())
                        <div
                            class="text-center p-10 bg-gray-100 dark:bg-[#1a1a1a] rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                            <svg class="mx-auto h-12 w-12 svg-ve" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <path d="M12 16v-4" />
                                <path d="M12 8h.01" />
                            </svg>
                            <p class="text-xl font-semibold text-gray-700 dark:text-gray-300">
                                No se encontraron ejercicios
                            </p>
                            <p class="text-gray-400 dark:text-gray-500 mt-2">
                                Ajusta tu búsqueda
                            </p>
                        </div>
                    @else
                        @foreach ($muscleGroupsMap as $tren => $groups)
                            <section @if ($this->filteredExercises->keys()->intersect($groups)->isEmpty()) class="hidden" @endif
                                class="border-b dark:border-gray-700 pb-6">
                                <h2 class="subtitles">
                                    {{ $tren }}
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    @foreach ($groups as $group)
                                        @php
                                            $exercisesInGroup =
                                                $this->filteredExercises[Str::lower($group)] ?? collect();
                                        @endphp

                                        @if ($exercisesInGroup->isNotEmpty())
                                            @foreach ($exercisesInGroup as $exercise)
                                                <div
                                                    class="flex items-center justify-between p-2 rounded-lg transition duration-150 flex-nowrap
                                                {{ in_array($exercise->id, $this->selectedRoutineIds)
                                                    ? 'bg-lime-200 dark:bg-lime-900 border border-lime-500'
                                                    : 'card-tl-ve ' }}">

                                                    <div class="flex items-center space-x-3 flex-grow min-w-0">

                                                        <button wire:click="showExerciseDetails({{ $exercise->id }})"
                                                            class="flex-shrink-0 rounded-lg overflow-hidden transition duration-150 transform focus:outline-none focus:ring-2 focus:ring-lime-500/70"
                                                            title="Ver instrucciones de {{ $exercise->name }}"
                                                            type="button">
                                                            @if ($exercise->image_path ?? false)
                                                                {{-- Se mantiene el w-16 para mejor visualización en móvil --}}
                                                                <img src="{{ $exercise->image_path }}"
                                                                    alt="{{ $exercise->name }}"
                                                                    class="w-16 h-16 rounded object-cover flex-shrink-0 border border-gray-300 dark:border-gray-600">
                                                            @else
                                                                {{-- Icono de Fallback (Ancho fijo) --}}
                                                                <svg class="w-16 h-16 p-1 text-lime-600 dark:text-lime-400 flex-shrink-0 bg-gray-200 dark:bg-gray-700 rounded-lg"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24" stroke-width="1.5"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.588-.388a.375.375 0 1 0 0 .75.375.375 1 0 0-.75ZM18.75 7.5h.008v.008h-.008V7.5ZM12 7.5a.75.75 0 0 1 .75-.75h.008v.008H12a.75.75 0 0 1-.75.75Z" />
                                                                </svg>
                                                            @endif
                                                        </button>

                                                        <div class="flex items-start space-x-2 flex-grow min-w-0">
                                                            <input type="checkbox"
                                                                wire:click="toggleExercise({{ $exercise->id }})"
                                                                id="exercise-{{ $exercise->id }}"
                                                                class="form-checkbox h-5 w-5 !text-lime-600 rounded !border-gray-300 focus:!ring-lime-500 focus:!border-lime-500 mt-0.5 flex-shrink-0"
                                                                {{ in_array($exercise->id, $this->selectedRoutineIds) ? 'checked' : '' }}>
                                                            <label for="exercise-{{ $exercise->id }}"
                                                                class="text-gray-900 dark:text-gray-100 cursor-pointer block leading-snug text-base flex-grow min-w-0 select-none">

                                                                {{-- Nombre del Ejercicio --}}
                                                                <span class="block font-medium truncate">
                                                                    {{ $exercise->name }}
                                                                </span>

                                                                {{-- **NUEVO:** Grupo Muscular Debajo del Nombre --}}
                                                                <span
                                                                    class="block text-xs font-normal text-gray-500 dark:text-gray-400 capitalize mt-0.5">
                                                                    {{ Str::ucfirst($group) }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="flex-shrink-0 ml-2">
                                                        {{-- Espacio para posibles botones de acción adicionales --}}
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    @endif
                </div>
            @endif

            @if ($currentView === 'routine')
                <div class="space-y-8">

                    <div class=" rounded-xl ">
                        <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Dale un Nombre a tu Rutina
                        </flux:label>
                        <flux:input wire:model.live="routineName" id="routineName"
                            placeholder="Ej. Rutina Full Body Lunes" type="text" required
                            class="block w-full max-w-lg inputs" />
                        @error('routineName')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class=" p-0 rounded-xl">
                        <h2 class="subtitles">
                            Resumen de Ejercicios ({{ count($this->selectedRoutineIds) }})
                        </h2>

                        @if ($selectedExercisesDetails->isEmpty())
                            <p class="text-gray-500 italic p-4 border dark:border-gray-700 rounded-lg">
                                No se han cargado los detalles. Vuelve al selector y asegúrate de que haya ejercicios
                                seleccionados.
                            </p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach ($selectedExercisesDetails as $exercise)
                                    <div class="card-tb-ve-v2">
                                        <div class="flex justify-between items-center w-full p-2">

                                            <div class="flex flex-col min-w-0 pr-4">
                                                <div
                                                    class="text-lg font-bold text-lime-600 dark:text-lime-400 truncate">
                                                    {{ $exercise->name }}
                                                </div>
                                                <div
                                                    class="text-sm text-gray-500 dark:text-gray-400 capitalize truncate">
                                                    Grupo: {{ $exercise->muscle_group }}
                                                </div>
                                            </div>

                                            <button type="button"
                                                wire:click="showExerciseDetails({{ $exercise->id }})"
                                                class="btn-outline-rounded-ve" title="Ver Instrucciones">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="mt-4 border-t pt-4 dark:border-gray-700 space-y-3">
                                            <h4 class="text-base font-bold text-gray-900 dark:text-white">Sets</h4>
                                            @forelse ($routineData[$exercise->id] ?? [] as $setIndex => $set)
                                                <div
                                                    class="flex flex-col w-full p-3 rounded-lg border border-gray-700/50 bg-[#262627] shadow-lg transition duration-300 ease-in-out hover:border-lime-500/50">

                                                    <div class="flex items-center space-x-2 w-full">

                                                        <span
                                                            class="text-sm font-bold text-base text-lime-400 whitespace-nowrap shrink-0 pr-1">
                                                            Set {{ $setIndex + 1 }} -
                                                        </span>

                                                        <div class="flex items-center space-x-2 flex-1 min-w-0">

                                                            <div class="flex items-center flex-1"> <label
                                                                    for="reps-{{ $exercise->id }}-{{ $setIndex }}"
                                                                    class="text-xs font-medium text-gray-300 mr-1 whitespace-nowrap">Reps</label>
                                                                <flux:input
                                                                    wire:model.live="routineData.{{ $exercise->id }}.{{ $setIndex }}.reps"
                                                                    id="reps-{{ $exercise->id }}-{{ $setIndex }}"
                                                                    placeholder="Nº" min="2" type="number"
                                                                    required class="w-full inputs" />
                                                            </div>

                                                            <div class="flex items-center flex-1"> <label
                                                                    for="kg-{{ $exercise->id }}-{{ $setIndex }}"
                                                                    class="text-xs font-medium text-gray-300 mr-1 whitespace-nowrap">KG</label>
                                                                <flux:input
                                                                    wire:model.live="routineData.{{ $exercise->id }}.{{ $setIndex }}.kg"
                                                                    id="kg-{{ $exercise->id }}-{{ $setIndex }}"
                                                                    placeholder="Peso" min="2" step="0.5"
                                                                    type="number" required class="w-full inputs" />
                                                            </div>
                                                        </div>

                                                        <button type="button"
                                                            wire:click="removeSet({{ $exercise->id }}, {{ $setIndex }})"
                                                            class="text-gray-400 hover:text-red-500 p-1 rounded-full text-xl leading-none transition duration-150 ease-in-out shrink-0"
                                                            title="Eliminar Serie">
                                                            &times;
                                                        </button>
                                                    </div>

                                                    <div class="flex space-x-4 justify-start pl-10 mt-1">
                                                        @error("routineData.{$exercise->id}.{$setIndex}.reps")
                                                            <p class="text-xs text-red-400/90">R: {{ $message }}</p>
                                                        @enderror
                                                        @error("routineData.{$exercise->id}.{$setIndex}.kg")
                                                            <p class="text-xs text-red-400/90">K: {{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-sm text-gray-500 italic">No hay sets definidos. Añade
                                                    uno.</p>
                                            @endforelse

                                            {{-- <button wire:click="addSet({{ $exercise->id }})"
                                                    class=" btn-outline-ve w-full mt-2">
                                                    + Añadir Set
                                                </button> --}}
                                        </div>

                                        <div class="mt-3 flex space-x-2 w-full">
                                            <button wire:click="addSet({{ $exercise->id }})"
                                                class="flex-1 py-1 btn-outline-ve">
                                                + Añadir Set
                                            </button>
                                            <button wire:click="toggleExercise({{ $exercise->id }})"
                                                class="flex-1 py-1  btn-outline-ro">
                                                Quitar Ejercicio
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="pt-6 border-t dark:border-gray-700">
                        <button wire:click="saveRoutine" wire:loading.attr="disabled" wire:target="saveRoutine"
                            class="w-full relative py-3 px-4 border border-transparent rounded-lg shadow-md text-base font-bold text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-4 focus:ring-lime-500 focus:ring-opacity-50 transition duration-150 ease-in-out disabled:opacity-50"
                            {{ empty($routineName) || count($this->selectedRoutineIds) === 0 ? 'disabled' : '' }}>
                            <span wire:loading.remove.delay wire:target="saveRoutine">
                                Guardar Rutina ({{ count($this->selectedRoutineIds) }} Ejercicios)
                            </span>
                            <span wire:loading.delay wire:target="saveRoutine"
                                class="flex items-center justify-center">
                                Guardando...
                            </span>
                        </button>
                        @error('routineData')
                            <p class="mt-2 text-sm text-red-500 text-center">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            @endif

            @if ($showModal && $selectedExerciseDetails)
                <div x-data="{ open: @entangle('showModal').live }" x-show="open" x-transition.opacity.scale.80 x-cloak
                    class="flex items-center justify-center p-4 sm:p-6 bg-modal">
                    <div x-show="open" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" @click.away="open = false"
                        class="modal-instrucciones">
                        <div class="border-b border-gray-700 pb-4 mb-4 flex items-center justify-between">
                            <h3 class="text-2xl font-extrabold leading-tight text-white">
                                <span class="text-lime-400">{{ $selectedExerciseDetails->name }}</span>
                            </h3>
                            <button @click="open = false"
                                class="text-gray-400 hover:text-white transition duration-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- CONTENIDO DEL MODAL --}}
                        <div class="mt-4 space-y-6 max-h-[65vh] overflow-y-auto pr-3 -mr-2 custom-scrollbar">

                            @if ($selectedExerciseDetails->gif_path ?? false)
                                <div
                                    class="w-full relative pb-[100%] overflow-hidden rounded-xl bg-gray-800 flex items-center justify-center shadow-lg border-2 border-lime-600">
                                    <img src="{{ $selectedExerciseDetails->gif_path }}"
                                        alt="GIF de {{ $selectedExerciseDetails->name }}"
                                        class="absolute inset-0 w-full h-full object-contain">
                                </div>
                            @endif

                            <p class="text-sm font-semibold text-lime-400 capitalize">
                                Grupo Muscular: <span
                                    class="font-normal text-gray-300">{{ $selectedExerciseDetails->muscle_group }}</span>
                            </p>

                            {{-- SECCIÓN PREPARACIÓN (Ahora usa description - resumen) --}}
                            <div class="border-t border-gray-700 pt-5">
                                <h4 class="text-xl font-bold text-white mb-2">
                                    Preparación (Resumen)
                                </h4>
                                <p class="text-gray-300 leading-relaxed">
                                    {{ $selectedExerciseDetails->description }}
                                </p>
                            </div>

                            {{-- SECCIÓN EJECUCIÓN (Ahora usa instructions - pasos) --}}
                            <div class="border-t border-gray-700 pt-5">
                                <h4 class="text-xl font-bold text-white mb-2">
                                    Ejecución
                                </h4>
                                <p class="text-gray-300 leading-relaxed">
                                    {{ $selectedExerciseDetails->instructions }}
                                </p>
                            </div>

                            {{-- SECCIÓN CONSEJOS CLAVES (Ahora usa tips) --}}
                            <div class="border-t border-gray-700 pt-5">
                                <h4 class="text-xl font-bold text-white mb-2">
                                    Consejos Claves
                                </h4>
                                <p class="text-gray-300 leading-relaxed">
                                    {{ $selectedExerciseDetails->tips }}
                                </p>
                            </div>

                        </div>
                        {{-- FIN: CONTENIDO DEL MODAL --}}

                        <div class="mt-8 pt-5 border-t border-gray-700 flex justify-end">
                            <button wire:click="closeModal" @click="open = false" type="button"
                                class="py-2 px-8 btn-outline-ve">
                                Entendido
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <div x-show="toast.show" x-cloak x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2" @click="toast.show = false" {{-- Clic para cerrar --}}
            class="fixed bottom-5 right-5 z-50 transform transition duration-300 cursor-pointer"
            :class="{
                'bg-green-600': toast.type === 'success',
                'bg-red-600': toast.type === 'error',
                'bg-blue-600': toast.type === 'info',
                'bg-yellow-600': toast.type === 'warning'
            }">
            <div
                class="max-w-xs w-full text-white p-4 rounded-lg shadow-xl font-semibold transform transition duration-300 cursor-pointer flex items-center space-x-3">

                {{-- Iconos --}}
                <svg x-show="toast.type === 'success'" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <svg x-show="toast.type === 'error' || toast.type === 'warning'" class="w-6 h-6"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <svg x-show="toast.type === 'info'" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="16" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>

                <span x-text="toast.message"></span>
            </div>
        </div>
    </div>
</div>
