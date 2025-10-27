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

        $this->redirectIntended(route('profile.setup', absolute: false), navigate: true);
    }
}; ?>

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
            alt="Admin Logo" 
            class="block h-12 w-auto dark:hidden transition-opacity duration-300"
        >
        <img 
            src="{{ asset('img/logo-verde.png') }}" 
            alt="Admin Logo Dark" 
            class="hidden h-12 w-auto dark:block transition-opacity duration-300"
        >
    </div>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-[#7bcb01] mt-4">
            ¡Comienza tu Entrenamiento!
        </h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Crea tu cuenta para acceder a la plataforma.
        </p>
    </div>

    <form method="POST" wire:submit="register" class="space-y-4">

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
            </div>

            <div>
                <flux:input
                    wire:model="lastName"
                    :label="__('Apellido')"
                    type="text"
                    required
                    autofocus
                    autocomplete="family-name"
                    placeholder="Apellido"
                    class:input="!w-full border !border-gray-600 dark:!border-gray-100 focus:!border-[#7bcb01] focus:!ring-2 focus:!outline focus:!ring-[#7bcb01] shadow-sm"
                />
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
            </div>
        </div>

        <button type="submit"
            class="mt-6 w-full relative block py-3 px-4 border border-transparent rounded-lg shadow-md text-sm font-bold text-white bg-lime-dark hover:bg-lime-darker focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime transition duration-150 ease-in-out"
            wire:loading.attr="disabled">

            <span wire:loading.remove>
                REGISTRAR
            </span>
            
            <span wire:loading>
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
        </button>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" wire:navigate class="text-sm font-medium text-lime hover:text-lime-darker dark:text-lime">
                ¿Ya tienes cuenta? Inicia sesión aquí.
            </a>
        </div>
    </form>
</div>