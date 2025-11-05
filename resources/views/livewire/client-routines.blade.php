<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="md:p-8">
            @if (session()->has('info'))
                <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                    {{ session('info') }}
                </div>
            @endif
            @if (session()->has('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b dark:border-gray-700 pb-3 space-y-3 sm:space-y-0">
    
                <h2 class="titles">
                    Mis Rutinas Creadas
                </h2>
                
                <a href="{{ route('routine.builder') }}" 
                class="w-full sm:w-auto flex flex-rowspace-x-2 btn-ouline-ve">
                    <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                    <span>Crear Nueva Rutina</span>
                </a>
            </div>

            @if (!isset($routines) || $routines->isEmpty())
                <div class="text-center p-10 bg-gray-100 dark:bg-[#1a1a1a] rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <svg class="w-12 h-12 svg-ve mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <p class="text-xl font-semibold text-gray-700 dark:text-gray-300">
                        Aún no tienes rutinas guardadas.
                    </p>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">
                        ¡Empieza a crear tu plan de entrenamiento ahora!
                    </p>
                    <a href="{{ route('routine.builder') }}" class="mt-4 inline-block btn-ouline-ve">
                        Ir al Constructor de Rutinas &rarr;
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($routines as $routine)
                        <div class="card-tb-ve-v2">
                            <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2 truncate" title="{{ $routine->name }}">
                                {{ $routine->name }}
                            </h3>
                            
                            <div class="flex items-center text-gray-600 dark:text-gray-400 mb-4">
                                <svg class="w-5 h-5 mr-2 text-lime-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.4 14.4 9 18l-1.5-1.5L6 18l3.6-3.6M14 6h4M16 4v4M12 12l-6 6M12 12l6 6"/></svg>
                                <span class="text-lg font-semibold">{{ $routine->exercises_count }} Ejercicios</span>
                            </div>

                            <p class="text-sm text-gray-500 dark:text-gray-400 italic mb-4">
                                Creada: {{ $routine->created_at->diffForHumans() }}
                            </p>

                            <div class="flex justify-end space-x-3">
                                <button wire:click="viewRoutineDetails({{ $routine->id }})"
                                    class="btn-ouline-ve">
                                    Ver Detalles
                                </button>
                                    
                                <button wire:click="confirmRoutineDeletion({{ $routine->id }})"
                                    class="btn-ouline-ro">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 🎯 ESTRUCTURA DEL MODAL DE ELIMINACIÓN --}}
            @if ($showDeleteModal)
                <div class=" bg-modal flex items-center justify-center">
                    <div class="card-tb-ro-v2"
                        @click.away="() => {}">
                        
                        <h3 class="text-xl font-bold text-red-600 mb-4">Confirmar Eliminación</h3>
                        
                        <p class="text-gray-700 dark:text-gray-300 mb-6">
                            ¿Estás seguro de que deseas eliminar la rutina **{{ $routineToDeleteName }}**?
                            <br>Esta acción es **irreversible** y también eliminará todos sus ejercicios asociados.
                        </p>
                        
                        <div class="flex justify-end space-x-3">
                            <button wire:click="closeModal"
                                    type="button"
                                    class="btn-ouline-ve">
                                Cancelar
                            </button>
                            <button wire:click="deleteRoutine"
                                    type="button"
                                    class="btn-ouline-ro">
                                Sí, Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>