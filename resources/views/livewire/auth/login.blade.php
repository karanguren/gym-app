<?php

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $user = $this->validateCredentials();

        if (Features::canManageTwoFactorAuthentication() && $user->hasEnabledTwoFactorAuthentication()) {
            Session::put([
                'login.id' => $user->getKey(),
                'login.remember' => $this->remember,
            ]);

            $this->redirect(route('two-factor.login'), navigate: true);

            return;
        }

        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();
        
        // =============================================================
        // LÓGICA DE REDIRECCIÓN CONDICIONAL POR ROL
        // Utiliza los métodos isEmployee() e isAdmin() del modelo User.
        // =============================================================

        if ($user->isEmployee()) {
             $this->redirect(route('employee.dashboard', absolute: false), navigate: true);
            return;
        }

        if ($user->isAdmin()) {
            $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
            return;
        }
        
        if (! $user->profile) {
            $this->redirect(route('profile.setup', absolute: false), navigate: true);
            return;
        }

        $clientProfile = $user->profile;

        if (! $clientProfile->is_verified) {
            $this->redirect(route('verification.pending', absolute: false), navigate: true);
            return;
        }

        $this->redirectIntended(default: route('client.dashboard', absolute: false), navigate: true);
    }

    /**
     * Validate the user's credentials.
     */
    protected function validateCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $this->email, 'password' => $this->password]);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}; ?>

<div class="w-full px-8 py-10 bg-white/70 dark:bg-[#1a1a1a]/70 shadow-2xl overflow-hidden rounded-xl backdrop-blur-sm transition-colors duration-300">
    
    <div class="flex flex-col gap-6">
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
        <x-auth-header :title="__('INICIAR SESIÓN')" :description="__('Ingresa tu email y contraseña para acceder')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" wire:submit="login" class="flex flex-col gap-6">
            <!-- Email Address -->
            <flux:input
                wire:model="email"
                :label="__('Correo')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
                class="mt-2 block w-full inputs"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    wire:model="password"
                    :label="__('Contraseña')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                    class="mt-2 block w-full inputs"
                />
            </div>

            <!-- Remember Me -->
            <flux:checkbox wire:model="remember" :label="__('Remember me')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full !bg-[#7bcb01]/70 !text-white !border-2 !border-[#7bcb01] text-lg rounded-lg !font-bold dark:!border-[#7bcb01] hover:!bg-transparent" data-test="login-button">
                    {{ __('INGRESAR') }}
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                <span>{{ __('¿No tienes cuenta?') }}</span>
                <flux:link :href="route('register')" wire:navigate>{{ __('Regístrate aquí') }}</flux:link>
            </div>
        @endif
    </div>
</div>
