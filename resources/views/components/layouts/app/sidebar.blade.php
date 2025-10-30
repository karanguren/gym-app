<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
@include('partials.head')
<script>
            (function () {
                const storedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                // Determinar el tema: Preferencia guardada > Configuración del sistema
                const themeToApply = storedTheme || (prefersDark ? 'dark' : 'light');
                
                if (themeToApply === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>
</head>
    <body class="min-h-screen bg-white antialiased dark:bg-[#1a1a1a]/95 ">
        <flux:sidebar sticky stashable class="border-e border-[#7bcb01] bg-white dark:border-[#7bcb01] dark:bg-[#1a1a1a]/95">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

                @php
                    $user = auth()->user();
                    $dashboardRoute = route('dashboard');
                    $logoutRoute = route('logout');

                    if ($user->role === 'administrador') {
                        $dashboardRoute = route('admin.dashboard');
                        $logoutRoute = route('admin.logout'); 
                    } elseif ($user->role === 'empleado') {
                        $dashboardRoute = route('employee.dashboard');
                    }

                    $clientIsVerified = $user->role === 'cliente' && ($user->profile?->is_verified ?? false);

                @endphp

                <div class="flex items-center justify-between me-5">
                    {{-- LOGO --}}
                    <a href="{{ $dashboardRoute }}" class="flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                        <img src="{{ asset('img/logo-light.png') }}" alt="Logo Claro" class="block h-6 w-auto dark:hidden transition-opacity duration-300" >
                        <img src="{{ asset('img/logo-dark.png') }}" alt="Logo Oscuro" class="hidden h-6 w-auto dark:block transition-opacity duration-300" >
                    </a>
                    
                    <div class="me-0"> 
                        @livewire('theme-switcher')
                    </div>
                </div>
                
                {{-- NAVEGACIÓN DINÁMICA BASADA EN ROLES --}}
                <flux:navlist variant="outline">

                    {{-- Grupo 1: Opciones BASE y Dashboard según Rol --}}
                    <flux:navlist.group 
                        :heading="__('Plataforma')" 
                        class="grid text-gray-900 dark:text-[#7bcb01]">
                        
                        @if ($user->role === 'administrador')
                            <flux:navlist.item 
                                icon="home" 
                                :href="route('admin.dashboard')" 
                                :current="request()->routeIs('admin.clients') || request()->routeIs('admin.employees') || request()->routeIs('admin.posts')" 
                                class="text-gray-900 dark:text-[#7bcb01] hover:bg-gray-100 dark:hover:bg-gray-800"
                                icon-class="text-[#7bcb01]"
                                wire:navigate>
                                {{ __('Panel Admin') }}
                            </flux:navlist.item>
                        @elseif ($user->role === 'trainer' || $user->role === 'nutriologo')
                            <flux:navlist.item 
                                icon="home" 
                                :href="route('employee.dashboard')" 
                                :current="request()->routeIs('employee.dashboard')" 
                                class="text-gray-900 dark:text-[#7bcb01] hover:bg-gray-100 dark:hover:bg-gray-800"
                                icon-class="text-[#7bcb01]"
                                wire:navigate>
                                {{ __('Panel Empleado') }}
                            </flux:navlist.item>
                        @else 
                            <flux:navlist.item 
                                icon="home" 
                                :href="route('dashboard')" 
                                :current="request()->routeIs('dashboard')" 
                                class="text-gray-900 dark:text-[#7bcb01] hover:bg-gray-100 dark:hover:bg-gray-800"
                                icon-class="text-[#7bcb01]"
                                wire:navigate>
                                {{ __('Dashboard Cliente') }}
                            </flux:navlist.item>
                        @endif
                        
                    </flux:navlist.group>

                    {{-- Grupo 2: Opciones para CLIENTES --}}
                    @if ($user->role === 'cliente' && $clientIsVerified)
                        <flux:navlist.group :heading="__('Cliente')" class="grid text-gray-900 dark:text-[#7bcb01]">
                                {{-- CLIENTE VERIFICADO: Muestra todas las opciones --}}
                            <flux:navlist.item icon="users" href="{{ route('client.routine-builder') }}" class="text-gray-900 dark:text-[#7bcb01]" icon-class="text-[#7bcb01]" wire:navigate>{{ __('Armar mi Rutina') }}</flux:navlist.item>
                            <flux:navlist.item icon="users" href="{{ route('client.progress') }}" class="text-gray-900 dark:text-[#7bcb01]" icon-class="text-[#7bcb01]" wire:navigate>{{ __('Mi Progreso') }}</flux:navlist.item>
                            @if ($user->client_type === 'personalized')
                            <flux:navlist.item 
                                icon="users"
                                href="{{ route('client.trainer-selection') }}" 
                                class="text-gray-900 dark:text-[#7bcb01]" 
                                icon-class="text-[#7bcb01]" 
                                wire:navigate>
                                {{ __('Entrenador Personal') }}
                            </flux:navlist.item>
                            @endif
                            <flux:navlist.item icon="users" href="{{ route('dashboard') }}" class="text-gray-900 dark:text-[#7bcb01]" icon-class="text-[#7bcb01]" wire:navigate>{{ __('Reservas') }}</flux:navlist.item>
                            <flux:navlist.item icon="users" href="{{ route('dashboard') }}" class="text-gray-900 dark:text-[#7bcb01]" icon-class="text-[#7bcb01]" wire:navigate>{{ __('Foro') }}</flux:navlist.item>
                        
                            
                        </flux:navlist.group>
                    @endif
                    
                    {{-- Grupo 3: Opciones para ADMINISTRADOR --}}
                    @if ($user->role === 'administrador')
                        <flux:navlist.group :heading="__('Administración')" class="grid text-gray-900 dark:text-[#7bcb01]">
                            
                            <flux:navlist.item 
                                icon="users" 
                                href="{{ route('admin.clients') }}" 
                                :current="request()->routeIs('admin.clients')" 
                                class="text-gray-900 dark:text-[#7bcb01]"
                                icon-class="text-[#7bcb01]"
                                wire:navigate>
                                {{ __('Clientes') }}
                            </flux:navlist.item>
                            
                            <flux:navlist.item 
                                icon="user-group"
                                href="{{ route('admin.employees') }}" 
                                :current="request()->routeIs('admin.employees')" 
                                class="text-gray-900 dark:text-[#7bcb01]"
                                icon-class="text-[#7bcb01]"
                                wire:navigate>
                                {{ __('Entrenadores/Nutriólogos') }}
                            </flux:navlist.item>

                            <flux:navlist.item 
                                icon="newspaper" 
                                href="{{ route('admin.posts') }}" 
                                :current="request()->routeIs('admin.posts')" 
                                class="text-gray-900 dark:text-[#7bcb01]"
                                icon-class="text-[#7bcb01]"
                                wire:navigate>
                                {{ __('Publicaciones') }}
                            </flux:navlist.item>
                            
                        </flux:navlist.group>
                    @endif
                    
                    {{-- Grupo 4: Opciones para EMPLEADOS --}}
                    @if ($user->role === 'trainer')
                    <flux:navlist.group :heading="__('Staff')" class="grid text-gray-900 dark:text-[#7bcb01]">
                        <flux:navlist.item icon="users" href="{{ route('employee.dashboard') }}" class="text-gray-900 dark:text-[#7bcb01]" icon-class="text-[#7bcb01]" wire:navigate>{{ __('Mis Clientes y Rutinas') }}</flux:navlist.item>
                        <flux:navlist.item icon="users" href="{{ route('employee.dashboard') }}" class="text-gray-900 dark:text-[#7bcb01]" icon-class="text-[#7bcb01]" wire:navigate>{{ __('Chat con Clientes') }}</flux:navlist.item>
                        <flux:navlist.item icon="users" href="{{ route('employee.dashboard') }}" class="text-gray-900 dark:text-[#7bcb01]" icon-class="text-[#7bcb01]" wire:navigate>{{ __('Mi Horario') }}</flux:navlist.item>
                    </flux:navlist.group>
                    @endif

                </flux:navlist>

                <flux:spacer />

                {{-- Menú de Usuario de Escritorio (Desktop) --}}
                <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                    <flux:profile
                        :name="$user->name"
                        :initials="$user->initials()"
                        icon:trailing="chevrons-up-down"
                        data-test="sidebar-menu-button"
                    />

                    <flux:menu class="w-[220px]">
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span
                                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-[#7bcb01] dark:text-gray-900"
                                        >
                                            {{ $user->initials() }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>
                                        <span class="truncate text-xs text-gray-600 dark:text-gray-300">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />
                        
                        {{-- FORMULARIO DE LOGOUT DE ESCRITORIO --}}
                        <form method="POST" action="{{ $logoutRoute }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                                {{ __('Cerrar Sesión') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:sidebar>

            {{-- Mobile User Menu --}}
            <flux:header class="lg:hidden">
                <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

                <flux:spacer />

                <flux:dropdown position="top" align="end">
                    <flux:profile
                        :initials="$user->initials()"
                        icon-trailing="chevron-down"
                    />

                    <flux:menu>
                        <flux:menu.radio.group>
                            <div class="p-0 text-sm font-normal">
                                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <span
                                            class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-[#7bcb01] dark:text-gray-900"
                                        >
                                            {{ $user->initials() }}
                                        </span>
                                    </span>

                                    <div class="grid flex-1 text-start text-sm leading-tight">
                                        <span class="truncate font-semibold text-gray-900 dark:text-white">{{ $user->name }}</span>
                                        <span class="truncate text-xs text-gray-600 dark:text-gray-300">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </div>
                        </flux:menu.radio.group>

                        <flux:menu.separator />

                        {{-- FORMULARIO DE LOGOUT MÓVIL --}}
                        <form method="POST" action="{{ $logoutRoute }}" class="w-full">
                            @csrf
                            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                                {{ __('Cerrar Sesión') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu>
                </flux:dropdown>
            </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
