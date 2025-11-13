<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="md:p-8">
            <h2 class="titles-border mb-4">
                Panel de Administración
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="card-tb-ve-v2">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <svg class="w-8 h-8 text-[#7bcb01]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c4.418 0 8 2.015 8 5v2H4v-2c0-2.985 3.582-5 8-5z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Clientes</h3>
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">
                            <p class="text-sm">Vista general de la base de clientes.</p>
                        </div>
                    </div>
                    <div class="mt-6 space-y-3">
                        <div class="flex justify-between items-center bg-lime-200/95 dark:bg-lime-300/50 p-3 rounded-md">
                            <span class="text-sm font-medium">Activos:</span>
                            <span class="text-xl font-bold">{{ $clientActiveCount }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-red-200/95 dark:bg-red-400/50 p-3 rounded-md">
                            <span class="text-sm font-medium">Inactivos:</span>
                            <span class="text-xl font-bold">{{ $clientInactiveCount }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.clients') }}" class="mt-4 text-[#7bcb01] hover:text-[#5aa301] text-sm font-semibold flex items-center">
                        Ir a Gestión de Clientes &rarr;
                    </a>
                </div>

                <div class="card-tb-an-v2">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c4.418 0 8 2.015 8 5v2H4v-2c0-2.985 3.582-5 8-5z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Staff</h3>
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">
                            <p class="text-sm">Resumen de personal activo sobre el total.</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-3">
                        <div class="flex justify-between items-center bg-orange-200 dark:bg-orange-300/50 p-3 rounded-md">
                            <span class="text-sm font-medium">Entrenadores (Activos/Total):</span>
                            <span class="text-xl font-bold">
                                {{ $trainerActiveCount }} / {{ $trainerTotalCount }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center bg-orange-200 dark:bg-orange-300/50 p-3 rounded-md">
                            <span class="text-sm font-medium">Nutriólogos (Activos/Total):</span>
                            <span class="text-xl font-bold">
                                {{ $nutriologoActiveCount }} / {{ $nutriologoTotalCount }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('admin.employees') }}" class="mt-4 text-orange-400 hover:text-orange-700 text-sm font-semibold flex items-center">
                        Ir a Gestión de Staff &rarr;
                    </a>
                </div>

                <div class="card-tb-az-v2">
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <svg class="w-8 h-8 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Publicaciones</h3>
                        </div>
                        <div class="text-gray-600 dark:text-gray-400">
                            <p class="text-sm">Anuncios y noticias activas en el sistema.</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-between items-center bg-cyan-200 dark:bg-cyan-900/50 p-3 rounded-md">
                        <span class="text-sm font-medium">Total de Posts:</span>
                        <span class="text-3xl font-bold">{{ $postCount }}</span>
                    </div>
                    <a href="{{ route('admin.posts') }}" class="mt-4 text-cyan-500 hover:text-cyan-700 text-sm font-semibold flex items-center">
                        Ir a Gestión de Publicaciones &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>