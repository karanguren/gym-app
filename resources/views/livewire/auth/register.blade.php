<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $lastName = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user_data = [
            'name' => $validated['name'],
            'last_name' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        event(new Registered(($user = User::create($user_data))));

        Auth::login($user);

        Session::regenerate();

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full max-w-lg bg-white dark:bg-[#1a1a1a]/95 rounded-xl shadow-2xl p-6 md:p-10 space-y-6 border border-gray-200 dark:border-[#7bcb01] mx-auto">
    
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
            alt="Admin Logo" 
            class="block h-12 w-auto dark:hidden transition-opacity duration-300"
        >
        <img 
            src="{{ asset('img/logo-verde.png') }}" 
            alt="Admin Logo Dark" 
            class="hidden h-12 w-auto dark:block transition-opacity duration-300"
        >
    </div>
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-4">
            ¡Comienza tu Entrenamiento!
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Crea tu cuenta para acceder a la plataforma.
        </p>
    </div>

    <form method="POST" wire:submit="register" class="space-y-4">

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
                <label for="lastName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Apellido
                </label>
                <input wire:model="lastName" id="lastName" type="text" required autocomplete="family-name"
                    placeholder="Apellido"
                    class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-lime focus:border-lime dark:bg-[#1a1a1a]/95 dark:border-gray-600 dark:text-white transition duration-150">
                @error('lastName') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Correo Electrónico
            </label>
            <input wire:model="email" id="email" type="email" required autocomplete="email"
                placeholder="email@ejemplo.com"
                class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-lime focus:border-lime dark:bg-[#1a1a1a]/95 dark:border-gray-600 dark:text-white transition duration-150">
            @error('email') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

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

        <button type="submit"
            class="w-full relative flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-gray-900 bg-lime-dark hover:bg-lime-darker focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime transition duration-150 ease-in-out"
            wire:loading.attr="disabled">
            
            {{-- Opción 1: Muestra "Registrar" cuando NO está cargando. Ocupa el espacio completo --}}
            <span wire:loading.remove>
                Registrar
            </span>
            
            {{-- Opción 2: Muestra el Spinner y "Registrando..." cuando SÍ está cargando. Ocupa el espacio completo y se centra --}}
            <span wire:loading class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Registrando...
            </span>
        </button>

        <div class="text-center mt-4">
            {{-- Enlace de inicio de sesión actualizado con text-lime --}}
            <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-lime hover:text-lime-darker dark:text-lime">
                ¿Ya tienes cuenta? Inicia sesión aquí.
            </a>
        </div>
    </form>
</div>