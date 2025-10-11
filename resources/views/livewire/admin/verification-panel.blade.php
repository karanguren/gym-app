<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6 border-b pb-2">
        Panel de Verificación de Clientes Pendientes 🛡️
    </h2>

    @if (session()->has('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md">{{ session('error') }}</div>
    @endif

    @if ($pendingClients->isEmpty())
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-xl text-center">
            <p class="text-xl text-indigo-600 dark:text-indigo-400 font-semibold">
                🎉 No hay clientes pendientes de verificación. ¡Buen trabajo!
            </p>
        </div>
    @else
        <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cédula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Datos Físicos</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($pendingClients as $profile)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition duration-150" wire:key="{{ $profile->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $profile->user->name }} {{ $profile->last_name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $profile->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                Cédula: {{ $profile->id_number }} <br>
                                Dirección: {{ $profile->address }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                Tlf: {{ $profile->personal_number }} <br>
                                Emergencia: {{ $profile->emergency_contact }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                Estatura: {{ $profile->height }} m <br>
                                Peso: {{ $profile->weight }} kg
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button wire:click="verifyClient({{ $profile->id }})"
                                        wire:confirm="¿Está seguro de verificar a {{ $profile->user->name }}?"
                                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg mr-2 transition duration-150">
                                    Aprobar ✅
                                </button>
                                <button wire:click="rejectClient({{ $profile->id }})"
                                        wire:confirm="ATENCIÓN: Esto eliminará el perfil y la cuenta de usuario de {{ $profile->user->name }}. ¿Desea continuar?"
                                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition duration-150">
                                    Rechazar ❌
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</div>
