<div>
    <div class="p-6 bg-white shadow-xl sm:rounded-lg max-w-lg mx-auto mt-10">
        <div x-data="{ show: false }" x-init="@this.on('trainer-registered', () => { show = true; setTimeout(() => show = false, 3000); })">
            
            <!-- Notificación de Éxito Flotante -->
            <div x-show="show" 
                x-transition:enter="transition ease-out duration-300" 
                x-transition:enter-start="opacity-0 scale-90" 
                x-transition:enter-end="opacity-100 scale-100" 
                x-transition:leave="transition ease-in duration-300" 
                x-transition:leave-start="opacity-100 scale-100" 
                x-transition:leave-end="opacity-0 scale-90"
                class="fixed top-4 right-4 bg-green-600 text-white font-semibold px-6 py-3 rounded-xl shadow-2xl z-50">
                ✅ Entrenador registrado como 'empleado'.
            </div>

            <!-- Título -->
            <h2 class="text-3xl font-extrabold text-indigo-700 mb-6 border-b-2 border-indigo-100 pb-3 text-center">
                Sistema de Alta de Entrenadores
            </h2>
            <p class="text-gray-500 mb-8 text-center">
                Complete los campos para registrar un nuevo miembro del staff con rol 'empleado'.
            </p>

            <!-- Formulario -->
            <form wire:submit="submit" class="space-y-6">
                
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nombre Completo</label>
                    <input wire:model="name" type="text" id="name" 
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150" 
                        required autofocus>
                    @error('name') <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico</label>
                    <input wire:model="email" type="email" id="email" 
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150" 
                        required>
                    @error('email') <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Contraseña</label>
                    <input wire:model="password" type="password" id="password" 
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150" 
                        required>
                    @error('password') <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirmar Contraseña</label>
                    <input wire:model="password_confirmation" type="password" id="password_confirmation" 
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150" 
                        required>
                </div>

                <!-- Botón de Registro -->
                <div class="pt-6">
                    <button type="submit" 
                            class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-lg text-base font-bold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out transform hover:scale-[1.01]"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Guardar Nuevo Empleado</span>
                        <span wire:loading>Guardando...</span>
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</div>