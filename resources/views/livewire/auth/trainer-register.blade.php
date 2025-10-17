<div>
    <div class="w-full max-w-lg px-8 py-10 bg-white/70 dark:bg-[#1a1a1a]/70 shadow-2xl overflow-hidden rounded-xl backdrop-blur-sm transition-colors duration-300">
        
        <style>
            .text-lime { color: #7bcb01; }
            .focus\:ring-lime { --tw-ring-color: #7bcb01; }
            .focus\:border-lime { border-color: #7bcb01; }
            .bg-lime-dark { background-color: #7bcb01; }
            .hover\:bg-lime-darker:hover { background-color: #69b301; } 
        </style>

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
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-4">
                REGISTRO DE STAFF
            </h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Crea tu perfil de entrenador o nutriólogo de la plataforma.
            </p>
        </div>

        <form wire:submit="register" class="space-y-4">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:input
                    wire:model="name"
                    :label="__('Nombre')"
                    type="text"
                    required
                    autofocus
                    autocomplete="given-name"
                    placeholder="Nombre"
                    class:input="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                />
                    @error('name') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <flux:input
                        wire:model="last_name"
                        :label="__('Apellido')"
                        type="text"
                        required
                        autofocus
                        autocomplete="family-name"
                        placeholder="Apellido"
                        class:input="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                    />
                    
                    @error('last_name') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div>
                <flux:input
                    wire:model="email"
                    :label="__('Correo')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                    class:input="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                />
                @error('email') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <flux:select 
                        :label="__('Tipo de Especialista')" wire:model="role"
                        class="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                        >
                        
                                <flux:select.option value="" disabled selected>Selecciona una opción</flux:select.option>
                                <flux:select.option value="trainer">Entrenador</flux:select.option>
                                <flux:select.option value="nutriologo">Nutriólogo</flux:select.option>
                           
                </flux:select>
                
                @error('role')
                    <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-4">
                <div>
                    <flux:input
                        wire:model="password"
                        :label="__('Contraseña')"
                        type="password"
                        required
                        :placeholder="__('Contraseña (Mín. 8 caracteres)')"
                        viewable
                        class:input="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                    />
                </div>

                <div>
                    <flux:input
                    wire:model="password_confirmation"
                    :label="__('Confirmar Contraseña')"
                    type="password"
                    required
                    :placeholder="__('Repite la contraseña')"
                    viewable
                    class:input="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                />
                    @error('password_confirmation') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

             <button type="submit"
                class="mt-6 w-full relative flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-lime-dark hover:bg-lime-darker focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime transition duration-150 ease-in-out"
                wire:loading.attr="disabled">

                <span wire:loading.class="invisible">
                    REGISTRAR
                </span>
                
                <span wire:loading class="absolute inset-0 flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 text-gray-900 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    REGISTRANDO...
                </span>
            </button>
            <!-- <button 
                type="submit"
                wire:loading.attr="disabled"
                wire:target="register"  
                class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#7bcb01] border border-transparent rounded-lg font-bold text-base text-white uppercase tracking-wider hover:bg-[#5aa301] active:bg-[#4a8a01] focus:outline-none focus:ring-2 focus:ring-[#7bcb01] focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 transition ease-in-out duration-300 shadow-md hover:shadow-lg">

                <span wire:loading.remove wire:target="register" class="flex items-center justify-center">
                    Registrar
                </span>

                <span wire:loading wire:target="register" class="flex items-center justify-center space-x-2">
                    <svg class="animate-spin h-5 w-5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Registrando...</span>
                </span>
            </button> -->

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
