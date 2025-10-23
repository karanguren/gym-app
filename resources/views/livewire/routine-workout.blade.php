<div x-data="{ 
        // 1. Añadir estado para el modal de salida
        showExitModal: false 
    }" 
    @keydown.escape.window="showExitModal = false" 
    class="py-6 sm:py-12"
    
    wire:poll.1000ms.{{ $isRunning ? '' : 'off' }}="updateTimer"
>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if (session()->has('success') || session()->has('error') || session()->has('info') || session()->has('warning'))
            <div class="p-4 mb-4 text-sm rounded-lg 
                @if (session()->has('success')) text-green-800 bg-green-50 dark:bg-green-900/50 dark:text-green-300 @endif
                @if (session()->has('error')) text-red-800 bg-red-50 dark:bg-red-900/50 dark:text-red-300 @endif
                @if (session()->has('info')) text-blue-800 bg-blue-50 dark:bg-blue-900/50 dark:text-blue-300 @endif
                @if (session()->has('warning')) text-yellow-800 bg-yellow-50 dark:bg-yellow-900/50 dark:text-yellow-300 @endif" role="alert">
                {{ session('success') ?? session('error') ?? session('info') ?? session('warning') }}
            </div>
        @endif
        
        <a href="{{ route('client.routines') }}" class="text-lime-600 hover:text-lime-700 dark:text-lime-400 dark:hover:text-lime-300 transition duration-150 mb-4 inline-flex items-center text-sm font-medium">
             <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Volver a Rutinas
        </a>

        <div class="bg-white/90 dark:bg-[#1a1a1a]/95 shadow-2xl sm:rounded-xl p-6 md:p-10">
            
            <header class="mb-8 border-b dark:border-gray-700 pb-4 flex justify-between items-center">
                
                <div class="flex flex-col">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-[#7bcb01]">
                        Entrenamiento: {{ $routine->name }}
                    </h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400 text-sm italic">
                        Iniciado por: {{ $routine->user->name ?? 'Cliente' }}
                    </p>
                </div>
                
                {{-- CRONÓMETRO Y CONTROLES --}}
                <div class="flex items-center space-x-4">
                    <div class="text-3xl font-mono font-bold text-gray-800 dark:text-white">
                        {{ $this->formattedTime }}
                    </div>
                    
                    @if (!$isRunning)
                        <button wire:click="startTimer" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-lime-600 text-white font-semibold rounded-lg shadow-md hover:bg-lime-700 transition duration-150 disabled:opacity-50">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v18l14-9L5 3z"></path></svg>
                            Iniciar
                        </button>
                    @else
                        <button wire:click="pauseTimer" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-yellow-600 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-700 transition duration-150 disabled:opacity-50">
                            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pausar
                        </button>
                    @endif
                    
                    <button @click="showExitModal = true"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg shadow-md hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150">
                        Finalizar
                    </button>
                </div>
            </header>

            {{-- Lista de Ejercicios y Sets --}}
            <div class="space-y-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 border-b dark:border-gray-700 pb-2">
                    Ejercicios
                </h2>

                @foreach ($routine->routineExercises as $re)
                    @php
                        $sets = $workoutData[$re->id] ?? [];
                    @endphp

                    <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-lg border-l-4 border-lime-600/70">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-lime-400 mb-3 flex justify-between items-center">
                            <span>{{ $re->order }}. {{ $re->exercise->name }}</span>
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400 capitalize">
                                Grupo: {{ $re->exercise->muscle_group }}
                            </span>
                        </h3>

                        {{-- Contenedor de Sets --}}
                        <div class="space-y-2 border-t dark:border-gray-700 pt-3">
                            @forelse ($sets as $setIndex => $set)
                                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 p-3 rounded-lg transition duration-150 
                                     {{ $set['done'] ? 'bg-lime-100 dark:bg-lime-900/50 border border-lime-500/70' : 'bg-gray-50 dark:bg-gray-700/50' }}">
                                    
                                    {{-- Indicador de Set --}}
                                    <span class="font-bold w-full sm:w-16 flex-shrink-0 text-sm {{ $set['done'] ? 'text-lime-700 dark:text-lime-300' : 'text-gray-600 dark:text-gray-200' }}">
                                        Set {{ $setIndex + 1 }}
                                    </span>
                                    
                                    {{-- Repeticiones/Peso Planificado --}}
                                    <div class="flex-1 text-sm text-gray-600 dark:text-gray-300 hidden sm:block">
                                        Plan: 
                                        <span class="font-semibold">{{ $set['target_reps'] }}</span> Reps @ 
                                        <span class="font-semibold">{{ $set['target_kg'] }}</span> KG
                                    </div>
                                    
                                    {{-- Inputs de Resultado (Para Edición) --}}
                                    <div class="flex items-center space-x-3 flex-wrap">
                                        <label class="text-xs text-gray-500 dark:text-gray-400">
                                            Reps
                                            <input type="number" step="1" 
                                                wire:model.live="workoutData.{{ $re->id }}.{{ $setIndex }}.result_reps"
                                                class="remove-number-arrows w-16 p-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600 focus:ring-lime-500 focus:border-lime-500"
                                            >
                                        </label>
                                        <label class="text-xs text-gray-500 dark:text-gray-400">
                                            KG
                                            <input type="number" step="0.5" 
                                                wire:model.live="workoutData.{{ $re->id }}.{{ $setIndex }}.result_kg"
                                                class="remove-number-arrows w-16 p-1 text-sm border rounded dark:bg-gray-700 dark:border-gray-600 focus:ring-lime-500 focus:border-lime-500"
                                            >
                                        </label>
                                    </div>

                                    {{-- Botones de Acción --}}
                                    <div class="flex items-center space-x-2">
                                        
                                        <button wire:click="removeSet({{ $re->id }}, {{ $setIndex }})"
                                            class="text-red-500 hover:text-red-700 p-1 rounded-full transition"
                                            title="Eliminar set">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        
                                        {{-- Botón de Set Completado --}}
                                        <button wire:click="toggleSetCompleted({{ $re->id }}, {{ $setIndex }})"
                                            class="text-xs px-3 py-1 rounded font-semibold transition duration-150 
                                                   {{ $set['done'] ? 'bg-red-500 hover:bg-red-600 text-white' : 'bg-lime-500 hover:bg-lime-600 text-white' }}">
                                            {{ $set['done'] ? 'Deshacer' : 'Completado' }}
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 italic p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">No hay sets planificados para este ejercicio.</p>
                            @endforelse
                        </div>
                        
                        {{-- Botón para Añadir Set --}}
                        <div class="mt-4">
                            <button wire:click="addSet({{ $re->id }})"
                                class="text-xs px-3 py-1 border border-lime-300 bg-lime-100 dark:bg-lime-800 text-lime-700 dark:text-lime-200 rounded hover:bg-lime-200 dark:hover:bg-lime-700 transition font-medium">
                                + Añadir Set Extra
                            </button>
                        </div>
                        
                    </div>
                @endforeach
            </div>
            
        </div>
        
        {{-- 4. MODAL DE CONFIRMACIÓN DE SALIDA --}}
        <div x-show="showExitModal" x-transition.opacity x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div x-show="showExitModal" x-transition.scale.80
                class="bg-white dark:bg-[#1a1a1a] rounded-xl shadow-2xl p-6 max-w-sm w-full">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    ¿Finalizar Entrenamiento?
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    El tiempo registrado es de **{{ $this->formattedTime }}**. ¿Deseas finalizar y guardar (o salir sin guardar)?
                </p>
                <div class="flex justify-end space-x-3">
                    <button @click="showExitModal = false"
                        class="py-2 px-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Cancelar
                    </button>
                    <a href="{{ route('client.routines') }}"
                       class="py-2 px-4 text-gray-700 dark:text-gray-300 rounded-lg transition border border-red-300 hover:bg-red-100 dark:hover:bg-red-900/50">
                        Salir sin Guardar
                    </a>
                    {{-- Llama al método de guardado en el componente --}}
                    <button wire:click="finishWorkout" 
                        @click="showExitModal = false"
                        class="py-2 px-4 bg-lime-600 hover:bg-lime-700 text-white font-bold rounded-lg transition">
                        Guardar y Terminar
                    </button>
                </div>
            </div>
        </div>
        {{-- FIN MODAL --}}

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
        [x-cloak] { display: none !important; }
    </style>
</div>