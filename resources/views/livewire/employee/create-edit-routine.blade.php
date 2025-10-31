<div x-data="{
    // Propiedades de Livewire en Alpine
    isTemplate: @entangle('isTemplateMode').live,
    isEditing: @json($routine && $routine->exists),
    targetUserId: @json($targetUserId), // Cliente fijado por URL/Edición
    targetUser: @json($targetUser?->name),
}" class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 font-inter">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form wire:submit="saveRoutine" class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden p-6 sm:p-10">

            <div class="mb-8 border-b pb-4 dark:border-gray-700 flex justify-between items-start flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        <span x-text="isEditing ? 'Editar Rutina' : 'Crear Nueva Rutina'"></span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <span x-text="isTemplate ? 'Guardar como Plantilla general.' : 'Asignar como Rutina Exclusiva'"></span>
                        <span x-show="!isTemplate && targetUser" class="font-semibold text-indigo-500 dark:text-indigo-400">
                            a <span x-text="targetUser"></span>.
                        </span>
                    </p>
                </div>
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition" wire:loading.attr="disabled">
                    <span wire:loading.remove>Guardar Rutina</span>
                    <span wire:loading>Guardando...</span>
                </button>
            </div>

            <div x-show="!targetUserId || isEditing && isTemplate" x-collapse.duration.500ms class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 p-4 bg-indigo-50 dark:bg-gray-700/50 rounded-xl border border-indigo-200 dark:border-gray-700">
                
                <div x-show="!targetUserId" class="col-span-1 flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="flex items-center space-x-2">
                        <label for="template-toggle" class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                            Modo Plantilla
                        </label>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full" 
                              :class="isTemplate ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300'">
                            <span x-text="isTemplate ? 'ACTIVADO' : 'EXCLUSIVO'"></span>
                        </span>
                    </div>

                    <div class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in">
                        <input 
                            type="checkbox" 
                            id="template-toggle" 
                            class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer checked:bg-indigo-600 transition duration-200 peer" 
                            wire:model.live="isTemplateMode" 
                            :disabled="!isEditing && targetUserId"
                        >
                        <label 
                            for="template-toggle" 
                            class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer peer-checked:bg-indigo-400 dark:peer-checked:bg-indigo-600 transition duration-200"
                        ></label>
                    </div>
                </div>

                <div x-show="!isTemplate && !targetUserId" x-collapse.duration.500ms class="col-span-1 md:col-span-2">
                    <label for="client-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Asignar Cliente (Selección Manual)
                    </label>
                    <select 
                        id="client-select" 
                        wire:model.live="selectedClientId" 
                        class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100 dark:disabled:bg-gray-700/50 transition"
                    >
                        <option value="">-- Selecciona un Cliente --</option>
                        @foreach($availableClients as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('selectedClientId') 
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                    @enderror
                </div>

                <div x-show="targetUserId && !isTemplate" class="col-span-1 md:col-span-3 p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                    <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Rutina exclusiva para: <span x-text="targetUser"></span>. El modo de asignación está **fijado**.
                    </p>
                </div>
            </div>

            <div class="space-y-6 mb-10">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre de la Rutina</label>
                    <input type="text" id="name" wire:model="name" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Full Body - Principiante A">
                    @error('name') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notas Adicionales (Opcional)</label>
                    <textarea id="notes" wire:model="notes" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ej: Realizar 10 minutos de cardio al inicio."></textarea>
                    @error('notes') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 pt-4 border-t dark:border-gray-700">
                Ejercicios de la Rutina
            </h2>

            <div class="space-y-8" wire:loading.attr="disabled">
                @foreach ($routineData as $index => $exerciseData)
                    <div 
                        wire:key="exercise-{{ $index }}" 
                        class="p-6 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg bg-gray-50 dark:bg-gray-700/50 relative transition-all duration-300"
                    >
                        
                        <div class="flex justify-between items-start mb-4 pb-4 border-b dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-indigo-700 dark:text-indigo-400">
                                Ejercicio #{{ $index + 1 }}
                            </h3>
                            <div class="flex space-x-2">
                                @if(isset($exerciseData['exercise_id']))
                                    <button 
                                        type="button" 
                                        wire:click="openModal({{ $exerciseData['exercise_id'] }})" 
                                        class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition"
                                        title="Ver detalles del ejercicio"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>
                                @endif

                                <button 
                                    type="button" 
                                    wire:click="removeExercise({{ $index }})" 
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-600 transition"
                                    title="Eliminar este ejercicio"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m-4-8h4m-4 0V5a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="exercise-{{ $index }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Selecciona el Ejercicio
                            </label>
                            <select 
                                id="exercise-{{ $index }}" 
                                wire:model.live.debounce.300ms="routineData.{{ $index }}.exercise_id" 
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition"
                            >
                                <option value="">-- Selecciona un Ejercicio --</option>
                                @foreach($availableExercises as $exercise)
                                    <option value="{{ $exercise['id'] }}">{{ $exercise['name'] }}</option>
                                @endforeach
                            </select>
                            @error("routineData.$index.exercise_id") 
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> 
                            @enderror
                        </div>
                        
                        <div class="mt-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-inner">
                            <h4 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex justify-between items-center">
                                Series y Repeticiones (Sets)
                                <button type="button" wire:click="addSet({{ $index }})" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold flex items-center transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Añadir Serie
                                </button>
                            </h4>

                            <div class="space-y-3">
                                @foreach ($exerciseData['sets'] as $setIndex => $set)
                                    <div wire:key="set-{{ $index }}-{{ $setIndex }}" class="flex space-x-3 items-end">
                                        
                                        <div class="flex-1">
                                            <label for="reps-{{ $index }}-{{ $setIndex }}" class="block text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Serie {{ $setIndex + 1 }} - Reps
                                            </label>
                                            <input 
                                                type="number" 
                                                id="reps-{{ $index }}-{{ $setIndex }}" 
                                                wire:model.live.debounce.300ms="routineData.{{ $index }}.sets.{{ $setIndex }}.reps" 
                                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                                min="1" max="999"
                                            >
                                            @error("routineData.$index.sets.$setIndex.reps") 
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> 
                                            @enderror
                                        </div>
                                        
                                        <div class="flex-1">
                                            <label for="kg-{{ $index }}-{{ $setIndex }}" class="block text-xs font-medium text-gray-500 dark:text-gray-400">
                                                KG (Opcional)
                                            </label>
                                            <input 
                                                type="number" 
                                                step="0.1" 
                                                id="kg-{{ $index }}-{{ $setIndex }}" 
                                                wire:model.live.debounce.300ms="routineData.{{ $index }}.sets.{{ $setIndex }}.kg" 
                                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                                min="0" max="999"
                                            >
                                            @error("routineData.$index.sets.$setIndex.kg") 
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> 
                                            @enderror
                                        </div>

                                        <button 
                                            type="button" 
                                            wire:click="removeSet({{ $index }}, {{ $setIndex }})" 
                                            class="p-2 text-red-500 hover:bg-red-100 dark:hover:bg-red-900 rounded-lg transition"
                                            title="Eliminar esta serie"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if (!$loop->last)
                            <div class="h-px bg-gray-200 dark:bg-gray-700 mt-8"></div>
                        @endif

                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                <button type="button" wire:click="addEmptyExercise" class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Añadir Otro Ejercicio
                </button>
            </div>

            @error('routineData')
                <p class="mt-4 text-sm font-medium text-red-600 dark:text-red-400 p-3 bg-red-50 dark:bg-red-900/50 rounded-lg border border-red-200 dark:border-red-700">
                    {{ $message }}
                </p>
            @enderror

            <div class="mt-10 pt-6 border-t dark:border-gray-700">
                <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900 transition" wire:loading.attr="disabled">
                    <span wire:loading.remove>Guardar Rutina</span>
                    <span wire:loading>Guardando...</span>
                </button>
            </div>
            
        </form>
    </div>

    {{-- NUEVO MODAL PARA DETALLES DE EJERCICIO (CONTROLADO SOLO POR LIVEWIRE) --}}
    @if($showExerciseModal)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
            wire:transition.opacity {{-- Transición de Livewire para el fondo --}}
            wire:click.self="closeModal" {{-- Cierre al hacer clic en el fondo (solo en el self, no en el contenido) --}}
        >
            <div 
                wire:transition.scale.300ms {{-- Transición de Livewire para la caja del modal --}}
                class="bg-white dark:bg-[#1a1a1a] rounded-xl shadow-2xl p-6 max-w-xl w-full transform transition-all duration-300 text-left" 
            >
                @if($selectedExerciseDetails)
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
                            Grupo Muscular: {{ $selectedExerciseDetails->muscle_group ?? 'N/A' }}
                        </p>
                        
                        {{-- SECCIÓN PREPARACIÓN (Ahora usa description - resumen) --}}
                        <div class="border-t pt-4 dark:border-gray-700">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                Preparación (Resumen)
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                {{ $selectedExerciseDetails->description ?? 'No hay descripción disponible.' }}
                            </p>
                        </div>

                        {{-- SECCIÓN EJECUCIÓN (Ahora usa instructions - pasos) --}}
                        <div class="border-t pt-4 dark:border-gray-700">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                Ejecución
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                {{ $selectedExerciseDetails->instructions ?? 'No hay pasos de ejecución disponibles.' }}
                            </p>
                        </div>

                        {{-- SECCIÓN CONSEJOS CLAVES (Ahora usa tips) --}}
                        <div class="border-t pt-4 dark:border-gray-700">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">
                                Consejos Claves
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                {{ $selectedExerciseDetails->tips ?? 'No hay consejos disponibles.' }}
                            </p>
                        </div>

                    </div>
                    {{-- FIN: CONTENIDO DEL MODAL --}}

                    <div class="mt-6 pt-4 border-t dark:border-gray-700 flex justify-end">
                        <button wire:click="closeModal" 
                            type="button" 
                            class="bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                            Entendido / Cerrar
                        </button>
                    </div>
                @else
                    {{-- Estado de carga adaptado --}}
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <p class="text-2xl font-bold leading-6 text-gray-900 dark:text-white">
                            Cargando detalles...
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t dark:border-gray-700 flex justify-end">
                        <button type="button" wire:click="closeModal" class="bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                            Cerrar
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
    
    <style>
        .toggle-checkbox {
            left: 2px;
            top: 2px;
            height: 20px; 
            width: 20px;
            border-width: 2px;
        }

        .toggle-checkbox:checked {
            transform: translateX(14px); 
            right: 0;
            border-color: #6366f1; /* Indigo-600 */
        }
        .toggle-label {
            transition: background-color 0.2s ease;
            height: 24px;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #818cf8; /* Indigo-400 */
        }
    </style>
</div>