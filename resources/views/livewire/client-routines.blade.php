<div class="mt-8">
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

<div class="flex justify-between items-center mb-6 border-b dark:border-gray-700 pb-3">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        Mis Rutinas Creadas
    </h2>
    
    <a href="{{ route('routine.builder') }}" class="flex items-center space-x-2 px-4 py-2 bg-lime-600 text-white font-semibold rounded-lg shadow-md hover:bg-lime-700 transition duration-150">
        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
        <span>Crear Nueva Rutina</span>
    </a>
</div>

@if (!isset($routines) || $routines->isEmpty())
    <div class="text-center p-10 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        <p class="text-xl font-semibold text-gray-700 dark:text-gray-300">
            Aún no tienes rutinas guardadas.
        </p>
        <p class="mt-2 text-gray-500 dark:text-gray-400">
            ¡Empieza a crear tu plan de entrenamiento ahora!
        </p>
        <a href="{{ route('routine.builder') }}" class="mt-4 inline-block font-medium text-lime-600 dark:text-lime-400 hover:text-lime-500 transition duration-150">
            Ir al Constructor de Rutinas &rarr;
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($routines as $routine)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-xl shadow-lg hover:shadow-xl transition duration-300 p-6 border-t-4 border-lime-500">
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
                        class="text-sm px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Ver Detalles
                    </button>
                    
                    {{-- 🎯 CAMBIO CLAVE: Llama a la función que abre el modal --}}
                    <button wire:click="confirmRoutineDeletion({{ $routine->id }})"
                        class="text-sm px-4 py-2 bg-red-100 text-red-600 font-medium rounded-lg hover:bg-red-200 transition">
                        Eliminar
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- 🎯 ESTRUCTURA DEL MODAL DE ELIMINACIÓN --}}
@if ($showDeleteModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 transition-opacity duration-300">
        {{-- Usar @click.outside para cerrar el modal al hacer clic fuera --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-2xl max-w-sm w-full transition-transform duration-300 scale-100"
             @click.away="() => {}">
            
            <h3 class="text-xl font-bold text-red-600 mb-4">Confirmar Eliminación</h3>
            
            <p class="text-gray-700 dark:text-gray-300 mb-6">
                ¿Estás seguro de que deseas eliminar la rutina **{{ $routineToDeleteName }}**?
                <br>Esta acción es **irreversible** y también eliminará todos sus ejercicios asociados.
            </p>
            
            <div class="flex justify-end space-x-3">
                <button wire:click="closeModal"
                        type="button"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    Cancelar
                </button>
                <button wire:click="deleteRoutine"
                        type="button"
                        class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg shadow-md hover:bg-red-700 transition">
                    Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
@endif

</div>