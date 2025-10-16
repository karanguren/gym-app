<div>
<div class="w-full max-w-lg bg-white dark:bg-[#1a1a1a]/95 rounded-xl shadow-2xl p-6 md:p-10 space-y-6 border border-gray-200 dark:border-[#7bcb01] mx-auto">
    
    <!-- Definición de estilos de color de marca -->
    <style>
        .text-lime { color: #7bcb01; }
        .focus\:ring-lime { --tw-ring-color: #7bcb01; }
        .focus\:border-lime { border-color: #7bcb01; }
        .bg-lime-dark { background-color: #7bcb01; }
        .hover\:bg-lime-darker:hover { background-color: #69b301; } 
    </style>

    <!-- Sección del Logo -->
    <div class="flex justify-center mb-6">
        <img 
            src="{{ asset('img/logo-negro.png') }}" 
            alt="Trainer Register Logo" 
            class="block h-12 w-auto dark:hidden transition-opacity duration-300"
        >
        <img 
            src="{{ asset('img/logo-verde.png') }}" 
            alt="Trainer Register Logo Dark" 
            class="hidden h-12 w-auto dark:block transition-opacity duration-300"
        >
    </div>
    
    <!-- Título y Descripción -->
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-4">
            Registro de Staff
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Crea tu perfil de entrenador o nutriólogo de la plataforma.
        </p>
    </div>

    <form wire:submit="register" class="space-y-4">

        <!-- Nombre y Apellido en dos columnas -->
        <div class="grid grid-cols-2 gap-4">
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Nombre
                </label>
                <input wire:model="name" id="name" type="text" required autofocus autocomplete="given-name"
                    placeholder="Nombre"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-lime focus:border-lime dark:bg-[#1a1a1a]/95 dark:border-gray-600 dark:text-white transition duration-150">
                @error('name') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Apellido
                </label>
                <!-- NOTA: El componente PHP debe tener la propiedad $lastName -->
                <input wire:model="last_name" id="last_name" type="text" required autocomplete="family-name"
                    placeholder="Apellido"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-lime focus:border-lime dark:bg-[#1a1a1a]/95 dark:border-gray-600 dark:text-white transition duration-150">
                @error('last_name') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>
        
        <!-- Correo Electrónico -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Correo Electrónico
            </label>
            <input wire:model="email" id="email" type="email" required autocomplete="email"
                placeholder="email@ejemplo.com"
                class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-lime focus:border-lime dark:bg-[#1a1a1a]/95 dark:border-gray-600 dark:text-white transition duration-150">
            @error('email') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Tipo de Especialista
            </label>
            <select 
                id="role" 
                wire:model="role" 
                class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-lime focus:border-lime dark:bg-[#1a1a1a]/95 dark:border-gray-600 dark:text-white transition duration-150" 
                required
            >
                <option value="" disabled selected>Selecciona una opción</option>
                <option value="trainer">Entrenador</option>
                <option value="nutriologo">Nutriólogo</option>
            </select>
            @error('role')
                <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Contraseñas -->
        <div class="space-y-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Contraseña
                </label>
                <input wire:model="password" id="password" type="password" required autocomplete="new-password"
                    placeholder="Contraseña segura"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-lime focus:border-lime dark:bg-[#1a1a1a]/95 dark:border-gray-600 dark:text-white transition duration-150">
                @error('password') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Confirmar Contraseña
                </label>
                <input wire:model="password_confirmation" id="password_confirmation" type="password" required autocomplete="new-password"
                    placeholder="Repite la contraseña"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-lime focus:border-lime dark:bg-[#1a1a1a]/95 dark:border-gray-600 dark:text-white transition duration-150">
                @error('password_confirmation') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Botón de Registro con Loader -->
        <button 
            type="submit"
            wire:loading.attr="disabled"
            wire:target="register"
            class="w-full relative py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-gray-900 bg-lime-dark hover:bg-lime-darker focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime transition duration-150 ease-in-out disabled:opacity-70 disabled:cursor-not-allowed">

            <!-- Texto normal -->
            <span wire:loading.remove wire:target="register" class="flex items-center justify-center">
                Registrar
            </span>

            <!-- Loader -->
            <span wire:loading wire:target="register" class="flex items-center justify-center space-x-2">
                <svg class="animate-spin h-5 w-5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Registrando...</span>
            </span>
        </button>

        <!-- Enlace de Login -->
        <div class="text-center pt-2">
            <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-lime hover:text-lime-darker dark:text-lime">
                ¿Ya tienes cuenta? Inicia sesión aquí.
            </a>
        </div>
    </form>
</div>
<style>
    /* 🚫 Oculta cualquier wire:loading al cargar la página */
    [wire\:loading] {
        display: none !important;
    }

    /* ✅ Muestra solo cuando está ejecutando el método register() */
    [wire\:loading][wire\:target="register"] {
        display: flex !important;
    }
</style>


</div>
