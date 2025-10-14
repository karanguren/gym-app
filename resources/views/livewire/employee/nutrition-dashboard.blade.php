<?php

use Livewire\Volt\Component;

new class extends Component
{
    // Aquí puedes definir propiedades y métodos de Livewire para la gestión de nutrición
    public $clientesAsignados = 12;
    public $revisionesPendientes = 3;

    public function mount()
    {
        // Lógica de inicialización, si es necesaria.
        // Por ejemplo, cargar datos iniciales de la base de datos.
    }
    
    // Ejemplo de método:
    public function actualizarClientes()
    {
        // Lógica para contar clientes reales...
        $this->clientesAsignados = rand(10, 20);
    }
}; ?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Nutrición (Livewire)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <h1 class="text-3xl font-extrabold text-indigo-700 dark:text-indigo-400 mb-4">
                    Bienvenido, Nutricionista
                </h1>
                
                <p class="text-gray-700 dark:text-gray-300 mb-6">
                    Esta vista es un componente Livewire/Volt. La data es dinámica.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Tarjeta 1: Clientes Asignados -->
                    <div class="p-5 bg-green-100 dark:bg-green-900/50 rounded-lg shadow-md border-b-4 border-green-600 dark:border-green-400">
                        <h3 class="text-xl font-semibold text-green-800 dark:text-green-200 mb-2">
                            Clientes con Plan
                        </h3>
                        <!-- Data dinámica de Livewire -->
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $clientesAsignados }}</p>
                        <button wire:click="actualizarClientes" class="text-sm mt-2 text-green-600 dark:text-green-400 hover:text-green-800 transition">
                            (Actualizar Simulado)
                        </button>
                    </div>

                    <!-- Tarjeta 2: Creación de Menús -->
                    <div class="p-5 bg-blue-100 dark:bg-blue-900/50 rounded-lg shadow-md border-b-4 border-blue-600 dark:border-blue-400">
                        <h3 class="text-xl font-semibold text-blue-800 dark:text-blue-200 mb-2">
                            Gestión de Menús
                        </h3>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">
                            Crear Nuevo Plan
                        </a>
                    </div>

                    <!-- Tarjeta 3: Solicitudes Pendientes -->
                    <div class="p-5 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg shadow-md border-b-4 border-yellow-600 dark:border-yellow-400">
                        <h3 class="text-xl font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                            Revisiones Pendientes
                        </h3>
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $revisionesPendientes }}</p>
                    </div>
                </div>

                <!-- Aquí iría la tabla de clientes o el formulario de gestión -->
                <div class="mt-8 p-6 border dark:border-gray-700 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Lista de Clientes Nutricionales
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Próximo paso: Cargar y filtrar la lista de clientes a los que se les asignará un plan.
                    </p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
