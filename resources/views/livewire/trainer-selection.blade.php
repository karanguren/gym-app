<div class="p-6 sm:px-10 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 min-h-screen"
x-data="{
showToast: false,
toastMessage: '',
toastType: 'success',

selectedTrainerName: '',

// El estado de los modales ahora se gestiona en Livewire, 
// pero necesitamos `selectedTrainerName` en Alpine para el modal.

// Se mantiene `openModal` solo para establecer el nombre en Alpine, el Livewire 
// se encargará de mostrarlo.
openModal(trainerName) {
    this.selectedTrainerName = trainerName;
    // El componente Livewire gestionará la apertura del modal al mismo tiempo
},

// Función de ayuda para confirmar la acción de desvincular
openDetachModal() {
    @this.call('openDetachModal');
},

confirmSelection() {
    // La lógica de confirmación se mueve a Livewire (selectTrainer)
    @this.call('selectTrainer');
},

confirmDetach() {
    // La lógica de desvinculación se llama desde Livewire
    @this.call('detachTrainer');
}


}"
x-init="@this.on('show-toast', (event) => {
toastMessage = event[0].message;
toastType = event[0].type || 'success';
showToast = true;
setTimeout(() => showToast = false, 3500);
})">

<h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-8">Selecciona tu Entrenador Personal</h1>

{{-- Estado de Asignación --}}
@php
$status = $this->clientProfile->assignment_status ?? 'unassigned';
$assignedTrainer = $this->clientProfile->assignedTrainer;
$requestedTrainer = $this->clientProfile->requestedTrainer;
$trainer = $assignedTrainer ?? $requestedTrainer;

$statusClasses = [
'unassigned' => 'bg-gray-100 text-gray-600',
'pending' => 'bg-blue-100 text-blue-800 ring-2 ring-blue-500/50 animate-pulse',
'accepted' => 'bg-green-100 text-green-800',
'rejected' => 'bg-red-100 text-red-800',
];

$statusText = [
'unassigned' => 'Actualmente no tienes un entrenador asignado.',
'pending' => 'Solicitud pendiente de aprobación de ' . ($requestedTrainer->name ?? 'un entrenador') . '.',
'accepted' => '¡Felicidades! Estás siendo entrenado por ' . ($assignedTrainer->name ?? 'un entrenador asignado') . '.',
'rejected' => 'Tu solicitud fue rechazada. Por favor, selecciona otro entrenador.',
];

@endphp

<div class="mb-8 p-4 sm:p-6 rounded-lg shadow-lg dark:bg-gray-800 border-l-4 border-indigo-500 transition duration-300 {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-600' }}">
<div class="flex items-center justify-between flex-wrap">
<p class="text-sm font-semibold dark:text-white">
<span class="font-extrabold text-lg mr-2 uppercase">{{ $status }}</span>
{{ $statusText[$status] ?? 'Estado desconocido.' }}
</p>
@if ($status === 'accepted' || $status === 'pending' || $status === 'rejected')
<button @click="openDetachModal()"
class="mt-2 sm:mt-0 px-4 py-2 text-xs font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition duration-150 shadow-md">
@if ($status === 'pending')
Cancelar Solicitud
@else
Desvincular Entrenador
@endif
</button>
@endif
</div>

{{-- Mostrar el nombre del entrenador actual si existe --}}
@if ($trainer)
<p class="mt-2 text-sm italic font-medium dark:text-gray-300">
Entrenador: {{ $trainer->name }} ({{ $trainer->email }})
</p>
@endif

{{-- Instrucción para rechazo --}}
@if ($status === 'rejected')
<p class="mt-2 text-sm dark:text-gray-300">
Puedes solicitar a otro entrenador de la lista a continuación.
</p>
@endif

</div>

{{-- Formulario de Búsqueda --}}

<div class="mb-6">
<input wire:model.live.debounce.300ms="search"
type="text"
placeholder="Buscar entrenador por nombre o email..."
class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm transition duration-150">
</div>

{{-- Lista de Entrenadores --}}

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
@forelse ($trainers as $trainerItem)
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition duration-300 p-5 flex flex-col justify-between">
<div>
<h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $trainerItem->name }}</h2>
<p class="text-sm text-indigo-600 dark:text-indigo-400 mt-1">{{ $trainerItem->email }}</p>

        {{-- Etiqueta de Entrenador Asociado --}}
        @if ($trainerItem->id === $assignedTrainer?->id)
            <span class="mt-3 inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 shadow-sm">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                ASIGNADO
            </span>
        @elseif ($trainerItem->id === $requestedTrainer?->id && $status === 'pending')
             <span class="mt-3 inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 shadow-sm animate-pulse">
                SOLICITUD PENDIENTE
            </span>
        @endif
    </div>

    <div class="mt-4">
        <button wire:click="openSelectionModal({{ $trainerItem->id }}, '{{ $trainerItem->name }}')" 
                @click="openModal('{{ $trainerItem->name }}')"
                {{-- Deshabilitar si ya hay una solicitud pendiente o ya está asignado --}}
                @if ($status === 'pending' || $trainerItem->id === $assignedTrainer?->id) disabled @endif
                class="w-full py-2 px-4 rounded-lg font-bold text-white transition duration-300 shadow-md
                    @if ($status === 'pending' || $trainerItem->id === $assignedTrainer?->id)
                        bg-gray-400 cursor-not-allowed
                    @else
                        bg-indigo-600 hover:bg-indigo-700
                    @endif">
            @if ($trainerItem->id === $assignedTrainer?->id)
                Actual Entrenador
            @elseif ($status === 'pending')
                Solicitud Pendiente
            @else
                Solicitar Entrenador
            @endif
        </button>
    </div>
</div>


@empty
<div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
<p class="text-gray-500 dark:text-gray-400">No se encontraron entrenadores que coincidan con la búsqueda.</p>
</div>
@endforelse

</div>

<div class="mt-8">
{{ $trainers->links() }}
</div>

{{-- MODAL 1: CONFIRMACIÓN DE SELECCIÓN (LIVEWIRE CONTROLADO) --}}

{{-- Usamos wire:model.live para enlazar la visibilidad al componente Livewire --}}

<div x-data="{ show: @entangle('showSelectionModal') }" x-show="show" x-cloak
class="fixed inset-0 z-50 overflow-y-auto"
style="display: none;">
<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
{{-- Fondo Oscuro (Añadimos z-40 para que el contenido del modal pueda superarlo con z-50) --}}
<div x-show="show" @click="show = false" wire:click="closeSelectionModal"
x-transition:enter="ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 z-40" aria-hidden="true"></div>

<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

{{-- Contenido del Modal (Añadimos z-50 y relative para garantizar que esté por encima del fondo) --}}
<div x-show="show"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
     class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50">

    <div class="bg-white dark:bg-gray-800 px-6 py-5">
        <svg class="mx-auto h-12 w-12 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 13h-4"/><path d="m15 16-3-3 3-3"/></svg>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-3 text-center" id="selection-title">Confirmar Solicitud</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 mb-6 text-center">
            Estás a punto de enviar una solicitud para que <span class="font-bold" x-text="selectedTrainerName"></span> te entrene. Ellos deberán aceptar la solicitud.
        </p>

        <div class="flex justify-end space-x-3">
            <button wire:click="closeSelectionModal"
                    type="button"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                Cancelar
            </button>
            <button wire:click="selectTrainer"
                    type="button"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                Enviar Solicitud
            </button>
        </div>
    </div>
</div>


</div>

</div>

{{-- MODAL 2: CONFIRMACIÓN DE DESVINCULACIÓN (NUEVO, LIVEWIRE CONTROLADO) --}}

<div x-data="{ show: @entangle('showDetachModal') }" x-show="show" x-cloak
class="fixed inset-0 z-50 overflow-y-auto"
style="display: none;">
<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
{{-- Fondo Oscuro (Añadimos z-40) --}}
<div x-show="show" @click="show = false" wire:click="closeDetachModal"
x-transition:enter="ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0"
class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 z-40" aria-hidden="true"></div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

    {{-- Contenido del Modal (Añadimos z-50 y relative) --}}
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full relative z-50">
        
        <div class="bg-white dark:bg-gray-800 px-6 py-5">
            <svg class="mx-auto h-12 w-12 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.83 18a2 2 0 0 0 1.73 3h16.88a2 2 0 0 0 1.73-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-3 text-center" id="detach-title">Confirmar Desvinculación</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 mb-6 text-center">
                ¿Estás seguro de que deseas desvincularte de tu entrenador actual? Esta acción cancelará tu solicitud pendiente o removerá tu asignación aceptada.
            </p>
            
            <div class="flex justify-end space-x-3">
                <button wire:click="closeDetachModal"
                        type="button"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    No, Cancelar
                </button>
                <button wire:click="detachTrainer"
                        type="button"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                    Sí, Desvincular
                </button>
            </div>
        </div>
    </div>
</div>


</div>

{{-- Toast de Notificación --}}

<div x-show="showToast"
x-transition:enter="ease-out duration-300"
x-transition:enter-start="opacity-0 translate-y-2"
x-transition:enter-end="opacity-100 translate-y-0"
x-transition:leave="ease-in duration-200"
x-transition:leave-start="opacity-100 translate-y-0"
x-transition:leave-end="opacity-0 translate-y-2"
class="fixed bottom-5 right-5 z-50 p-4 rounded-lg shadow-xl flex items-center space-x-3"
:class="{
'bg-green-500 text-white': toastType === 'success',
'bg-red-500 text-white': toastType === 'error',
'bg-blue-500 text-white': toastType === 'info',
'bg-yellow-500 text-white': toastType === 'warning',
}"
style="display: none;">
<span x-text="toastMessage" class="font-semibold"></span>
</div>