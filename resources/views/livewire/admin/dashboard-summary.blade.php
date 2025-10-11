<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 ">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-[#7bcb01] mb-8">
            Panel de Administración
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="bg-white/95 dark:bg-[#1a1a1a]/95 overflow-hidden shadow-xl sm:rounded-lg p-6 flex flex-col justify-between transition duration-300 hover:shadow-2xl hover:scale-[1.01] border-t-4 border-[#7bcb01]">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <!-- Icono de Grupo de Usuarios -->
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
                    <div class="flex justify-between items-center bg-green-50 dark:bg-green-900/50 p-3 rounded-md">
                        <span class="text-sm font-medium text-green-700 dark:text-green-300">Activos:</span>
                        <span class="text-xl font-bold text-green-800 dark:text-green-200">{{ $clientActiveCount }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-red-50 dark:bg-red-900/50 p-3 rounded-md">
                        <span class="text-sm font-medium text-red-700 dark:text-red-300">Inactivos:</span>
                        <span class="text-xl font-bold text-red-800 dark:text-red-200">{{ $clientInactiveCount }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.clients') }}" class="mt-4 text-[#7bcb01] hover:text-[#5aa301] text-sm font-semibold flex items-center">
                    Ir a Gestión de Clientes &rarr;
                </a>
            </div>

            <!-- CUADRO 2: Empleados/Staff - ICONO DE USUARIO INDIVIDUAL -->
            <div class="bg-white/95 dark:bg-[#1a1a1a]/95 overflow-hidden shadow-xl sm:rounded-lg p-6 flex flex-col justify-between transition duration-300 hover:shadow-2xl hover:scale-[1.01] border-t-4 border-orange-500">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c4.418 0 8 2.015 8 5v2H4v-2c0-2.985 3.582-5 8-5z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Entrenadores & Staff</h3>
                    </div>
                    <div class="text-gray-600 dark:text-gray-400">
                        <p class="text-sm">Total de personal activo, incluyendo administradores.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-between items-center bg-orange-50 dark:bg-orange-900/50 p-3 rounded-md">
                    <span class="text-sm font-medium text-orange-700 dark:text-orange-300">Total de Miembros:</span>
                    <span class="text-3xl font-bold text-orange-800 dark:text-orange-200">{{ $employeeCount }}</span>
                </div>
                <a href="{{ route('admin.employees') }}" class="mt-4 text-orange-500 hover:text-orange-700 text-sm font-semibold flex items-center">
                    Ir a Gestión de Staff &rarr;
                </a>
            </div>

            <!-- CUADRO 3: Publicaciones/Anuncios -->
            <div class="bg-white/95 dark:bg-[#1a1a1a]/95 overflow-hidden shadow-xl sm:rounded-lg p-6 flex flex-col justify-between transition duration-300 hover:shadow-2xl hover:scale-[1.01] border-t-4 border-cyan-500">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <!-- Icono de Publicaciones (sin cambios) -->
                        <svg class="w-8 h-8 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Publicaciones</h3>
                    </div>
                    <div class="text-gray-600 dark:text-gray-400">
                        <p class="text-sm">Anuncios y noticias activas en el sistema.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-between items-center bg-cyan-50 dark:bg-cyan-900/50 p-3 rounded-md">
                    <span class="text-sm font-medium text-cyan-700 dark:text-cyan-300">Total de Posts:</span>
                    <span class="text-3xl font-bold text-cyan-800 dark:text-cyan-200">{{ $postCount }}</span>
                </div>
                <a href="{{ route('admin.posts') }}" class="mt-4 text-cyan-500 hover:text-cyan-700 text-sm font-semibold flex items-center">
                    Ir a Gestión de Publicaciones &rarr;
                </a>
            </div>
        </div>
    </div>

</div>