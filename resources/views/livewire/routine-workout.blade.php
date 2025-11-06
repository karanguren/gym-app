<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="md:p-8">

            <a href="{{ route('client.routines') }}"
                class="text-lime-600 hover:text-lime-700 dark:text-lime-400 dark:hover:text-lime-300 transition duration-150 mb-4 inline-flex items-center text-sm font-medium">
                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M19 12H5" />
                    <path d="M12 19l-7-7 7-7" />
                </svg>
                Volver a Rutinas
            </a>


            <header class="mb-8 border-b dark:border-gray-700 pb-4">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-[#7bcb01] flex items-center">
                    <svg class="w-8 h-8 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 12l6-4.5 6 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 21v-9z" />
                        <path d="M9 11v6" />
                        <path d="M12 9v8" />
                        <path d="M15 11v6" />
                    </svg>
                    {{ $routine->name }}
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Registra tu progreso set por set. ¡Buena suerte con tu entrenamiento!
                </p>
            </header>

            {{-- Cronómetro y Controles --}}
            <div
                class="flex flex-col sm:flex-row items-center justify-between p-4 mb-8  bg-white dark:bg-[#1a1a1a] p-8 flex flex-col justify-between rounded-xl shadow-2xl border-t-4 border-t-[#7bcb01]">

                {{-- Tiempo --}}
                <div class="text-5xl font-mono font-bold dark:text-white mb-4 sm:mb-0">
                    <span wire:poll.visible.1000ms="updateTimer">{{ $this->formattedTime }}</span>
                </div>

                {{-- Controles --}}
                <div class="flex space-x-3">
                    @if (!$isRunning)
                        <button wire:click="startTimer" type="button"
                            class="flex items-center bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-3 px-6 rounded-full transition duration-300 shadow-lg">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="5 3 19 12 5 21 5 3" />
                            </svg>
                            @if ($seconds > 0)
                                Reanudar
                            @else
                                Iniciar
                            @endif
                        </button>
                    @else
                        <button wire:click="pauseTimer" type="button"
                            class="flex items-center bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-6 rounded-full transition duration-300 shadow-lg">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="6" y="4" width="4" height="16" />
                                <rect x="14" y="4" width="4" height="16" />
                            </svg>
                            Pausar
                        </button>
                    @endif

                    <button wire:click="openFinalizeModal" type="button"
                        class="flex items-center py-3 px-6 rounded-full text-white transition duration-300 
                                @if ($this->isEverySetCompleted) bg-[#7bcb01] hover:bg-[#5aa301] @else bg-gray-500 cursor-not-allowed opacity-70 @endif"
                        title="Finalizar Entrenamiento" @disabled(!$this->isEverySetCompleted)>
                        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        Finalizar
                    </button>
                </div>
            </div>


            {{-- Listado de Ejercicios y Progreso --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($routineExercises as $index => $routineExercise)
                    @php
                        $reId = $routineExercise->id;
                        $exerciseId = $routineExercise->exercise_id;
                        $exerciseName = $routineExercise->exercise->name ?? 'Ejercicio Desconocido';
                        $muscleGroup = $routineExercise->exercise->muscle_group ?? 'N/A';

                        $sets = $workoutData[$reId] ?? [
                            [
                                'target_reps' => $routineExercise->target_reps ?? 10,
                                'target_kg' => $routineExercise->target_weight ?? 0.0,
                                'done' => false,
                                'result_reps' => $routineExercise->target_reps ?? 10,
                                'result_kg' => $routineExercise->target_weight ?? 0.0,
                            ],
                        ];
                    @endphp

                    <div id="exercise-{{ $reId }}" class="card-tb-ve-v2">

                        {{-- Cabecera del Ejercicio --}}
                        <div class="flex justify-between items-start mb-4 border-b pb-3 dark:border-gray-700">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $index + 1 }}. {{ $exerciseName }}
                                </h2>
                                <p class="text-xs font-medium text-lime-600 dark:text-lime-400 uppercase">
                                    {{ $muscleGroup }}
                                </p>
                            </div>
                            <button type="button" wire:click="showExerciseDetails({{ $exerciseId }})"
                                class="btn-outline-rounded-ve" title="Ver Instrucciones">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>

                        {{-- Tabla de Sets --}}
                        <div class="overflow-x-auto">
                            <table class="tables table-fixed">
                                <thead class="">
                                    <tr>
                                        <th scope="col"
                                            class="w-1/12 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Set
                                        </th>
                                        <th scope="col"
                                            class="w-4/12 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Reps
                                        </th>
                                        <th scope="col"
                                            class="w-4/12 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Kg
                                        </th>
                                        <th scope="col"
                                            class="w-3/12 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                            Acción
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="tables-tbody">
                                    @foreach ($sets as $setIndex => $set)
                                        <tr wire:key="set-{{ $reId }}-{{ $setIndex }}"
                                            class="@if ($set['done']) bg-lime-50/50 dark:bg-gray-900/50 @endif">

                                            <td class="px-2 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $setIndex + 1 }}
                                            </td>

                                            {{-- Input Reps --}}
                                            <td class="px-2 py-3">

                                                <flux:input
                                                    wire:model.live.debounce.300ms="workoutData.{{ $reId }}.{{ $setIndex }}.result_reps"
                                                    placeholder="{{ $set['target_reps'] }}" min="2"
                                                    type="number" class="w-full inputs text-center" />
                                            </td>

                                            {{-- Input Kg --}}
                                            <td class="px-2 py-3">

                                                <flux:input
                                                    wire:model.live.debounce.300ms="workoutData.{{ $reId }}.{{ $setIndex }}.result_kg"
                                                    placeholder="{{ number_format($set['target_kg'], 1) }}"
                                                    min="2" step="0.5" type="number"
                                                    class="w-full inputs text-center" />
                                            </td>

                                            {{-- Botones de Acción --}}
                                            <td
                                                class="px-2 py-3 text-right text-sm font-medium flex space-x-1 justify-center items-center">

                                                {{-- Toggle Done --}}
                                                <button
                                                    wire:click="toggleSetCompleted({{ $reId }}, {{ $setIndex }})"
                                                    type="button"
                                                    title="{{ $set['done'] ? 'Marcar como Incompleto' : 'Marcar como Completo' }}"
                                                    class="p-2 rounded-full transition duration-150 
                                                                    {{ $set['done'] ? 'bg-lime-500 hover:bg-lime-600 text-white shadow-md' : 'bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300' }}">
                                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M20 6L9 17l-5-5" />
                                                    </svg>
                                                </button>

                                                {{-- Botón Eliminar Set --}}
                                                <button
                                                    wire:click="confirmRemoveSet({{ $reId }}, {{ $setIndex }})"
                                                    type="button" title="Eliminar Set"
                                                    class="p-2 rounded-full bg-red-100 hover:bg-red-200 text-red-600 transition duration-150 dark:bg-red-900/40 dark:hover:bg-red-900/60 dark:text-red-400">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                                        width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M18 6L6 18" />
                                                        <path d="M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Botón para Añadir Set --}}
                        <div class="mt-4 flex justify-end">
                            <button wire:click="addSet({{ $reId }})" type="button"
                                class="flex btn-outline-ve">
                                <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                                Añadir Set Extra
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>


        </div>


        {{-- Modal de INSTRUCCIONES --}}
        @if ($showModal && $selectedExerciseDetails)
            <x-exercise-instructions-modal 
                :show-modal="'showModal'" 
                :exercise="$selectedExerciseDetails" 
            />
        @endif


        {{-- Modal de CONFIRMACIÓN DE ELIMINACIÓN DE SET --}}
        {{-- @if ($showDeleteConfirmationModal)
            <div class="bg-modal flex items-center justify-center p-4" @click.self="cancelRemoveSet">
                <div class="card-tb-ro-v2 max-w-sm w-full" @click.stop>
                    <h3 class="text-xl font-bold text-red-600 dark:text-red-400 mb-2">
                        Confirmar Eliminación
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300 mb-6">
                        ¿Estás seguro de que deseas eliminar permanentemente el Set #{{ $setIndexToDelete + 1 }}?
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button wire:click="cancelRemoveSet" type="button" class="btn-outline-ve">
                            Cancelar
                        </button>
                        <button wire:click="removeSet" type="button" class="btn-outline-ro">
                            Sí, Eliminar
                        </button>
                    </div>
                </div>
            </div>
        @endif --}}


        {{-- Modal de CONFIRMACIÓN DE FINALIZACIÓN --}}
        {{-- @if ($showFinalizeModal)
            <div class="fixed inset-0 z-50 bg-modal flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" wire:click.prevent="closeFinalizeModal"></div>

                <div class="card-tb-ve-v2 max-w-md w-full z-10">

                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        Finalizar Entrenamiento
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300 mb-6">
                        ¿Estás seguro de que quieres finalizar este entrenamiento? Se registrará tu progreso y el tiempo
                        total.
                    </p>

                    <div class="flex justify-end space-x-3">
                        <button wire:click.prevent="closeFinalizeModal" type="button" class="btn-outline-ro">
                            Cancelar
                        </button>
                        
                        <button wire:click.prevent="finishWorkout" wire:loading.attr="disabled"
                            type="button" class="btn-outline-ve">
                            Confirmar y Guardar
                        </button>
                    </div>
                </div>
            </div>
        @endif --}}
    </div>
</div>
