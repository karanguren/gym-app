<div class="py-6 sm:py-12 bg-gray-50 dark:bg-gray-900">
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

    <div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-xl p-6 md:p-10">
        
        <header class="mb-8 border-b dark:border-gray-700 pb-4 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                    Arma tu Rutina
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Selecciona los ejercicios que deseas incluir en tu plan de entrenamiento.
                </p>
            </div>
            
            @if ($currentView === 'selector')
                <button wire:click="changeView('routine')"
                    class="px-4 py-2 bg-lime-600 text-white font-semibold rounded-lg shadow-md hover:bg-lime-700 transition duration-150 disabled:opacity-50"
                    {{ count($selectedRoutineIds) === 0 ? 'disabled' : '' }}
                >
                    Ver Rutina ({{ count($selectedRoutineIds) }})
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
                @foreach ($muscleGroupsMap as $tren => $groups)
                    <section class="border-b dark:border-gray-700 pb-6">
                        <h2 class="text-2xl font-bold text-lime-600 dark:text-lime-400 mb-5">
                            {{ $tren }}
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach ($groups as $group)
                                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-xl shadow-inner">
                                    <h3 class="text-xl font-semibold capitalize text-gray-900 dark:text-white mb-3 border-b dark:border-gray-600 pb-2">
                                        {{ Str::ucfirst($group) }}
                                    </h3>

                                    <div class="space-y-2 max-h-96 overflow-y-auto pr-2">
                                        @forelse ($exercises[Str::lower($group)] ?? [] as $exercise)
                                            <div class="flex items-center justify-between p-2 rounded-lg transition duration-150 
                                                {{ in_array($exercise->id, $selectedRoutineIds) 
                                                    ? 'bg-lime-200 dark:bg-lime-900 border border-lime-500' 
                                                    : 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                                
                                                <div class="flex items-center space-x-3">
                                                    <input 
                                                        type="checkbox" 
                                                        wire:click="toggleExercise({{ $exercise->id }})" 
                                                        id="exercise-{{ $exercise->id }}"
                                                        {{ in_array($exercise->id, $selectedRoutineIds) ? 'checked' : '' }}
                                                        class="form-checkbox h-5 w-5 text-lime-600 rounded border-gray-300 focus:ring-lime-500"
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
                                        @empty
                                            <p class="text-gray-500 italic">No hay ejercicios disponibles para {{ Str::ucfirst($group) }}.</p>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif

        @if ($currentView === 'routine')
            <div class="space-y-8">
                
                <div class="bg-gray-100 dark:bg-gray-700 p-6 rounded-xl shadow-md">
                    <label for="routineName" class="block text-xl font-bold text-gray-900 dark:text-white mb-2">
                        1. Dale un Nombre a tu Rutina
                    </label>
                    <input 
                        wire:model.live="routineName" 
                        id="routineName" 
                        type="text" 
                        placeholder="Ej. Rutina Full Body Lunes"
                        class="block w-full max-w-lg px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-lime-500 focus:border-lime-500 dark:bg-gray-800 dark:text-white transition duration-150"
                    >
                    @error('routineName') 
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="bg-white dark:bg-gray-800 p-0 rounded-xl">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                        2. Resumen de Ejercicios ({{ count($selectedRoutineIds) }})
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
                        {{ empty($routineName) || count($selectedRoutineIds) === 0 ? 'disabled' : '' }}
                    >
                        <span wire:loading.remove.delay wire:target="saveRoutine">
                            3. Guardar Rutina ({{ count($selectedRoutineIds) }} Ejercicios)
                        </span>
                        <span wire:loading.delay wire:target="saveRoutine" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                    @error('selectedRoutineIds') 
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
                
                <div class="mt-4 space-y-4">
                    @if ($selectedExerciseDetails->gif_url ?? false)
                        <div class="w-full max-h-60 overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shadow-inner">
                            <img src="{{ $selectedExerciseDetails->gif_url }}" 
                                 alt="GIF de {{ $selectedExerciseDetails->name }}" 
                                 class="w-full h-auto object-cover max-h-60"
                            >
                        </div>
                    @endif

                    <p class="text-sm font-semibold text-[#7bcb01] capitalize"> 
                        Grupo Muscular: {{ $selectedExerciseDetails->muscle_group }}
                    </p>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                        {{ $selectedExerciseDetails->instructions }}
                    </p>
                </div>

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