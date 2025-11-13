<div class="div-principal">
    <div x-data="{
        // Propiedades de Livewire en Alpine
        isTemplate: @entangle('isTemplateMode').live,
        isEditing: @json($routine && $routine->exists),
        targetUserId: @json($targetUserId), // Cliente fijado por URL/Edición
        targetUser: @json($targetUser?->name),
    }" class="max-w mx-auto sm:px-6 lg:px-8">

        <div class="md:p-8">
            <form wire:submit="saveRoutine" class="">

                <div>
                    <h1 class="titles-border">
                        <span>
                            @if ($routine && $routine->exists)
                                Editar Rutina
                            @elseif ($targetUser)
                                Crear Nueva Rutina para: {{ $targetUser->name }} {{ $targetUser->last_name }}
                            @else
                                Crear Nueva Rutina
                            @endif
                        </span>
                    </h1>
                    {{-- <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        <span>
                            @if ($isTemplateMode)
                                Guardar como **Plantilla general**.
                            @else
                                Asignar como **Rutina Exclusiva**.
                            @endif
                        </span>
                        
                        @if (!$isTemplateMode && $targetUser)
                            <span class="font-semibold text-indigo-500 dark:text-indigo-400">
                                a {{ $targetUser->name }} {{$targetUser->last_name}}.
                            </span>
                        @endif
                    </p> --}}
                </div>

                {{-- <div x-show="!targetUserId || isEditing && isTemplate" x-collapse.duration.500ms
                    class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 p-4 bg-indigo-50 dark:bg-gray-700/50 rounded-xl border border-indigo-200 dark:border-gray-700">

                    <div x-show="!targetUserId"
                        class="col-span-1 flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="flex items-center space-x-2">
                            <label for="template-toggle"
                                class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                Modo Plantilla
                            </label>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                :class="isTemplate ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' :
                                    'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300'">
                                <span x-text="isTemplate ? 'ACTIVADO' : 'EXCLUSIVO'"></span>
                            </span>
                        </div>

                        <div class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in">
                            <input type="checkbox" id="template-toggle"
                                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer checked:bg-indigo-600 transition duration-200 peer"
                                wire:model.live="isTemplateMode" :disabled="!isEditing && targetUserId">
                            <label for="template-toggle"
                                class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer peer-checked:bg-indigo-400 dark:peer-checked:bg-indigo-600 transition duration-200"></label>
                        </div>
                    </div>

                    <div x-show="!isTemplate && !targetUserId" x-collapse.duration.500ms
                        class="col-span-1 md:col-span-2">
                        <label for="client-select"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Asignar Cliente (Selección Manual)
                        </label>
                        <select id="client-select" wire:model.live="selectedClientId" class="w-full inputs">
                            <option value="">-- Selecciona un Cliente --</option>
                            @foreach ($availableClients as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('selectedClientId')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-show="targetUserId && !isTemplate" class="col-span-1 md:col-span-3">
                        <p class="text-sm font-semibold text-indigo-700 dark:text-indigo-300">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Rutina exclusiva para: <span x-text="targetUser"></span>. El modo de asignación está
                            **fijado**.
                        </p>
                    </div>
                </div> --}}

                <div class=" ">

                    <div class="space-y-6 mb-10">
                        <div>
                            <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nombre de la Rutina
                            </flux:label>
                            <flux:input wire:model="name" placeholder="Ej: Full Body" type="text" class="mt-2 block w-full inputs" />
                            @error('name')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Notas Adicionales (Opcional)
                            </flux:label>
                            <flux:textarea 
                                wire:model="notes"
                                id="notes"
                                rows="auto"
                                class="mt-2 block w-full inputs"
                                placeholder="Ej: Realizar 10 minutos de cardio al inicio."
                            />
                            @error('notes') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <h2 class="subtitles">
                        Ejercicios de la Rutina
                    </h2>

                    <div class="space-y-8" wire:loading.attr="disabled">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" wire:loading.attr="disabled"
                        >
                            @foreach ($routineData as $index => $exerciseData)
                                <div wire:key="exercise-{{ $index }}"
                                    class="card-tb-ve-v2">

                                    <div class="flex justify-between items-start mb-4 pb-4 border-b dark:border-gray-600">
                                        <h3 class="modalTitle">
                                            Ejercicio #{{ $index + 1 }}
                                        </h3>
                                        <div class="flex space-x-2">
                                            @if (isset($exerciseData['exercise_id']))
                                                <button type="button"
                                                    wire:click="showExerciseDetails({{ $exerciseData['exercise_id'] }})"
                                                    class="text-[#7bcb01] hover:text-[#7bcb01]/80 dark:hover:text-[#7bcb01]/70 transition"
                                                    title="Ver detalles del ejercicio">
                                                    <svg class="w-5 h-5 " fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            @endif

                                            <button type="button" wire:click="removeExercise({{ $index }})"
                                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-600 transition"
                                                title="Eliminar este ejercicio">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        
                                        <flux:select :label="__('Selecciona el Ejercicio')"
                                                wire:model.live.debounce.300ms="routineData.{{ $index }}.exercise_id"  id="exercise-{{ $index }}"
                                            class="!w-full inputs">
                                            <flux:select.option value="">-- Selecciona un Ejercicio -- </flux:select.option>
                                            @foreach ($availableExercises as $exercise)
                                                <flux:select.option value="{{ $exercise['id'] }}">{{ $exercise['name'] }}</flux:select.option>
                                            @endforeach
                                        </flux:select>
                                        
                                        @error("routineData.$index.exercise_id")
                                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mt-6 p-4 bg-white dark:bg-[#121212] rounded-lg shadow-inner">
                                        <h4
                                            class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-3 flex justify-between items-center">
                                            Series y Repeticiones (Sets)
                                            <button type="button" wire:click="addSet({{ $index }})"
                                                class="text-sm text-[#7bcb01] hover:text-[#7bcb01]/80 dark:hover:text-[#7bcb01]/70 font-semibold flex items-center transition">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                Añadir Serie
                                            </button>
                                        </h4>

                                        <div class="space-y-3">
                                            @foreach ($exerciseData['sets'] as $setIndex => $set)
                                                <div wire:key="set-{{ $index }}-{{ $setIndex }}"
                                                    class="flex space-x-3 items-end">

                                                    <div class="flex-1">
                                                        <flux:label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                                             Serie {{ $setIndex + 1 }} - Reps
                                                        </flux:label>
                                                        <flux:input wire:model.live.debounce.300ms="routineData.{{ $index }}.sets.{{ $setIndex }}.reps"
                                                        type="number"  id="reps-{{ $index }}-{{ $setIndex }}" min="1" max="999" class="w-full inputs" />
                                                        @error("routineData.$index.sets.$setIndex.reps")
                                                            <span class="text-xs text-red-500">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="flex-1">
                                                        <flux:label class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                                            KG
                                                        </flux:label>
                                                        <flux:input wire:model.live.debounce.300ms="routineData.{{ $index }}.sets.{{ $setIndex }}.kg" step="0.5"
                                                        type="number"  id="kg-{{ $index }}-{{ $setIndex }}" min="1" max="999" class="w-full inputs" />
                                                        @error("routineData.$index.sets.$setIndex.kg")
                                                            <span class="text-xs text-red-500">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <button type="button"
                                                        wire:click="removeSet({{ $index }}, {{ $setIndex }})"
                                                        class="p-2 text-red-500 hover:bg-red-100 dark:hover:bg-red-900 rounded-lg transition"
                                                        title="Eliminar esta serie">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="button" wire:click="addEmptyExercise" class="w-full btn-outline-lime">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Añadir Otro Ejercicio
                        </button>
                    </div>

                    @error('routineData')
                        <p
                            class="mt-4 text-sm font-medium text-red-600 dark:text-red-400 p-3 bg-red-50 dark:bg-red-900/50 rounded-lg border border-red-200 dark:border-red-700">
                            {{ $message }}
                        </p>
                    @enderror

                    <div class="mt-10 pt-6 border-t dark:border-gray-700">
                        <button type="submit" class="btn-outline-lime" wire:loading.attr="disabled">
                            <span wire:loading.remove>Guardar Rutina</span>
                            <span wire:loading>Guardando...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- NUEVO MODAL PARA DETALLES DE EJERCICIO (CONTROLADO SOLO POR LIVEWIRE) --}}
        @if ($showModal && $selectedExerciseDetails)
            <x-exercise-instructions-modal :show-modal="'showModal'" :exercise="$selectedExerciseDetails" />
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
                border-color: #6366f1;
                /* Indigo-600 */
            }

            .toggle-label {
                transition: background-color 0.2s ease;
                height: 24px;
            }

            .toggle-checkbox:checked+.toggle-label {
                background-color: #818cf8;
                /* Indigo-400 */
            }
        </style>
    </div>
</div>
