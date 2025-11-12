<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="md:p-8">
            <style>
                [x-cloak] {
                    display: none !important;
                }
            </style>

            <h1 class="titles-border">
                Gestión de Clientes
            </h1>

            {{-- ======================================================= --}}
            {{-- CLIENTES PENDIENTES  --}}
            {{-- ======================================================= --}}
            @if ($pendingCount > 0)
                <div class="div-table-principal mb-8">

                    <div class="p-5 flex flex-wrap items-center justify-between gap-2 mb-4">
                        <h2 class="title-table">
                            Pendientes de Verificación
                        </h2>
                        <span
                            class="px-3 py-1 text-xs sm:text-sm font-bold text-white bg-yellow-500 rounded-full shadow whitespace-nowrap">
                            {{ $pendingCount }} Pendientes
                        </span>
                    </div>

                    <div class="">
                        <table class="tables">
                            <thead class="tables-th">
                                <tr>
                                    <th class="text-left text-table-th">Nombre</th>
                                    <th class="text-left text-table-th">Email</th>
                                    <th class="text-center text-table-th">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="tables-tbody">
                                @foreach ($pendingClients as $profile)
                                    @php
                                        $fullName = trim(
                                            ($profile->user->name ?? '') . ' ' . ($profile->user->last_name ?? ''),
                                        );
                                    @endphp
                                    <tr class="hover:bg-lime/10 transition">
                                        <td class="px-4 py-2 text-xs text-gray-800 dark:text-white">
                                            {{ $fullName !== '' ? $fullName : 'N/A' }}</td>
                                        <td class="px-4 py-2 text-xs text-gray-600 dark:text-white">
                                            {{ $profile->user->email ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-xs">
                                            <div
                                                class="flex flex-wrap items-center justify-center gap-2 sm:flex-nowrap">
                                                <button
                                                    wire:click="confirmToggleVerification({{ $profile->user->id }}, '{{ $fullName }}')"
                                                    class="w-28 btn-outline-lime">
                                                    Aprobar
                                                </button>

                                                <button
                                                    wire:click="confirmRejectClient({{ $profile->id }}, '{{ $fullName }}')"
                                                    class="w-28 btn-outline-red">
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
            {{-- FILTROS, BÚSQUEDA Y TABLA --}}
            {{-- ======================================================= --}}
            <div class="div-table-principal">

                <div class="p-5 bg-white dark:bg-[#1a1a1a]/90 border-b border-gray-200 dark:border-zinc-800 flex flex-col sm:flex-row gap-4 items-center">
                    <div class="flex-grow w-full sm:w-auto">
                        <flux:input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar por nombre o email..." class:input="!w-full inputs" />
                    </div>

                    <div class="w-full sm:w-48 flex-shrink-0 relative">
                        <flux:select wire:model.live="filterStatus" class="!w-full inputs">
                            @foreach ($statuses as $key => $label)
                                <flux:select.option value="{{ $key }}">{{ $label }}</flux:select.option>
                            @endforeach
                        </flux:select>

                    </div>
                </div>

                {{-- TABLA DE CLIENTES --}}
                <div class="">
                    <table class="tables">
                        <thead class="tables-th">
                            <tr>
                                <th class="text-left text-table-th">
                                    Nombre</th>
                                <th class="text-left text-table-th">
                                    Email</th>
                                <th class="text-left text-table-th">
                                    Teléfono</th>
                                <th class="text-left text-table-th">
                                    N° Emergencia</th>
                                <th class="text-left text-table-th">
                                    Tipo</th> 
                                <th class="text-left text-table-th">
                                    Verificación</th>
                                <th class="text-center text-table-th">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="tables-tbody">
                            @forelse ($users as $user)
                                @php
                                    $fullName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
                                @endphp
                                <tr class="hover:bg-lime/10 transition">
                                    <td class="px-6 py-3 text-xs font-semibold text-gray-800 dark:text-white">
                                        {{ $fullName !== '' ? $fullName : 'N/A' }}</td>
                                    <td class="px-6 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $user->email }}
                                    </td>
                                    <td class="px-6 py-3 text-xs text-gray-600 dark:text-gray-300">
                                        {{ $user->profile?->personal_number ?? 'N/A' }}</td>
                                    <td class="px-6 py-3 text-xs text-gray-600 dark:text-gray-300">
                                        {{ $user->profile?->emergency_contact ?? 'N/A' }}</td>
                                    <td class="px-6 py-3">
                                        @if ($user->client_type === 'personalized')
                                            <span
                                                class="inline-block w-30 text-center px-3 py-1 text-xs font-bold rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">Personalizado</span>
                                        @else
                                            <span
                                                class="inline-block w-30 text-center px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700/80 dark:text-gray-300">Regular</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        @if ($user->profile?->is_verified)
                                            <span
                                                class="inline-block min-w-[100px] text-center px-3 py-1 text-xs font-bold rounded-full bg-[#7bcb0126] text-[#5aa101]">Verificado</span>
                                        @else
                                            <span
                                                class="inline-block min-w-[100px] text-center px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-center">
                                        <div class="flex flex-wrap items-center justify-center gap-2 sm:flex-nowrap">
                                            <button
                                                wire:click="confirmToggleVerification({{ $user->id }}, '{{ $fullName }}')"
                                                class=" {{ $user->is_active ? 'w-28 btn-outline-yellow' : 'w-28 btn-outline-lime' }}">
                                                {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>

                                            <button wire:click="openTypeChangeModal({{ $user->id }})"
                                                title="Cambiar Tipo de Cliente" class="w-28 btn-outline-grey">
                                                Cambiar Tipo
                                            </button>

                                            <button
                                                wire:click="confirmDeleteUser({{ $user->id }}, '{{ $fullName }}')"
                                                title="Eliminar Usuario" class="w-28 btn-outline-red">
                                                Eliminar
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
            {{-- CAMBIO DE TIPO DE CLIENTE --}}
            {{-- ======================================================= --}}
            <div x-data="{ show: @entangle('showTypeChangeModal') }" x-show="show" x-cloak class="bg-modal" x-transition.opacity>
                <div class="max-w-md w-full modal-card transform transition-all relative" x-transition.scale
                    @click.away="$wire.closeTypeChangeModal()">
                    <h3 class="modalTitle">
                        Cambiar Tipo de Cliente
                    </h3>

                    <form wire:submit.prevent="updateClientType">
                        <div class="mb-4">
                            <label for="new_client_type"
                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                Tipo de Cliente Actual: <span class="font-normal text-lime">
                                    {{ $availableClientTypes[$currentClientType] ?? 'Desconocido' }}</span>
                            </label>
                            <flux:select :label="__('Seleccionar')" wire:model.defer="newClientType"
                                id="new_client_type" class="!w-full inputs" required>
                                @foreach ($availableClientTypes as $key => $label)
                                    <flux:select.option value="{{ $key }}">{{ $label }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            @error('newClientType')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeTypeChangeModal" class="btn-outline-red">
                                Cancelar
                            </button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="updateClientType"
                                class="btn-outline-lime">
                                <span wire:loading.remove wire:target="updateClientType">Guardar Cambios</span>
                                <span wire:loading wire:target="updateClientType">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
