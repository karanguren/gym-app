<div x-data="{}">

{{-- ======================================================= --}}
{{-- ESTILOS PERSONALIZADOS --}}
{{-- ======================================================= --}}
<style>
/* Verde Lima base */
.text-lime { color: #7bcb01 !important; }
.bg-lime { background-color: #7bcb01 !important; }
.hover\:bg-lime-darker:hover { background-color: #69b301 !important; }
.border-lime { border-color: #7bcb01 !important; }

/* Input y Select */
input, select {
    padding: 0.6rem 0.8rem !important;
    border-radius: 0.75rem !important;
    border-width: 1.5px !important;
    border-color: #d1d5db !important; /* gris claro */
    transition: all 0.2s ease-in-out;
}
input:focus, select:focus {
    border-color: #7bcb01 !important;
    box-shadow: 0 0 0 3px rgba(123, 203, 1, 0.25) !important;
    outline: none;
}

/* Botones */
button {
    transition: all 0.25s ease-in-out !important;
}

/* Borde del contenedor */
.card-border-lime {
    border: 1.5px solid #7bcb01;
}

/* Alpine x-cloak */
[x-cloak] { display: none !important; }
</style>

{{-- ======================================================= --}}
{{-- TÍTULO --}}
{{-- ======================================================= --}}
<h1 class="text-3xl font-extrabold mb-8 text-[#7bcb01]">
    Gestión de Clientes
</h1>

{{-- ======================================================= --}}
{{-- CLIENTES PENDIENTES (Verificación de perfil) --}}
{{-- ======================================================= --}}
@if($pendingCount > 0)
<div class="mb-8 p-6 bg-white/95 dark:bg-[#1a1a1a]/95 rounded-xl shadow-lg border-l-4 border-lime card-border-lime">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
        <h2 class="text-xl font-bold text-[#7bcb01]">
            Pendientes de Verificación
        </h2>
        <span class="px-3 py-1 text-xs sm:text-sm font-bold text-white bg-yellow-500 rounded-full shadow whitespace-nowrap">
            {{ $pendingCount }} Pendientes
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-[#7bcb01]">
            <thead class="bg-gray-50 dark:bg-[#1a1a1a]/95">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-[#7bcb01] uppercase tracking-wider">Nombre</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-[#7bcb01] uppercase tracking-wider">Email</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-[#7bcb01] uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($pendingClients as $profile)
                @php
                    $fullName = trim(($profile->user->name ?? '') . ' ' . ($profile->user->last_name ?? ''));
                @endphp
                <tr class="hover:bg-lime/10 transition">
                    <td class="px-4 py-2 font-medium text-gray-800 dark:text-white">{{ $fullName !== '' ? $fullName : 'N/A' }}</td>
                    <td class="px-4 py-2 text-gray-600 dark:text-white">{{ $profile->user->email ?? 'N/A' }}</td>
                    <td class="px-4 py-2 text-center">
                        <div class="flex flex-wrap justify-center gap-2 sm:flex-nowrap">
                            <button 
                                wire:click="confirmToggleVerification({{ $profile->user->id }}, '{{ $fullName }}')" 
                                class="text-sm w-28 sm:w-32 px-3 py-2 rounded-full font-bold bg-lime hover:bg-lime-darker text-white shadow transition-transform duration-200 hover:scale-105"
                            >
                                Aprobar
                            </button>

                            <button 
                                wire:click="confirmRejectClient({{ $profile->id }}, '{{ $fullName }}')" 
                                class="text-sm w-28 sm:w-32 px-3 py-2 rounded-full font-bold bg-red-600 hover:bg-red-700 text-white shadow transition-transform duration-200 hover:scale-105"
                            >
                                Rechazar
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ======================================================= --}}
{{-- FILTROS Y BÚSQUEDA --}}
{{-- ======================================================= --}}
<div class="mb-8 p-5 bg-white/95 dark:bg-[#1a1a1a]/95 rounded-xl shadow-lg flex flex-col sm:flex-row gap-4 items-center border border-lime">
    <div class="flex-grow w-full sm:w-auto">
        <input wire:model.live.debounce.300ms="search" id="search" type="text"
            placeholder="Buscar por nombre o email..."
            class="w-full dark:bg-[#1a1a1a]/95 dark:text-white" />
    </div>

    <div class="w-full sm:w-48 flex-shrink-0 relative">
        <select
            wire:model.live="filterStatus"
            id="filterStatus"
            class="w-full appearance-none pr-8 pl-3 py-2 border border-gray-300 rounded-lg
                bg-white dark:bg-[#1a1a1a]/95 dark:text-white dark:border-gray-700
                focus:outline-none focus:ring-2 focus:ring-lime focus:border-lime
                cursor-pointer"
        >
            @foreach ($statuses as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
            @endforeach
        </select>

        <!-- Flecha personalizada -->
        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- TABLA DE CLIENTES --}}
{{-- ======================================================= --}}
<div class="bg-white/95 dark:bg-[#1a1a1a]/95 dark:text-white shadow-xl rounded-xl overflow-hidden border border-lime">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-[#7bcb01]">
            <thead class="bg-white/95 dark:bg-[#1a1a1a]/95">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[#7bcb01] uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[#7bcb01] uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[#7bcb01] uppercase">Teléfono</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[#7bcb01] uppercase">N° Emergencia</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[#7bcb01] uppercase">estado</th> 
                    <th class="px-6 py-3 text-left text-xs font-semibold text-[#7bcb01] uppercase">Verificación</th> 
                    <th class="px-6 py-3 text-center text-xs font-semibold text-[#7bcb01] uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($users as $user)
                @php
                    $fullName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
                @endphp
                    <tr class="hover:bg-lime/10 transition">
                        <td class="px-6 py-3 font-semibold text-gray-800 dark:text-white">{{ $fullName !== '' ? $fullName : 'N/A' }}</td>
                        <td class="px-6 py-3 text-gray-600 dark:text-white">{{ $user->email }}</td>
                        <td class="px-6 py-3 text-gray-600 dark:text-white">{{ $user->profile?->personal_number ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-gray-600 dark:text-white">{{ $user->profile?->emergency_contact ?? 'N/A' }}</td>
                        <td class="px-6 py-3">
                            @if ($user->is_active)
                                <span class="inline-block min-w-[100px] text-center px-3 py-1 text-sm font-bold rounded-full bg-[#7bcb01]/20 text-lime">Activo</span>
                            @else
                                <span class="inline-block min-w-[100px] text-center px-3 py-1 text-sm font-bold rounded-full bg-red-100 text-red-700">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if ($user->profile?->is_verified)
                                <span class="inline-block min-w-[100px] text-center px-3 py-1 text-sm font-bold rounded-full bg-[#7bcb01]/20 text-lime">Verificado</span>
                            @else
                                <span class="inline-block min-w-[100px] text-center px-3 py-1 text-sm font-bold rounded-full bg-yellow-100 text-yellow-700">Pendiente</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            <div class="flex flex-wrap justify-center gap-2 sm:flex-nowrap">
                                <button
                                    wire:click="confirmToggleVerification({{ $user->id }}, '{{ $fullName }}')"
                                    class="text-sm w-28 sm:w-32 px-3 py-2 rounded-full font-bold shadow
                                    {{ $user->is_active 
                                        ? 'bg-yellow-500 hover:bg-yellow-600 text-white' 
                                        : 'bg-lime hover:bg-lime-darker text-white' }}">
                                    {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                </button>

                                <button
                                    wire:click="confirmDeleteUser({{ $user->id }}, '{{ $fullName }}')"
                                    class="text-sm w-28 sm:w-32 px-3 py-2 rounded-full font-bold bg-red-600 hover:bg-red-700 text-white shadow">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        {{-- Colspan ajustado a 6 --}}
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No se encontraron usuarios que coincidan con los filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4">
        {{ $users->links() }}
    </div>
</div>

{{-- ======================================================= --}}
{{-- MODAL DE CONFIRMACIÓN (con animación de éxito) --}}
{{-- ======================================================= --}}
<div 
    x-data="{ 
        show: @entangle('showConfirmationModal'), 
        success: false,
        confirmAction() {
            $wire.call('executeModalAction')
            this.success = true;
            setTimeout(() => {
                this.show = false;
                this.success = false;
            }, 1800);
        }
    }" 
    x-show="show" 
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
    x-transition.opacity
>
    <div 
        class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 border-t-4 border-lime transform transition-all relative overflow-hidden"
        x-transition.scale
    >

        {{-- Estado normal --}}
        <template x-if="!success">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $modalTitle }}</h3>
                <p class="text-sm text-gray-600 mb-5">{!! nl2br(e($modalMessage)) !!}</p>

                <div class="flex justify-end space-x-3 mt-5">
                    <button 
                        wire:click="closeModal"
                        class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        @click="confirmAction()" 
                        class="px-4 py-2 rounded-lg bg-lime hover:bg-lime-darker text-white font-semibold shadow transition"
                    >
                        Confirmar
                    </button>
                </div>
            </div>
        </template>

        {{-- Estado de éxito --}}
        <template x-if="success">
            <div class="flex flex-col items-center justify-center text-center py-8 animate-fadeIn">
                <svg class="w-16 h-16 text-lime mb-3 animate-check" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-lg font-semibold text-gray-800">Acción realizada con éxito</p>
            </div>
        </template>

    </div>
</div>

{{-- ======================================================= --}}
{{-- ANIMACIONES CSS --}}
{{-- ======================================================= --}}
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
    animation: fadeIn 0.5s ease-out forwards;
}

@keyframes check {
    from { stroke-dasharray: 0, 30; opacity: 0.5; }
    to { stroke-dasharray: 30, 0; opacity: 1; }
}
.animate-check path {
    stroke-dasharray: 30, 0;
    animation: check 0.6s ease-out forwards;
}
</style>

</div>
