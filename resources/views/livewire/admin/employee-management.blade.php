<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6 border-b pb-2">
            Gestión de Empleados/Staff 🧑‍💼
        </h2>

        @if (session()->has('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md">{{ session('error') }}</div>
        @endif

        <!-- Filtros -->
        <div class="mb-4 flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-4">
            <input wire:model.live="search" type="text" placeholder="Buscar Empleado por Nombre o Email..."
                class="flex-grow rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 shadow-sm">
            
            <select wire:model.live="filterRole" 
                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 shadow-sm">
                <option value="all">Mostrar Todo el Staff</option>
                <option value="empleado">Empleados (Entrenadores/Nutriólogos)</option>
                <option value="administrador">Otros Administradores</option>
            </select>
        </div>

        <!-- Tabla de Empleados -->
        <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Staff</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rol Actual</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cambiar Rol</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition duration-150" wire:key="{{ $user->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($user->role == 'administrador') bg-red-100 text-red-800
                                    @else bg-indigo-100 text-indigo-800 @endif">
                                    {{ $roles[$user->role] ?? ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <select 
                                    wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                    class="rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 shadow-sm text-sm"
                                >
                                    @foreach ($roles as $roleKey => $roleLabel)
                                        <option value="{{ $roleKey }}" @selected($user->role == $roleKey)>
                                            {{ $roleLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="ATENCIÓN: Esto eliminará la cuenta de {{ $user->name }} permanentemente. ¿Desea continuar?"
                                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded-lg text-xs transition duration-150">
                                    Eliminar 🗑️
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No se encontró personal que coincida con los filtros.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="p-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

</div>
