<div>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session()->has('success'))
                <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-green-800 dark:text-green-200" role="alert">
                    <span class="font-medium">Éxito:</span> {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-red-800 dark:text-red-200" role="alert">
                    <span class="font-medium">Error:</span> {{ session('error') }}
                </div>
            @endif

            <div class="bg-white/90 dark:bg-[#1a1a1a]/95 shadow-2xl sm:rounded-xl p-6 md:p-10">
                
                <header class="mb-8 border-b dark:border-gray-700 pb-4 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-[#7bcb01]">
                            Arma tu Rutina
                        </h1>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            Selecciona los ejercicios que deseas incluir en tu plan de entrenamiento.
                        </p>
                    </div>
                    
                    @if ($currentView === 'selector')
                        <button wire:click="changeView('routine')"
                            class="px-4 py-2 bg-lime-600 text-white font-semibold rounded-lg shadow-md hover:bg-lime-700 transition duration-150 disabled:opacity-50"
                            {{ count($this->selectedRoutineIds) === 0 ? 'disabled' : '' }}
                        >
                            Ver Rutina ({{ count($this->selectedRoutineIds) }})
                        </button>
                    @else
                        <button wire:click="changeView('selector')"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg shadow-md hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150"
                        >
                            Volver al Selector
                        </button>
                    @endif
                </header>

                @if ($currentView === 'selector')
                    <div class="space-y-10">
                        <div class="mb-6">
                            <flux:input
                                wire:model.live.debounce.300ms="searchQuery"
                                type="text"
                                placeholder="Buscar ejercicio por nombre..."
                                class:input="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                            />
                        </div>
                            
                        @if ($this->filteredExercises->isEmpty())
                            <div class="text-center p-10 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-xl text-gray-500 dark:text-gray-400 font-medium">
                                    No se encontraron ejercicios
                                </p>
                                <p class="text-gray-400 dark:text-gray-500 mt-2">
                                    Ajusta tu búsqueda o selecciona otro grupo muscular.
                                </p>
                            </div>
                        @else
                            @foreach ($muscleGroupsMap as $tren => $groups)
                                <section 
                                    @if ($this->filteredExercises->keys()->intersect($groups)->isEmpty())
                                        class="hidden" 
                                    @endif
                                    class="border-b dark:border-gray-700 pb-6"
                                >
                                    <h2 class="text-2xl font-bold text-gray-600 dark:text-white mb-5">
                                        {{ $tren }}
                                    </h2>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        @foreach ($groups as $group)
                                            @php
                                                $exercisesInGroup = $this->filteredExercises[Str::lower($group)] ?? collect();
                                            @endphp

                                            @if ($exercisesInGroup->isNotEmpty())
                                                <div class="bg-gray-100 dark:bg-[#3f3f46] p-4 rounded-xl shadow-inner">
                                                    <h3 class="text-xl font-semibold capitalize text-gray-900 dark:text-white mb-3 border-b dark:border-gray-600 pb-2">
                                                        {{ Str::ucfirst($group) }}
                                                    </h3>

                                                    <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                                                        @foreach ($exercisesInGroup as $exercise)
                                                            <div class="flex items-center justify-between p-2 rounded-lg transition duration-150 
                                                                {{ in_array($exercise->id, $this->selectedRoutineIds) 
                                                                    ? 'bg-lime-200 dark:bg-lime-900 border border-lime-500' 
                                                                    : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                                                
                                                                <div class="flex items-center space-x-3">
                                                                    
                                                                    @if ($exercise->image_path ?? false)
                                                                        <img src="{{ $exercise->image_path }}" alt="{{ $exercise->name }}" class="w-24 h-24 rounded object-cover flex-shrink-0 border border-gray-300 dark:border-gray-600">
                                                                    @else
                                                                        {{-- Icono de Fallback --}}
                                                                        <svg class="w-24 h-24 p-1 text-lime-600 dark:text-lime-400 flex-shrink-0 bg-gray-200 dark:bg-gray-700 rounded-full" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.588-.388a.375.375 0 1 0 0 .75.375.375 1 0 0-.75ZM18.75 7.5h.008v.008h-.008V7.5ZM12 7.5a.75.75 0 0 1 .75-.75h.008v.008H12a.75.75 0 0 1-.75.75Z" /></svg>
                                                                    @endif

                                                                    <input 
                                                                        type="checkbox" 
                                                                        wire:click="toggleExercise({{ $exercise->id }})" 
                                                                        id="exercise-{{ $exercise->id }}"
                                                                        class="form-checkbox h-5 w-5 !text-lime-600 rounded !border-gray-300 focus:!ring-lime-500 focus:!border-lime-500"
                                                                        {{ in_array($exercise->id, $this->selectedRoutineIds) ? 'checked' : '' }}
                                                                    >
                                                                    <label for="exercise-{{ $exercise->id }}" class="text-gray-900 dark:text-gray-100 cursor-pointer">
                                                                        {{ $exercise->name }}
                                                                    </label>
                                                                </div>

                                                                <button wire:click="showExerciseDetails({{ $exercise->id }})"
                                                                    class="text-gray-500 hover:text-lime-600 dark:hover:text-lime-400 p-1 rounded-full transition"
                                                                    title="Ver instrucciones">
                                                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
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
                            <label for="routineName" class="block text-xl font-bold text-gray-900 dark:text-white mb-2">
                                Dale un Nombre a tu Rutina
                            </label>
                            <input 
                                wire:model.live="routineName" 
                                id="routineName" 
                                type="text" 
                                placeholder="Ej. Rutina Full Body Lunes"
                                class="block w-full max-w-lg px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-[#1a1a1a]/95 dark:text-white transition duration-150"
                            >
                            @error('routineName') 
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p> 
                            @enderror
                        </div>

                        <div class=" p-0 rounded-xl">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                Resumen de Ejercicios ({{ count($this->selectedRoutineIds) }})
                            </h2>
                            
                            @if ($selectedExercisesDetails->isEmpty())
                                <p class="text-gray-500 italic p-4 border dark:border-gray-700 rounded-lg">
                                    No se han cargado los detalles. Vuelve al selector y asegúrate de que haya ejercicios seleccionados.
                                </p>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($selectedExercisesDetails as $exercise)
                                        <div class="p-4 border dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 shadow-sm flex flex-col justify-between">
                                            <div>
                                                <div class="text-lg font-bold text-lime-600 dark:text-lime-400">
                                                    {{ $exercise->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400 capitalize">
                                                    Grupo: {{ $exercise->muscle_group }}
                                                </div>
                                            </div>

                                            {{-- SECCIÓN: Definición de Sets, Reps y KG --}}
                                            <div class="mt-4 border-t pt-4 dark:border-gray-700 space-y-3">
                                            <h4 class="text-base font-bold text-gray-900 dark:text-white">Sets</h4>
                                            
                                            {{-- Recorremos la estructura de sets para el ejercicio actual --}}
                                            @forelse ($routineData[$exercise->id] ?? [] as $setIndex => $set)
                                                <div class="flex items-start space-x-2 bg-gray-50 dark:bg-gray-900 p-2 rounded-lg">
                                                    
                                                    <div class="flex flex-col w-full space-y-1">
                                                        
                                                        {{-- 🎯 FIX: Se eliminó el campo 'sets' (Sets/Series) ya que no existe en el array $routineData --}}
                                                        <div class="flex items-center space-x-2 text-sm w-full"> 
    
                                                            {{-- 1. Indicador del Set (No se encoge) --}}
                                                            <span class="font-semibold text-lime-500 whitespace-nowrap shrink-0">Set {{ $setIndex + 1 }}:</span>

                                                            {{-- 2. Contenedor de Inputs (OCUPA TODO EL ESPACIO RESTANTE) --}}
                                                            {{-- Usamos flex-grow/flex-1 y space-x-2 para distribuir los dos inputs --}}
                                                            <div class="flex flex-1 space-x-2"> 
                                                                
                                                                {{-- Contenedor para Repeticiones (Se queda con 1/2 del espacio flexible) --}}
                                                                {{-- Usamos flex-1 para que ocupe la mitad del espacio disponible --}}
                                                                <div class="flex items-center flex-1"> 
                                                                    <label for="reps-{{ $exercise->id }}-{{ $setIndex }}" class="text-xs font-medium text-gray-700 dark:text-gray-300 mr-1 whitespace-nowrap">Reps:</label> 
                                                                    <input 
                                                                        wire:model.live="routineData.{{ $exercise->id }}.{{ $setIndex }}.reps" 
                                                                        id="reps-{{ $exercise->id }}-{{ $setIndex }}"
                                                                        type="number" 
                                                                        placeholder="Nº"
                                                                        min="1"
                                                                        class="flex-grow px-1 py-0.5 text-xs border-gray-300 dark:border-gray-600 rounded focus:ring-lime-500 focus:border-lime-500 dark:bg-[#1a1a1a]/95 dark:text-white remove-number-arrows w-[5px]"
                                                                        title="Repeticiones"
                                                                    >
                                                                </div>
                                                                
                                                                {{-- Contenedor para Kilogramos (Se queda con 1/2 del espacio flexible) --}}
                                                                {{-- Usamos flex-1 para que ocupe la otra mitad del espacio disponible --}}
                                                                <div class="flex items-center flex-1">
                                                                    <label for="kg-{{ $exercise->id }}-{{ $setIndex }}" class="text-xs font-medium text-gray-700 dark:text-gray-300 mr-1 whitespace-nowrap">KG:</label> 
                                                                    <input 
                                                                        wire:model.live="routineData.{{ $exercise->id }}.{{ $setIndex }}.kg" 
                                                                        id="kg-{{ $exercise->id }}-{{ $setIndex }}"
                                                                        type="number" 
                                                                        placeholder="Peso"
                                                                        min="0"
                                                                        step="0.5"
                                                                        class="flex-grow px-1 py-0.5 text-xs border-gray-300 dark:border-gray-600 rounded focus:ring-lime-500 focus:border-lime-500 dark:bg-[#1a1a1a]/95 dark:text-white remove-number-arrows w-[5px]"
                                                                        title="Kilogramos"
                                                                    >
                                                                </div>
                                                            </div>
                                                            
                                                            {{-- 3. Botón de Eliminar (No se encoge ni se estira, se queda al final) --}}
                                                            <button type="button" 
                                                                    wire:click="removeSet({{ $exercise->id }}, {{ $setIndex }})" 
                                                                    class="text-red-500 hover:text-red-700 p-1 rounded-full text-lg leading-none transition duration-150 ease-in-out shrink-0">
                                                                &times; 
                                                            </button>
                                                        </div>
                                                        {{-- Muestra de errores de validación para los campos anidados --}}
                                                        <div class="flex space-x-2 justify-between">
                                                            {{-- 🎯 FIX: Se eliminó el error para 'sets' --}}
                                                            @error("routineData.{$exercise->id}.{$setIndex}.reps") <p class="text-xs text-red-500 -mt-1">R: {{ $message }}</p> @enderror
                                                            @error("routineData.{$exercise->id}.{$setIndex}.kg") <p class="text-xs text-red-500 -mt-1">K: {{ $message }}</p> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-sm text-gray-500 italic">No hay sets definidos. Añade uno.</p>
                                            @endforelse

                                            <button wire:click="addSet({{ $exercise->id }})"
                                                class="text-xs px-2 py-1 bg-lime-100 dark:bg-lime-800 text-lime-700 dark:text-lime-200 rounded hover:bg-lime-200 dark:hover:bg-lime-700 transition w-full mt-2">
                                                + Añadir Set
                                            </button>
                                        </div>
                                            {{-- FIN: SECCIÓN --}}

                                            <div class="mt-3 flex space-x-2">
                                                <button wire:click="showExerciseDetails({{ $exercise->id }})"
                                                    class="text-xs px-2 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                                                    Instrucciones
                                                </button>
                                                <button wire:click="toggleExercise({{ $exercise->id }})"
                                                    class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                                    Quitar
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="pt-6 border-t dark:border-gray-700">
                            <button wire:click="saveRoutine"
                                wire:loading.attr="disabled"
                                wire:target="saveRoutine"
                                class="w-full relative py-3 px-4 border border-transparent rounded-lg shadow-md text-base font-bold text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-4 focus:ring-lime-500 focus:ring-opacity-50 transition duration-150 ease-in-out disabled:opacity-50"
                                {{ empty($routineName) || count($this->selectedRoutineIds) === 0 ? 'disabled' : '' }}
                            >
                                <span wire:loading.remove.delay wire:target="saveRoutine">
                                    Guardar Rutina ({{ count($this->selectedRoutineIds) }} Ejercicios)
                                </span>
                                <span wire:loading.delay wire:target="saveRoutine" class="flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
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
            </div>
            
            @if ($showModal && $selectedExerciseDetails)
                <div 
                    x-data="{ open: @entangle('showModal').live }" 
                    x-show="open" 
                    x-transition.opacity.scale.80 
                    x-cloak 
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
                >
                    <div 
                        x-show="open" 
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        @click.away="open = false" 
                        class="bg-white dark:bg-[#1a1a1a] rounded-xl shadow-2xl p-6 max-w-xl w-full transform transition-all duration-300 text-left" 
                    >
                        <div class="border-b dark:border-gray-700 pb-3 mb-4">
                            <h3 class="text-2xl font-bold leading-6 text-gray-900 dark:text-white">
                                Instrucciones: {{ $selectedExerciseDetails->name }}
                            </h3>
                        </div>
                        
                        {{-- CONTENIDO DEL MODAL --}}
                        <div class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                            
                            {{-- GIF (gif_path) --}}
                            @if ($selectedExerciseDetails->gif_path ?? false)
                                <div class="w-full max-h-64 overflow-hidden rounded-xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center shadow-lg border-4 border-lime-600/50">
                                    {{-- USAMOS gif_path --}}
                                    <img src="{{ $selectedExerciseDetails->gif_path }}" 
                                        alt="GIF de {{ $selectedExerciseDetails->name }}" 
                                        class="w-full h-auto object-contain max-h-64"
                                    >
                                </div>
                            @endif

                            <p class="text-sm font-semibold text-[#7bcb01] capitalize"> 
                                Grupo Muscular: {{ $selectedExerciseDetails->muscle_group }}
                            </p>
                            
                            {{-- SECCIÓN PREPARACIÓN (Ahora usa description - resumen) --}}
                            <div class="border-t pt-4 dark:border-gray-700">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Preparación (Resumen)
                                </h4>
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                    {{ $selectedExerciseDetails->description }}
                                </p>
                            </div>

                            {{-- SECCIÓN EJECUCIÓN (Ahora usa instructions - pasos) --}}
                            <div class="border-t pt-4 dark:border-gray-700">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Ejecución
                                </h4>
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                    {{ $selectedExerciseDetails->instructions }}
                                </p>
                            </div>

                            {{-- SECCIÓN CONSEJOS CLAVES (Ahora usa tips) --}}
                            <div class="border-t pt-4 dark:border-gray-700">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                    Consejos Claves
                                </h4>
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                    {{ $selectedExerciseDetails->tips }}
                                </p>
                            </div>

                        </div>
                        {{-- FIN: CONTENIDO DEL MODAL --}}

                        <div class="mt-6 pt-4 border-t dark:border-gray-700 flex justify-end">
                            <button wire:click="closeModal" 
                                @click="open = false"
                                type="button" 
                                class="bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                                Entendido / Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>
    <style>
        .remove-number-arrows::-webkit-outer-spin-button,
    .remove-number-arrows::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .remove-number-arrows[type=number] {
        -moz-appearance: textfield; /* Firefox */
    }

    </style>
</div>