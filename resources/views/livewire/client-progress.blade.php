<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="md:p-8">

            <header class="mb-10 border-b dark:border-gray-700 pb-4">
                <h1 class="text-4xl font-extrabold text-gray-900 dark:text-[#7bcb01] flex items-center">
                    <svg class="w-8 h-8 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Mi Progreso
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Sigue tu evolución de fuerza y el historial de tus entrenamientos.
                </p>
            </header>

            {{-- Mensaje si no hay datos --}}
            @if ($totalWorkouts === 0)
                <div class="text-center p-12 bg-gray-50 dark:bg-[#1a1a1a] rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <svg class="mx-auto h-12 w-12 svg-ve" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">Aún no hay progreso</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        ¡Comienza a registrar tu primer entrenamiento para ver tus estadísticas aquí!
                    </p>
                </div>
            @else
                
                {{-- 1. TARJETAS DE ESTADÍSTICAS GENERALES --}}
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Estadísticas Clave</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                    
                    {{-- Total Entrenamientos --}}
                    <div class="bg-lime-50 dark:bg-gray-800 p-6 rounded-xl shadow-md border-l-4 border-[#7bcb01] dark:border-[#5aa301]">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Entrenamientos Completados</p>
                        <p class="mt-1 text-3xl font-extrabold text-gray-900 dark:text-[#7bcb01]">{{ $totalWorkouts }}</p>
                    </div>

                    {{-- Duración Promedio --}}
                    <div class="bg-blue-50 dark:bg-gray-800 p-6 rounded-xl shadow-md border-l-4 border-blue-500 dark:border-blue-600">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Duración Promedio</p>
                        <p class="mt-1 text-3xl font-extrabold text-gray-900 dark:text-blue-500">
                            {{ $this->formatSeconds($avgDuration) }}
                        </p>
                    </div>

                    {{-- Máxima Duración --}}
                    <div class="bg-red-50 dark:bg-gray-800 p-6 rounded-xl shadow-md border-l-4 border-red-500 dark:border-red-600">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Entrenamiento Más Largo</p>
                        <p class="mt-1 text-3xl font-extrabold text-gray-900 dark:text-red-500">
                            {{ $this->formatSeconds($maxDuration) }}
                        </p>
                    </div>

                </div>

                {{-- 2. TABLA DE PROGRESO DE FUERZA (PESO MÁXIMO) --}}
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                    Progreso de Fuerza (Máximo Kg) 
                    <svg class="w-5 h-5 ml-2 text-[#7bcb01]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 17.58A5 5 0 0 0 18 10c-1.2 0-2.43.3-3.4.88L12 2v10l3.4-1.88a5 5 0 0 0 3.4.88 5 5 0 0 0 0-10"/></svg>
                </h2>

                <div class="shadow overflow-hidden border border-gray-200 dark:border-gray-700 sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Ejercicio
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Máximo Peso Registrado (Kg)
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($sortedProgress as $progress)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $progress['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-[#7bcb01]">
                                            {{ number_format($progress['max_weight'], 1) }} Kg
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            No se encontraron pesos registrados con sets completados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                {{-- Nota sobre el cálculo --}}
                <p class="mt-4 text-xs text-gray-500 dark:text-gray-400 italic">
                    *El progreso de fuerza se calcula usando el peso máximo levantado para un set completado en cualquier registro de entrenamiento.
                </p>

            @endif

        </div>
    </div>
</div>
