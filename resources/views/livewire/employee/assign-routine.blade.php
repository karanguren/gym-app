<div class="py-6 sm:py-12 min-h-screen bg-gray-50 dark:bg-gray-900">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos personalizados para el formulario */
        .card-routine {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .dark .card-routine {
            background-color: #1f2937;
            box-shadow: none;
            border: 1px solid #374151;
        }

        /* Estilo para eliminar las flechas de los inputs tipo number */
        .remove-number-arrows::-webkit-outer-spin-button,
        .remove-number-arrows::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .remove-number-arrows[type=number] {
            -moz-appearance: textfield;
            /* Firefox */
        }
    </style>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Mensajes de Sesión --}}
        @if (session()->has('success'))
            <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-green-800 dark:text-green-200"
                role="alert">
                <span class="font-medium">Éxito:</span> {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-red-800 dark:text-red-200"
                role="alert">
                <span class="font-medium">Error:</span> {{ session('error') }}
            </div>
        @endif

        @if ($currentTemplate && $targetUser)
            <!-- HEADER Y TÍTULO -->
            <header class="mb-8 p-6 bg-white/95 dark:bg-[#1a1a1a]/95 rounded-xl shadow-lg border-t-4 border-[#7bcb01]">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-[#7bcb01]">
                    Asignar y Personalizar Rutina
                </h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">
                    Estás personalizando la plantilla **{{ $currentTemplate->name }}** para el cliente
                    **{{ $targetUser->name }}**.
                </p>
                <div class="mt-3 inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    Plantilla Original: {{ $currentTemplate->name }}
                </div>
            </header>

            <!-- FORMULARIO PRINCIPAL -->
            <form wire:submit.prevent="assignRoutine" class="card-routine p-6 md:p-10">

                <!-- DATOS GENERALES DE LA NUEVA RUTINA -->
                <section class="grid grid-cols-1 gap-6 mb-8">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre
                            de la Rutina Asignada</label>
                        <input type="text" id="name" wire:model="name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#7bcb01] focus:ring-[#7bcb01] dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required>
                        @error('name')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción (Notas de
                            Personalización)</label>
                        <textarea id="description" wire:model="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#7bcb01] focus:ring-[#7bcb01] dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                            required></textarea>
                        @error('description')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </section>

                <!-- TÍTULO DE EJERCICIOS -->
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 border-b pb-2">
                    Ajuste de Ejercicios ({{ count($routineData) }})
                </h2>

                <!-- LISTA DE EJERCICIOS PARA PERSONALIZAR -->
                <section class="space-y-6">
                    @foreach ($routineData as $index => $exercise)
                        <div
                            class="bg-gray-50 dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">

                            <div class="flex justify-between items-center mb-4 border-b pb-3 dark:border-gray-700">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white">
                                    {{ $exercise['name'] }}
                                </h3>
                                <div>
                                    <button type="button" wire:click="openModal({{ $index }})"
                                        class="inline-flex justify-center items-center px-3 py-1.5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-150">
                                        Detalles
                                    </button>
                                </div>
                            </div>

                            <!-- DEFINICIÓN DE SETS -->
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3">Sets de Trabajo
                                </h4>

                                <div class="space-y-3">
                                    @foreach ($exercise['sets'] as $setIndex => $set)
                                        <div
                                            class="flex space-x-3 items-center bg-white dark:bg-gray-700 p-3 rounded-lg border dark:border-gray-600">
                                            <span
                                                class="font-bold text-lg text-[#7bcb01] w-8">S{{ $setIndex + 1 }}</span>

                                            <!-- Repeticiones (Reps) -->
                                            <div class="flex-1">
                                                <label
                                                    class="block text-xs font-medium text-gray-500 dark:text-gray-400">Reps</label>
                                                <input type="number"
                                                    wire:model.defer="routineData.{{ $index }}.sets.{{ $setIndex }}.reps"
                                                    min="1" required
                                                    class="remove-number-arrows mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#7bcb01] focus:ring-[#7bcb01] dark:bg-gray-600 dark:border-gray-500 dark:text-white text-center">
                                                @error("routineData.$index.sets.$setIndex.reps")
                                                    <span
                                                        class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Peso (KG) -->
                                            <div class="flex-1">
                                                <label
                                                    class="block text-xs font-medium text-gray-500 dark:text-gray-400">Peso
                                                    (KG/LBS)</label>
                                                <input type="number"
                                                    wire:model.defer="routineData.{{ $index }}.sets.{{ $setIndex }}.kg"
                                                    min="0" step="0.5"
                                                    class="remove-number-arrows mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#7bcb01] focus:ring-[#7bcb01] dark:bg-gray-600 dark:border-gray-500 dark:text-white text-center">
                                                @error("routineData.$index.sets.$setIndex.kg")
                                                    <span
                                                        class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Botón Eliminar Set -->
                                            <button type="button"
                                                wire:click="removeSet({{ $index }}, {{ $setIndex }})"
                                                class="p-2 text-gray-400 hover:text-red-500 transition duration-150">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Botón Añadir Set -->
                                <button type="button" wire:click="addSet({{ $index }})"
                                    class="mt-3 w-full border border-dashed border-indigo-500 text-indigo-600 hover:bg-indigo-50 py-2 rounded-lg text-sm font-medium transition duration-150 dark:border-indigo-400 dark:text-indigo-400 dark:hover:bg-gray-700">
                                    + Añadir Set
                                </button>
                            </div>

                        </div>
                    @endforeach
                </section>

                <!-- CONTROLES INFERIORES -->
                <div class="mt-8 pt-6 border-t dark:border-gray-700 flex justify-end">

                    <!-- Botón Asignar -->
                    <button type="submit"
                        class="flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-[#7bcb01] hover:bg-[#5aa301] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#7bcb01] transition duration-300">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Asignar Rutina Personalizada a {{ $targetUser->name }}
                    </button>
                </div>

                @if ($errors->any())
                    <div class="mt-6 p-4 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-red-800 dark:text-red-200"
                        role="alert">
                        <span class="font-medium">¡Atención!</span> Por favor, corrige los errores en el formulario para
                        guardar.
                    </div>
                @endif

            </form>
        @else
            <!-- Mensaje de Error si no se pudo cargar la data -->
            <div class="p-6 bg-red-100 dark:bg-red-900 border border-red-400 rounded-xl text-red-700 dark:text-red-200">
                <h2 class="font-bold text-xl">Error de Carga</h2>
                <p>No se pudo cargar la plantilla o el cliente objetivo. Por favor, verifica la URL e inténtalo de
                    nuevo.</p>
                <a href="{{ route('employee.dashboard') }}" wire:navigate
                    class="mt-3 inline-block font-medium text-red-900 dark:text-red-100 underline hover:no-underline">Volver
                    al Dashboard</a>
            </div>
        @endif

        <!-- MODAL DE DETALLES DEL EJERCICIO (Similar al componente de Creación) -->
        @if ($selectedExerciseDetails)
            <div x-data="{ open: @entangle('showExerciseModal').live }" x-show="open" x-on:close-modal.window="open = false"
                class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <!-- Background overlay -->
                    <div x-show="open" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <!-- Modal Content -->
                    <div x-show="open" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                        <div class="bg-white dark:bg-gray-800 p-6">
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
                                    class="bg-lime-600 hover:bg-lime-700 text-white font-bold py-2 px-8 rounded-lg transition duration-300 ease-in-out shadow-lg transform hover:scale-105">
                                    Entendido / Cerrar
                                </button>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 flex justify-end">
                            <button wire:click="closeModal" @click="open = false" type="button"
                                class="bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                                Entendido / Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Toast/Mensaje de Notificación (Placeholder de Alpine.js) -->
        <div x-data="{ show: false, title: '', message: '', type: 'success' }"
            x-on:toast-message.window="show = true; title = $event.detail.title; message = $event.detail.message; type = $event.detail.type; setTimeout(() => { show = false }, 3000)"
            x-show="show" x-transition.duration.500ms style="display: none;"
            class="fixed bottom-5 right-5 z-50 p-4 rounded-lg shadow-lg text-white font-medium"
            :class="{ 'bg-green-600': type === 'success', 'bg-red-600': type === 'error' }">
            <h4 class="font-bold" x-text="title"></h4>
            <p x-text="message"></p>
        </div>

    </div>
</div>
