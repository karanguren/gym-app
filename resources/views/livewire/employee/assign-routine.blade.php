<div class="py-6 sm:py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Mensajes de Sesión --}}
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

        <form wire:submit.prevent="assignRoutine" class="bg-white/90 dark:bg-[#1a1a1a]/95 shadow-2xl sm:rounded-xl p-6 md:p-10">

            <header class="mb-8 border-b dark:border-gray-700 pb-4">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-[#7bcb01]">
                    Asignar y Personalizar Rutina
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Estás asignando la rutina: <span class="font-bold text-[#7bcb01]">{{ $currentRoutine->name ?? 'Cargando...' }}</span>. Edita los sets si es necesario antes de asignar.
                </p>
            </header>

            {{-- SECCIÓN 1: SELECCIÓN DE CLIENTE --}}
            <div class="mb-8">
                <label for="user_id" class="block text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Selecciona al Cliente
                </label>
                <select wire:model.live="userId" id="user_id" 
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#7bcb01] focus:ring-[#7bcb01] dark:bg-gray-800 dark:border-gray-600 dark:text-white remove-number-arrows">
                    <option value="0">-- Seleccionar Cliente --</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }} (ID: {{ $client->id }})</option>
                    @endforeach
                </select>
                @error('userId') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- SECCIÓN 2: EJERCICIOS Y SETS (REPLICANDO LÓGICA DE ROUTINE BUILDER) --}}
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b dark:border-gray-700 pb-2">
                Ejercicios en la Rutina ({{ count($routineData) }})
            </h2>
            
            @if (empty($routineData))
                <p class="text-gray-500 dark:text-gray-400">No hay ejercicios cargados en esta rutina. Esto es un error de configuración.</p>
            @else
                <div class="space-y-8">
                    @foreach ($currentRoutine->exercises as $routineExercise)
                        @php
                            $exerciseId = $routineExercise->exercise_id;
                            // Asumimos que $routineData ya está cargado y decodificado en mount()
                            $sets = $routineData[$exerciseId] ?? []; 
                            $exerciseDetails = $routineExercise->exercise; // Accede a la relación Exercise
                        @endphp

                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-md bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-[#7bcb01]">
                                    {{ $exerciseDetails->name ?? 'Ejercicio Desconocido' }} 
                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ ucfirst($exerciseDetails->muscle_group ?? 'N/A') }})</span>
                                </h3>
                                <button type="button" wire:click="showExerciseDetails({{ $exerciseId }})" class="text-gray-500 hover:text-[#7bcb01] transition duration-150">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                            </div>

                            {{-- TABLA DE SETS --}}
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider rounded-tl-lg">Set</th>
                                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Repeticiones</th>
                                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Peso (Kg)</th>
                                            <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider rounded-tr-lg">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse ($sets as $setIndex => $set)
                                            <tr wire:key="set-{{ $exerciseId }}-{{ $setIndex }}">
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $setIndex + 1 }}</td>
                                                
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <input type="number" 
                                                        wire:model.live.debounce.300ms="routineData.{{ $exerciseId }}.{{ $setIndex }}.reps" 
                                                        class="w-20 rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm remove-number-arrows"
                                                        min="1" required>
                                                    @error("routineData.{$exerciseId}.{$setIndex}.reps") <span class="text-xs text-red-500 block">{{ $message }}</span> @enderror
                                                </td>
                                                
                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    <input type="number" 
                                                        wire:model.live.debounce.300ms="routineData.{{ $exerciseId }}.{{ $setIndex }}.kg" 
                                                        class="w-20 rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm remove-number-arrows"
                                                        min="0" step="0.5">
                                                    @error("routineData.{$exerciseId}.{$setIndex}.kg") <span class="text-xs text-red-500 block">{{ $message }}</span> @enderror
                                                </td>

                                                <td class="px-3 py-2 whitespace-nowrap">
                                                    @if (count($sets) > 1)
                                                        <button type="button" wire:click="removeSet({{ $exerciseId }}, {{ $setIndex }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200 transition duration-150">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    Añade el primer set para este ejercicio.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            {{-- BOTÓN AÑADIR SET --}}
                            <div class="mt-4 flex justify-end">
                                <button type="button" wire:click="addSet({{ $exerciseId }})" 
                                    class="text-sm px-3 py-1 bg-[#5aa301] hover:bg-[#7bcb01] text-white font-medium rounded-full transition duration-300 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    Añadir Set
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif


            {{-- SECCIÓN 3: BOTÓN DE GUARDADO/ASIGNACIÓN --}}
            <footer class="mt-10 pt-6 border-t dark:border-gray-700 flex justify-end">
                <button type="submit" 
                    {{-- Usa la propiedad canSaveRoutine del RoutineBuilder (si la replicaste) o verifica manualmente --}}
                    @if (!$userId || empty($routineData)) disabled @endif
                    class="bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-3 px-8 rounded-xl transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed text-lg">
                    Asignar Rutina a Cliente
                </button>
            </footer>
            
        </form>
        
        {{-- MODAL DE DETALLES DEL EJERCICIO (Similar al RoutineBuilder) --}}
        @if ($showModal && $selectedExerciseDetails)
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" 
                x-data="{ open: @entangle('showModal') }" 
                x-show="open" 
                x-transition.opacity>
                
                <div @click.outside="open = false; $wire.call('closeModal')" class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        {{-- ENCABEZADO DEL MODAL --}}
                        <div class="flex justify-between items-start border-b dark:border-gray-700 pb-3 mb-4">
                            <h3 class="text-2xl font-extrabold text-[#7bcb01]">
                                {{ $selectedExerciseDetails->name ?? 'Detalles del Ejercicio' }}
                            </h3>
                            <button wire:click="closeModal" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        {{-- CONTENIDO DEL MODAL --}}
                        <div class="space-y-4 text-gray-700 dark:text-gray-300">
                            
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Grupo Muscular Principal:</h4>
                                <p class="capitalize">{{ $selectedExerciseDetails->muscle_group }}</p>
                            </div>

                            <div>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Descripción:</h4>
                                <p class="whitespace-pre-wrap">{{ $selectedExerciseDetails->description }}</p>
                            </div>
                            
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
            </div>
        @endif

    </div>
    {{-- Estilo para eliminar las flechas de los inputs tipo number --}}
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
