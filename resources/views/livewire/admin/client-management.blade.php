<div x-data="{}">

    <style>
        .text-lime { color: #7bcb01 !important; }
        .bg-lime { background-color: #7bcb01 !important; }
        .hover\:bg-lime-darker:hover { background-color: #69b301 !important; }
        .border-lime { border-color: #7bcb01 !important; }

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

        button {
            transition: all 0.25s ease-in-out !important;
        }

        [x-cloak] { display: none !important; }

        .bg-state-active { background-color: rgba(123, 203, 1, 0.15); }
        .text-state-active { color: #5aa101; }
    </style>

    <h1 class="text-3xl font-extrabold mb-8 text-[#7bcb01]">
        Gestión de Clientes
    </h1>

    {{-- ======================================================= --}}
    {{-- CLIENTES PENDIENTES  --}}
    {{-- ======================================================= --}}
    @if($pendingCount > 0)
    <div class="mb-8 p-6 bg-white/95 dark:bg-[#1a1a1a]/95 rounded-xl shadow-lg border border-gray-300">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <h2 class="text-xl font-bold dark:text-gray-100 text-gray-950">
                Pendientes de Verificación
            </h2>
            <span class="px-3 py-1 text-xs sm:text-sm font-bold text-white bg-yellow-500 rounded-full shadow whitespace-nowrap">
                {{ $pendingCount }} Pendientes
            </span>
        </div>

        <div class="overflow-x-auto border border-gray-300 rounded-xl">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider dark:text-gray-100">Nombre</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider dark:text-gray-100">Email</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold uppercase tracking-wider dark:text-gray-100">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($pendingClients as $profile)
                        @php
                            $fullName = trim(($profile->user->name ?? '') . ' ' . ($profile->user->last_name ?? ''));
                        @endphp
                        <tr class="hover:bg-lime/10 transition">
                            <td class="px-4 py-2 font-medium text-gray-800 dark:text-white">{{ $fullName !== '' ? $fullName : 'N/A' }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-white">{{ $profile->user->email ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex flex-wrap items-center justify-center gap-2 sm:flex-nowrap">
                                    {{-- Botón Aprobar --}}
                                    <button
                                        wire:click="confirmToggleVerification({{ $profile->user->id }}, '{{ $fullName }}')"
                                        class="text-sm px-3 py-2 rounded-full font-bold bg-[#7bcb01]/75 hover:bg-lime-darker text-white shadow transition-transform duration-200 hover:scale-105"
                                    >
                                        Aprobar
                                    </button>
                                    {{-- Botón Rechazar (Cambiado a Icono) --}}
                                    <button
                                        wire:click="confirmRejectClient({{ $profile->id }}, '{{ $fullName }}')"
                                        title="Rechazar Cliente"
                                        class="p-2 rounded-full font-bold bg-red-600/90 hover:bg-red-700 text-white shadow transition-transform duration-200 hover:scale-110 flex items-center justify-center h-9 w-9"
                                    >
                                        <!-- Icono de Bote de Basura/Rechazar -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.5H2.5a.75.75 0 0 0 0 1.5h15a.75.75 0 0 0 0-1.5H14v-.5A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4.75a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0ZM5.77 17.5a.75.75 0 0 0 .75-.689l.86-9.178A.75.75 0 0 0 6.63 7h6.74a.75.75 0 0 0-.75 1.033l-.86 9.178a.75.75 0 0 0 .736.812h-.008a.75.75 0 0 0 .737-.812l.86-9.178a.75.75 0 0 0-.737-1.033h-6.74a.75.75 0 0 0-.75 1.033l.86 9.178a.75.75 0 0 0 .737.812h-.008Z" clip-rule="evenodd" />
                                        </svg>
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
    {{-- FILTROS, BÚSQUEDA Y TABLA --}}
    {{-- ======================================================= --}}
    <div class="bg-white/90 dark:bg-[#1a1a1a]/90 dark:text-white shadow-xl rounded-xl overflow-hidden border border-gray-300">

        <div class="p-5 bg-white dark:bg-[#1a1a1a]/90 border-b border-gray-200 dark:border-zinc-800 flex flex-col sm:flex-row gap-4 items-center">
            <div class="flex-grow w-full sm:w-auto">
                {{-- Nota: El componente flux:input/select no se puede generar aquí, se mantiene la estructura original --}}
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Buscar por nombre o email..."
                    class:input="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                />
            </div>

            <div class="w-full sm:w-48 flex-shrink-0 relative">
                <flux:select
                        wire:model.live="filterStatus"
                        class="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                        >
                            @foreach ($statuses as $key => $label)
                                <flux:select.option value="{{ $key }}">{{ $label }}</flux:select.option>
                            @endforeach
                    </flux:select>

            </div>
        </div>

        {{-- TABLA DE CLIENTES --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">N° Emergencia</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Tipo</th> {{-- CAMBIADO DE 'estado' a 'Tipo' --}}
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Verificación</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($users as $user)
                        @php
                            $fullName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
                        @endphp
                        <tr class="hover:bg-lime/10 transition">
                            <td class="px-6 py-3 font-semibold text-gray-800 dark:text-white">{{ $fullName !== '' ? $fullName : 'N/A' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $user->profile?->personal_number ?? 'N/A' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $user->profile?->emergency_contact ?? 'N/A' }}</td>
                            <td class="px-6 py-3">
                                @if ($user->client_type === 'personalized')
                                    <span class="inline-block min-w-[100px] text-center px-3 py-1 text-sm font-bold rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">Personalizado</span>
                                @else
                                    <span class="inline-block min-w-[100px] text-center px-3 py-1 text-sm font-bold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-300">Regular</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                @if ($user->profile?->is_verified)
                                    <span class="inline-block min-w-[100px] text-center px-3 py-1 text-sm font-bold rounded-full bg-state-active text-state-active">Verificado</span>
                                @else
                                    <span class="inline-block min-w-[100px] text-center px-3 py-1 text-sm font-bold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center">
                                <div class="flex flex-wrap items-center justify-center gap-2 sm:flex-nowrap">

                                    {{-- Botón 1: Activar/Desactivar --}}
                                    <button
                                        wire:click="confirmToggleVerification({{ $user->id }}, '{{ $fullName }}')"
                                        class="text-sm px-3 py-2 rounded-full font-bold shadow transition-transform duration-200 hover:scale-105
                                        {{ $user->is_active
                                            ? 'bg-yellow-500/90 hover:bg-yellow-600 text-white'
                                            : 'bg-[#7bcb01]/75 hover:bg-lime-darker text-white' }}">
                                        {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>

                                    {{-- Botón 2: Cambio de Tipo de Cliente (NUEVO) --}}
                                    <button
                                        wire:click="openTypeChangeModal({{ $user->id }})"
                                        title="Cambiar Tipo de Cliente"
                                        class="p-2 rounded-full font-bold bg-gray-400/90 hover:bg-gray-500 text-white shadow transition-transform duration-200 hover:scale-110 flex items-center justify-center h-9 w-9"
                                    >
                                        <!-- Icono de Usuario/Engranaje -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                            <path d="M7 6a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-.58 0l-1.39-1.39a.25.25 0 0 0-.35 0L7.58 9a.75.75 0 0 1-.58-.75V6ZM15 3.75a.75.75 0 0 0-.75-.75h-2.5a.75.75 0 0 1-.75-.75V2.25c0-.414.336-.75.75-.75h2.5c.414 0 .75.336.75.75v.5A.75.75 0 0 1 15 3.75ZM6 3.75a.75.75 0 0 0-.75-.75H2.75A1.75 1.75 0 0 0 1 4.75v10.5c0 .966.784 1.75 1.75 1.75h14.5A1.75 1.75 0 0 0 18 15.25v-10.5c0-.966-.784-1.75-1.75-1.75h-2.5a.75.75 0 0 0-.75.75v.5c0 .414-.336.75-.75.75h-2.5A.75.75 0 0 0 8 3.75v.5c0 .414-.336.75-.75.75h-2.5A.75.75 0 0 0 4 4.75v-1A.75.75 0 0 0 3.25 3H2.75a.25.25 0 0 1-.25-.25v-1A.75.75 0 0 0 1.25 1H.75A.75.75 0 0 0 0 1.75v1.5c0 .414.336.75.75.75h.25a.75.75 0 0 0 .75.75h3.5a.75.75 0 0 0 .75-.75v-1.5c0-.414-.336-.75-.75-.75H5.75A.75.75 0 0 0 5 2.25v1.5A.75.75 0 0 0 5.75 4.5h.5c.414 0 .75-.336.75-.75v-1.5A.75.75 0 0 0 6 1.75v1.5a.75.75 0 0 0 .75.75h.5c.414 0 .75-.336.75-.75V1.75A.75.75 0 0 0 7.75 1H.75A.75.75 0 0 0 0 1.75v1.5c0 .414.336.75.75.75h.25A.75.75 0 0 0 1.75 5h.25a.75.75 0 0 0 .75.75h3.5A.75.75 0 0 0 6.75 5h.5c.414 0 .75-.336.75-.75V3.75A.75.75 0 0 0 7.75 3h.5c.414 0 .75-.336.75-.75V1.75A.75.75 0 0 0 9.75 1H17.25c.414 0 .75.336.75.75v14.5c0 .966-.784 1.75-1.75 1.75H2.75A.75.75 0 0 1 2 16.5V6.75A.75.75 0 0 1 2.75 6H17.25c.414 0 .75-.336.75-.75v-1A.75.75 0 0 0 17.25 4H2.75A.25.25 0 0 1 2.5 3.75V2.25c0-.414.336-.75.75-.75h2.5c.414 0 .75.336.75.75v.5A.75.75 0 0 0 6 3.75V4.5A.75.75 0 0 0 6.75 5h.5a.75.75 0 0 0 .75-.75v-1.5A.75.75 0 0 0 7.75 2.25v1.5a.75.75 0 0 0 .75.75h3.5a.75.75 0 0 0 .75-.75v-1.5A.75.75 0 0 0 13.75 2.25v.5a.75.75 0 0 0 .75.75h2.5A.75.75 0 0 0 17 3.75V4.5A.75.75 0 0 0 17.75 5h.5c.414 0 .75-.336.75-.75v-1.5A.75.75 0 0 0 18.25 2.25v1.5A.75.75 0 0 0 19 4.5h.25A.75.75 0 0 0 20 3.75v-1.5c0-.414-.336-.75-.75-.75H17.25c-.414 0-.75.336-.75.75v1.5c0 .414.336.75.75.75h.5a.75.75 0 0 0 .75-.75v-1.5A.75.75 0 0 0 18.25 1H2.75A.75.75 0 0 0 2 1.75v.5a.75.75 0 0 0 .75.75h3.5A.75.75 0 0 0 7 2.25v-.5A.75.75 0 0 0 6.25 1.75H2.75ZM7 6a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-.58 0l-1.39-1.39a.25.25 0 0 0-.35 0L7.58 9a.75.75 0 0 1-.58-.75V6Z"/>
                                        </svg>
                                    </button>

                                    {{-- Botón 3: Eliminar (Ícono de Cubo de Basura) --}}
                                    <button
                                        wire:click="confirmDeleteUser({{ $user->id }}, '{{ $fullName }}')"
                                        title="Eliminar Usuario"
                                        class="p-2 rounded-full font-bold bg-red-600/90 hover:bg-red-700 text-white shadow transition-transform duration-200 hover:scale-110 flex items-center justify-center h-9 w-9"
                                    >
                                        <!-- Icono de Bote de Basura/Eliminar -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.5H2.5a.75.75 0 0 0 0 1.5h15a.75.75 0 0 0 0-1.5H14v-.5A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4.75a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0ZM5.77 17.5a.75.75 0 0 0 .75-.689l.86-9.178A.75.75 0 0 0 6.63 7h6.74a.75.75 0 0 0-.75 1.033l-.86 9.178a.75.75 0 0 0 .736.812h-.008a.75.75 0 0 0 .737-.812l.86-9.178a.75.75 0 0 0-.737-1.033h-6.74a.75.75 0 0 0-.75 1.033l.86 9.178a.75.75 0 0 0 .737.812h-.008Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No se encontraron usuarios que coincidan con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 dark:border-gray-800">
            {{ $users->links() }}
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- MODAL DE CONFIRMACIÓN GENERAL (EXISTENTE) --}}
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
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-zinc-900/70 backdrop-blur-sm"
        x-transition.opacity
    >
        <div
            class="bg-white dark:bg-[#1a1a1a]/90 rounded-xl shadow-2xl max-w-lg w-full p-6 border-t-4 border-lime transform transition-all relative overflow-hidden"
            x-transition.scale
        >

            <template x-if="!success">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $modalTitle }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">{!! nl2br(e($modalMessage)) !!}</p>

                    <div class="flex justify-end space-x-3 mt-5">
                        <button
                            wire:click="closeModal"
                            class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition font-bold"
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

            <template x-if="success">
                <div class="flex flex-col items-center justify-center text-center py-8 animate-fadeIn">
                    <svg class="w-16 h-16 text-lime mb-3 animate-check" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">Acción realizada con éxito</p>
                </div>
            </template>

        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- NUEVO MODAL: CAMBIO DE TIPO DE CLIENTE --}}
    {{-- ======================================================= --}}
    <div
        x-data="{ show: @entangle('showTypeChangeModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[10000] flex items-center justify-center bg-zinc-900/70 backdrop-blur-sm"
        x-transition.opacity
    >
        <div
            class="bg-white dark:bg-[#1a1a1a]/90 rounded-xl shadow-2xl max-w-md w-full p-6 border-t-4 border-lime transform transition-all relative"
            x-transition.scale
            @click.away="$wire.closeTypeChangeModal()"
        >
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Cambiar Tipo de Cliente
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">
                Ajustando el plan de: <span class="font-bold text-lime">{{ $clientToEditName }}</span>
            </p>

            <form wire:submit.prevent="updateClientType">
                <div class="mb-4">
                    <label for="new_client_type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Tipo de Cliente Actual: <span class="font-normal text-lime">{{ $availableClientTypes[$currentClientType] ?? 'Desconocido' }}</span>
                    </label>

                    <label for="new_client_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">
                        Seleccionar Nuevo Tipo:
                    </label>
                    <select
                        id="new_client_type"
                        wire:model.defer="newClientType"
                        required
                        class="mt-1 block w-full border border-gray-300 dark:border-gray-600 dark:bg-zinc-800 dark:text-white focus:ring-lime focus:border-lime rounded-lg shadow-sm"
                    >
                        @foreach ($availableClientTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('newClientType') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button
                        type="button"
                        wire:click="closeTypeChangeModal"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 transition font-bold"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="updateClientType"
                        class="px-4 py-2 rounded-lg bg-lime hover:bg-lime-darker text-white font-semibold shadow transition disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="updateClientType">Guardar Cambios</span>
                        <span wire:loading wire:target="updateClientType">Guardando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>


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
