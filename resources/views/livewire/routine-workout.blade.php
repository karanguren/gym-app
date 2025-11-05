<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8" x-data="{
        showFinalizeModal: false,
        toast: { show: false, message: '', type: 'success' },
        showToast(data) {
            this.toast.message = data.message;
            this.toast.type = data.type || 'success';
            this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 3000);
        }
    }" x-init="@this.on('show-toast', (event) => showToast(event[0]))" x-cloak>
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

                    <!-- <button @click="showFinalizeModal = true" type="button" class="flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-full transition duration-300 shadow-lg disabled:opacity-50" @if (!$isRunning && $seconds == 0) disabled @endif>
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3l-2.4-2.4c-.2-.2-.5-.3-.7-.3H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V9.4c0-.3-.1-.5-.3-.7l-2.4-2.4z"/><path d="M12 18v-6h6"/></svg>
                            Finalizar
                        </button> -->
                    <button @click="showFinalizeModal = true" type="button"
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($routineExercises as $index => $routineExercise)
                    @php
                        $reId = $routineExercise->id;
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
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $index + 1 }}. {{ $exerciseName }}
                                </h2>
                                <p class="text-sm font-medium text-lime-600 dark:text-lime-400 uppercase">
                                    {{ $muscleGroup }}
                                </p>
                            </div>
                            {{-- <button wire:click="showInstructions({{ $reId }})" type="button" class="text-sm font-semibold text-gray-500 hover:text-lime-600 dark:text-gray-400 dark:hover:text-lime-500 transition duration-150 p-2 rounded-full">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.8 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                                </button> --}}
                            <button type="button" wire:click="showInstructions({{ $reId }})"
                                class="btn-ouline-rounded-ve" title="Ver Instrucciones">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>

                        {{-- Tabla de Sets --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-fixed">
                                <thead class="bg-gray-50 dark:bg-gray-700">
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
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                                    @foreach ($sets as $setIndex => $set)
                                        <tr wire:key="set-{{ $reId }}-{{ $setIndex }}"
                                            class="@if ($set['done']) bg-lime-50/50 dark:bg-gray-900/50 @endif">

                                            <td
                                                class="px-2 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $setIndex + 1 }}
                                            </td>

                                            {{-- Input Reps --}}
                                            <td class="px-2 py-3">

                                                <flux:input
                                                    wire:model.live.debounce.300ms="workoutData.{{ $reId }}.{{ $setIndex }}.result_reps"
                                                    placeholder="{{ $set['target_reps'] }}"
                                                    min="2"
                                                    type="number"
                                                    class="w-full inputs text-center"
                                                />
                                            </td>

                                            {{-- Input Kg --}}
                                            <td class="px-2 py-3">
                                                
                                                <flux:input
                                                    wire:model.live.debounce.300ms="workoutData.{{ $reId }}.{{ $setIndex }}.result_kg"
                                                    placeholder="{{ number_format($set['target_kg'], 1) }}" 
                                                    min="2"
                                                    step="0.5"
                                                    type="number"
                                                    class="w-full inputs text-center"
                                                />
                                            </td>

                                            {{-- Botones de Acción --}}
                                            <td class="px-2 py-3 text-right text-sm font-medium flex space-x-1 justify-center items-center">

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
                                class="flex btn-ouline-ve">
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
        @if ($showInstructionsModal && $selectedExerciseDetails)
            <div class="bg-modal flex items-center justify-center p-4"
                @click.self="closeInstructionsModal">
                <div class="modal-instrucciones" @click.stop>

                    <div class="border-b border-gray-700 pb-4 mb-4 flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold leading-tight text-white">
                            <span class="text-lime-400">{{ $selectedExerciseDetails['name'] }}</span>
                        </h3>
                        <button wire:click="closeInstructionsModal" @click="open = false"
                            class="text-gray-400 hover:text-white transition duration-200">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- CONTENIDO DEL MODAL --}}
                    <div class="mt-4 space-y-6 max-h-[65vh] overflow-y-auto pr-3 -mr-2 custom-scrollbar">

                        @if ($selectedExerciseDetails['gif_path'] ?? false)
                            <div
                                class="w-full relative pb-[100%] overflow-hidden rounded-xl bg-gray-800 flex items-center justify-center shadow-lg border-2 border-lime-600">
                                <img src="{{ $selectedExerciseDetails['gif_path'] }}"
                                    alt="GIF de {{ $selectedExerciseDetails['name'] }}"
                                    class="absolute inset-0 w-full h-full object-contain">
                            </div>
                        @endif

                        <p class="text-sm font-semibold text-lime-400 capitalize">
                            Grupo Muscular: <span
                                class="font-normal text-gray-300">{{ $selectedExerciseDetails['muscle_group'] }}</span>
                        </p>

                        {{-- SECCIÓN PREPARACIÓN (Ahora usa description - resumen) --}}
                        <div class="border-t border-gray-700 pt-5">
                            <h4 class="text-xl font-bold text-white mb-2">
                                Preparación (Resumen)
                            </h4>
                            <p class="text-gray-300 leading-relaxed">
                                {{ $selectedExerciseDetails['description'] }}
                            </p>
                        </div>

                        {{-- SECCIÓN EJECUCIÓN (Ahora usa instructions - pasos) --}}
                        <div class="border-t border-gray-700 pt-5">
                            <h4 class="text-xl font-bold text-white mb-2">
                                Ejecución
                            </h4>
                            <p class="text-gray-300 leading-relaxed">
                                {{ $selectedExerciseDetails['instructions'] }}
                            </p>
                        </div>

                        {{-- SECCIÓN CONSEJOS CLAVES (Ahora usa tips) --}}
                        <div class="border-t border-gray-700 pt-5">
                            <h4 class="text-xl font-bold text-white mb-2">
                                Consejos Claves
                            </h4>
                            <p class="text-gray-300 leading-relaxed">
                                {{ $selectedExerciseDetails['tips'] }}
                            </p>
                        </div>

                    </div>
                    {{-- FIN: CONTENIDO DEL MODAL --}}

                    <div class="mt-8 pt-5 border-t border-gray-700 flex justify-end">
                        <button wire:click="closeInstructionsModal" @click="open = false" type="button"
                            class="btn-ouline-ve">
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal de CONFIRMACIÓN DE ELIMINACIÓN DE SET --}}
        @if ($showDeleteConfirmationModal)
            <div class="bg-modal flex items-center justify-center p-4"
                @click.self="cancelRemoveSet">
                <div class="card-tb-ro-v2 max-w-sm w-full" @click.stop>
                    <h3 class="text-xl font-bold text-red-600 dark:text-red-400 mb-2">
                        Confirmar Eliminación
                    </h3>
                    <p class="text-gray-700 dark:text-gray-300 mb-6">
                        ¿Estás seguro de que deseas eliminar permanentemente el Set #{{ $setIndexToDelete + 1 }}?
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button wire:click="cancelRemoveSet" type="button"
                            class="btn-ouline-ve">
                            Cancelar
                        </button>
                        <button wire:click="removeSet" type="button"
                            class="btn-ouline-ro">
                            Sí, Eliminar
                        </button>
                    </div>
                </div>
            </div>
        @endif


        {{-- Modal de CONFIRMACIÓN DE FINALIZACIÓN --}}
        <div x-show="showFinalizeModal"
            class="bg-modal flex items-center justify-center p-4" x-cloak>
            <div class="card-tb-ve-v2 max-w-md w-full"
                @click.outside="showFinalizeModal = false">

                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    Finalizar Entrenamiento
                </h3>
                <p class="text-gray-700 dark:text-gray-300 mb-6">
                    ¿Estás seguro de que quieres finalizar este entrenamiento? Se registrará tu progreso y el tiempo
                    total.
                </p>

                <div class="flex justify-end space-x-3">
                    <button @click="showFinalizeModal = false" type="button"
                        class="btn-ouline-ro">
                        Cancelar
                    </button>
                    <button wire:click="finishWorkout" wire:loading.attr="disabled"
                        @click="showFinalizeModal = false" type="button"
                        class="btn-ouline-ve">
                        Confirmar y Guardar
                    </button>
                </div>
            </div>
        </div>

        {{-- CRÍTICO: TOASTER / NOTIFICACIONES LIVEWIRE --}}
        <div class="fixed bottom-4 right-4 z-[999]" x-cloak>
            <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                @click.away="toast.show = false"
                :class="{
                    'bg-green-500': toast.type === 'success',
                    'bg-red-500': toast.type === 'error',
                    'bg-blue-500': toast.type === 'info',
                    'bg-yellow-500': toast.type === 'warning'
                }"
                class="max-w-xs w-full text-white p-4 rounded-lg shadow-xl font-semibold transform transition duration-300 cursor-pointer flex items-center space-x-3">

                <svg x-show="toast.type === 'success'" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <svg x-show="toast.type === 'error'" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                <svg x-show="toast.type === 'info'" class="w-6 h-6" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="16" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>

                <span x-text="toast.message" class="flex-grow"></span>
            </div>
        </div>
    </div>
</div>
