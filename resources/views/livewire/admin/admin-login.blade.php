<div class="relative min-h-screen w-full flex flex-col items-center justify-center p-4 bg-[url('../../../public/img/fondo2.jpg')] bg-center bg-cover" >
    <style>
        .text-lime { color: #7bcb01; }
        .focus\:ring-lime { --tw-ring-color: #7bcb01; }
        .focus\:border-lime { border-color: #7bcb01; }
        .bg-lime-dark { background-color: #7bcb01; }
        .hover\:bg-lime-darker:hover { background-color: #69b301; } 
    </style>
    <div class="absolute top-4 right-4 z-10">
        @livewire('theme-switcher')
    </div>

    <div class="w-full sm:max-w-md px-8 py-10 bg-white/70 dark:bg-[#1a1a1a]/70 shadow-2xl overflow-hidden rounded-xl backdrop-blur-sm transition-colors duration-300">
        
        <div class="flex justify-center mb-6">
            <img 
                src="{{ asset('img/logo-negro.png') }}" 
                alt="Admin Logo" 
                class="block h-12 w-auto dark:hidden transition-opacity duration-300"
            >
            <img 
                src="{{ asset('img/logo-verde.png') }}" 
                alt="Admin Logo Dark" 
                class="hidden h-12 w-auto dark:block transition-opacity duration-300"
            >
        </div>

        <h2 class="text-2xl font-bold text-center text-black dark:text-[#7bcb01] mb-8 tracking-wide">
            ACCESO ADMINISTRATIVO
        </h2>
        
        <form wire:submit="login" class="space-y-6">
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <input 
                    wire:model="email" 
                    id="email" 
                    type="email" 
                    required 
                    autofocus 
                    placeholder="ejemplo@mail.com"
                    class="block w-full px-4 py-2 rounded-lg shadow-sm focus:ring-lime focus:border-gray-300 focus:outline-none focus:ring-2 dark:bg-[#1a1a1a]/95 border border-gray-300 dark:border-gray-100 dark:text-white transition duration-150"
                >
                @error('email') <span class="mt-2 text-sm text-red-600 dark:text-red-500 font-medium">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña</label>
                <input 
                    wire:model="password" 
                    id="password" 
                    type="password" 
                    required 
                    placeholder="Ingresa tu contraseña"
                    class="block w-full px-4 py-2 rounded-lg shadow-sm focus:ring-lime focus:border-gray-300 focus:outline-none focus:ring-2 dark:bg-[#1a1a1a]/95 border border-gray-300 dark:border-gray-100 dark:text-white transition duration-150"
                >
                @error('password') <span class="mt-2 text-sm text-red-600 dark:text-red-500 font-medium">{{ $message }}</span> @enderror
            </div>

            <button 
                type="submit" 
                class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#7bcb01] border border-transparent rounded-lg font-bold text-base text-white uppercase tracking-wider hover:bg-[#5aa301] active:bg-[#4a8a01] focus:outline-none focus:ring-2 focus:ring-[#7bcb01] focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800 transition ease-in-out duration-300 shadow-md hover:shadow-lg"
            >
                Acceder
            </button>

        </form>
    </div>
</div>