<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="md:p-8">
            <style>
                [x-cloak] {
                    display: none !important;
                }

                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: scale(0.9);
                    }

                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }

                .animate-fadeIn {
                    animation: fadeIn 0.5s ease-out forwards;
                }

                @keyframes check {
                    from {
                        stroke-dasharray: 0, 30;
                        opacity: 0.5;
                    }

                    to {
                        stroke-dasharray: 30, 0;
                        opacity: 1;
                    }
                }

                .bg-state-active {
                    background-color: rgba(123, 203, 1, 0.15);
                }

                .text-state-active {
                    color: #5aa101;
                }
            </style>


            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-gray-100 mb-6 border-b dark:border-gray-700 pb-2 flex justify-between items-center text-lime">
                <span class="titles">Gestión de Staff</span>
                <button wire:click="$set('showCreateModal', true)" class="w-28 sm:w-32 btn-outline-lime">
                    + Crear Nuevo
                </button>
            </h2>

            {{-- @if (session()->has('success'))
                <div
                    class="bg-green-100 dark:bg-green-800 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-4 mb-4 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div
                    class="bg-red-100 dark:bg-red-800 border-l-4 border-red-500 text-red-700 dark:text-red-200 p-4 mb-4 rounded-md">
                    {{ session('error') }}
                </div>
            @endif --}}

            {{-- ======================================================= --}}
            {{-- FILTROS, BÚSQUEDA Y LISTA --}}
            {{-- ======================================================= --}}
            <div class="div-table-principal">

                <div class="p-5 bg-white dark:bg-[#1a1a1a]/95 border-b border-gray-200 dark:border-zinc-800 flex flex-col sm:flex-row gap-4 items-center">
                    <div class="flex-grow w-full sm:w-auto">
                        <flux:input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar por nombre o email..." class:input="!w-full inputs" />
                    </div>

                    <div class="w-full sm:w-48 flex-shrink-0 relative">
                        <flux:select wire:model.live="filterRole" class="!w-full inputs">
                            <flux:select.option value="all">Mostrar Todo</flux:select.option>
                            <flux:select.option value="trainer">Entrenadores</flux:select.option>
                            {{-- <flux:select.option value="nutriologo">Nutriólogos</flux:select.option> --}}
                        </flux:select>
                    </div>
                </div>

                {{-- TABLA DE EMPLEADOS --}}
                <div class="overflow-x-auto">
                    <table class="tables">
                        <thead class="tables-th">
                            <tr>
                                <th class="text-left text-table-th">
                                    Staff </th>
                                <th class="text-left text-table-th">
                                    Rol Actual</th>
                                <th class="text-left text-table-th">
                                    Estado</th>
                                <th class="text-left text-table-th">
                                    Cambiar Rol</th>
                                <th class="text-center text-table-th">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="tables-tbody">
                            @forelse ($users as $user)
                                @php
                                    $fullName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? ''));
                                @endphp
                                <tr class="hover:bg-lime/10 transition" wire:key="{{ $user->id }}">
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-medium text-gray-900 dark:text-white">
                                            {{ $fullName !== '' ? $fullName : 'N/A' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if ($user->role == 'trainer') bg-green-100/50 text-green-800 dark:bg-green-800/50 dark:text-green-300 
                                            @elseif($user->role == 'nutriologo')
                                                bg-blue-100/50 text-blue-800 dark:bg-blue-800/50 dark:text-blue-300
                                            @else 
                                                bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ $roleLabels[$user->role] ?? ucfirst($user->role) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->is_active)
                                            <span
                                                class="inline-block min-w-[100px] text-center px-3 py-1 text-xs font-bold rounded-full bg-state-active text-state-active">Activo</span>
                                        @else
                                            <span
                                                class="inline-block min-w-[100px] text-center px-3 py-1 text-xs font-bold rounded-full bg-red-100/50 text-red-700 dark:bg-red-900/50 dark:text-red-300">Inactivo</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium relative">
                                        <flux:select
                                            wire:change="confirmRoleChange({{ $user->id }}, $event.target.value, '{{ $fullName }}')"
                                            class="!w-full inputs text-xs">
                                            @foreach ($staffRoles as $roleKey => $roleLabel)
                                                @if ($roleKey == 'trainer' || $roleKey == 'nutriologo')
                                                    <option value="{{ $roleKey }}" 
                                                        @selected($user->role == $roleKey)>
                                                        {{ $roleLabel }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </flux:select>
                                    </td>

                                    <td class="px-6 py-4 text-center text-xs font-medium">
                                        <div class="flex flex-wrap justify-center gap-2 sm:flex-nowrap">
                                            {{-- BOTÓN ACTIVAR/DESACTIVAR (Outline) --}}
                                            <button
                                                wire:click="confirmToggleActiveStatus({{ $user->id }}, '{{ $fullName }}')"
                                                class=" w-28 sm:w-32 @if ($user->is_active) btn-outline-yellow @else btn-outline-lime @endif">
                                                {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>

                                            {{-- BOTÓN ELIMINAR (Outline) --}}
                                            <button
                                                wire:click="confirmDeleteUser({{ $user->id }}, '{{ $fullName }}')"
                                                class=" w-28 sm:w-32 btn-outline-red">
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No se encontró staff que coincida con los filtros.
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
            {{-- MODAL DE CREACIÓN DE EMPLEADO --}}
            {{-- ======================================================= --}}
            <div x-data="{ open: @entangle('showCreateModal').live }" x-show="open" x-transition.opacity.scale.80 x-cloak class="bg-modal">
                <div x-show="open" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" @click.away="open = false"
                    class="max-w-lg w-full modal-card">
                    <div class="border-b dark:border-gray-700 pb-3 mb-6">
                        <h3 class="modalTitle">
                            Registrar Nuevo Miembro del Staff
                        </h3>
                    </div>

                    <form wire:submit.prevent="createUser" class="space-y-4">

                        <div>
                            <flux:input wire:model="newName" :label="__('Nombre')" type="text" required autofocus
                                autocomplete="newName" placeholder="Nombre" class:input="!w-full inputs" />
                            @error('newName')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <flux:input wire:model="newLastname" :label="__('Apellido')" type="text" required
                                autofocus autocomplete="newLastname" placeholder="Apellido"
                                class:input="!w-full inputs" />
                            @error('newName')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <flux:input wire:model="newEmail" :label="__('Correo')" type="email" required autofocus
                                autocomplete="email" placeholder="email@example.com" class:input="!w-full inputs" />
                            @error('newEmail')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <flux:input wire:model="newPassword" :label="__('Contraseña')" type="password" required
                                :placeholder="__('Contraseña (Mín. 8 caracteres)')" viewable
                                class:input="!w-full inputs" />
                            @error('newPassword')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <flux:select :label="__('Rol')" wire:model="newRole" class="!w-full inputs">
                                @foreach ($staffRoles as $roleKey => $roleLabel)
                                    @if ($roleKey !== 'cliente' && $roleKey !== 'administrador')
                                        <flux:select.option value="{{ $roleKey }}">{{ $roleLabel }}
                                        </flux:select.option>
                                    @endif
                                @endforeach
                            </flux:select>
                            @error('newRole')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mt-6 pt-4 border-t dark:border-gray-700 flex justify-end space-x-3">
                            <button type="button" wire:click="$set('showCreateModal', false)" @click="open = false"
                                class="btn-outline-red">
                                Cancelar
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="btn-outline-lime">
                                <span wire:loading.remove wire:target="createUser">Guardar</span>
                                <span wire:loading wire:target="createUser">Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- MODAL DE CONFIRMACIÓN --}}
            {{-- ======================================================= --}}
            <div x-data="{
                show: @entangle('showConfirmationModal'),
                success: false,
                confirmAction() {
                    $wire.call('executeModalAction').then(() => {
                        this.success = true;
                        setTimeout(() => {
                            this.show = false;
                            this.success = false;
                            $wire.call('closeModal');
                        }, 1800);
                    });
                }
            }" x-show="show" x-cloak class="bg-modal" x-transition.opacity>
                <div class="max-w-lg w-full card-tb-ve-v2 transform transition-all relative overflow-hidden"
                    x-transition.scale>

                    <template x-if="!success">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $modalTitle }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-5">{!! nl2br($modalMessage) !!}</p>

                            <div class="flex justify-end space-x-3 mt-5">
                                <button wire:click="closeModal" class="btn-outline-red">
                                    Cancelar
                                </button>
                                <button @click="confirmAction()" class="btn-outline-lime">
                                    Confirmar
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="success">
                        <div class="flex flex-col items-center justify-center text-center py-8 animate-fadeIn">
                            <svg class="w-16 h-16 text-lime mb-3 animate-check" fill="none" stroke="currentColor"
                                stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">Acción realizada con éxito
                            </p>
                        </div>
                    </template>

                </div>
            </div>
        </div>
    </div>
</div>
