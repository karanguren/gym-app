<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard Administrativo
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex space-x-4 border-b border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-sm font-medium 
                    @if(request()->routeIs('admin.dashboard')) 
                        border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 
                    @else 
                        text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                    @endif">
                    Clientes Pendientes
                </a>
                <a href="{{ route('admin.users') }}" class="px-4 py-2 text-sm font-medium 
                    @if(request()->routeIs('admin.users')) 
                        border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400 
                    @else 
                        text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200
                    @endif">
                    Gestión de Usuarios
                </a>
                <a href="#" class="px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Publicaciones 📝
                </a>
            </div>
            
            @if(request()->routeIs('admin.dashboard'))
                @livewire('admin.verification-panel')
            @elseif(request()->routeIs('admin.users'))
                @livewire('admin.user-management')
            @endif
        </div>
    </div>
</x-app-layout>