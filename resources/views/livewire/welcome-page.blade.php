<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        
        <style>
            .fixed-social::after {
                content: "";
                display: block;
                width: 1px;
                height: 70px;
                margin: 0 auto;
                background-color: #fff; 
            }

            .fixed-email::after {
                content: "";
                display: block;
                width: 1px;
                height: 70px;
                margin: 0 auto;
                background-color: #fff; 
            }

            .fixed-email a {
                writing-mode: vertical-rl;
                text-orientation: mixed;
                transition: color 0.3s ease-in-out;
                padding: 15px 0; 
                font-size: 0.9em; 
            }

            @media (max-width: 768px) {
                .fixed-social, .fixed-email {
                    display: none;
                }
            }
        </style>
        
        
    </head>
    
    <body class="bg-white font-sans antialiased dark:bg-[#0a0a0a]">
        @livewireScripts
        <div class="flex flex-col min-h-screen">
            
            <header class="p-6 bg-white shadow-md sticky top-0 z-50 w-full dark:bg-[#1a1a1a]">
                <div class="container mx-auto flex justify-between items-center">
                    <a href="/" class="flex items-center space-x-2">
                        <img src="{{ asset('img/logo-negro.png') }}" 
                            alt="fit101" 
                            class="block h-9 w-auto dark:hidden">
                        
                        <img src="{{ asset('img/logo-verde.png') }}" 
                            alt="fit101 Dark" 
                            class="hidden h-9 w-auto dark:block">
                    </a>
                    
                    <!-- <div class='flex'>
                        @livewire('theme-switcher')
                    </div> -->
                </div>
            </header>

            <main class="relative flex-grow py-20 md:py-32 w-full bg-cover bg-no-repeat bg-center">
                
                <div class="absolute inset-0 bg-[url('/img/fondo2.jpg')] dark:bg-[url('/img/fondo2.jpg')] bg-cover bg-no-repeat bg-center">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
                </div>

                <div class="container mx-auto text-center px-4 relative z-10">
    
                    <div class="mb-8"> 
                        <img src="{{ asset('img/logo-1-light.png') }}" alt="Logo Gym Claro" 
                            class="h-20 w-auto mx-auto mb-4 block transition-opacity duration-300">
                    </div>
                    
                    <h1 class="text-5xl md:text-3xl font-extrabold text-white leading-tight mb-4 drop-shadow-lg">
                        Arma tus propias rutinas y lleva un control de tu progreso
                    </h1>
                    
                    <p class="text-xl md:text-2xl text-[#7bcb01] font-semibold mb-6 drop-shadow-md">
                        Exclusivo para nuestros clientes
                    </p>

                    <p class="text-lg text-gray-200 mb-10 drop-shadow-sm">
                        Entrenadores calificados
                    </p>
                    
                    <div class="inline-flex flex-col md:flex-row items-center space-y-4 md:space-x-4 md:space-y-0">
                        @guest
                        <a href="{{ route('register') }}" class="bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-3 px-8 text-lg rounded-lg shadow-xl transform hover:scale-105 transition duration-300">
                            Únete y Comienza Ahora
                        </a>
                        <a href="{{ route('login') }}" class="bg-transparent text-[#7bcb01] border-2 border-[#7bcb01] font-bold py-3 px-8 text-lg rounded-lg transition duration-300 shadow-xl transform hover:scale-105 dark:text-[#7bcb01] dark:border-[#7bcb01]">
                            Iniciar sesion
                        </a>
                        @endguest
                        @auth
                            @php
                                $dashboardRoute = route('dashboard'); 

                                if (auth()->user()->role === 'administrador') {
                                    $dashboardRoute = route('admin.dashboard');
                                } elseif (auth()->user()->role === 'trainer' || auth()->user()->role === 'nutriologo') {
                                    $dashboardRoute = route('employee.dashboard');
                                }
                            @endphp

                            <div 
                                x-data="{ showModal: false }" 
                                class="flex flex-col md:flex-row items-center space-y-4 md:space-x-4 md:space-y-0"
                            >
                                @if (!auth()->user()->is_active)
                                    <button 
                                        @click="showModal = true"
                                        class="bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-3 px-8 text-lg rounded-lg shadow-xl transform hover:scale-105 transition duration-300"
                                    >
                                        Mi Panel
                                    </button>
                                @else
                                    <a href="{{ $dashboardRoute }}" 
                                        class="bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-3 px-8 text-lg rounded-lg shadow-xl transform hover:scale-105 transition duration-300"
                                    >
                                        Mi Panel
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('logout') }}" class="inline-block">
                                    @csrf 
                                    <button type="submit" 
                                        class="bg-transparent text-[#7bcb01] border-2 border-[#7bcb01] font-bold py-3 px-8 text-lg rounded-lg transition duration-300 shadow-xl transform hover:scale-105 dark:text-[#7bcb01] dark:border-[#7bcb01]"
                                    >
                                        Cerrar Sesión
                                    </button>
                                </form>

                                <div 
                                    x-show="showModal" 
                                    x-transition.opacity.scale.80
                                    x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
                                >
                                    <div 
                                        class="bg-white dark:bg-[#1a1a1a] rounded-xl shadow-2xl p-6 max-w-sm w-full text-center transform transition-all duration-300"
                                    >
                                        <div class="flex justify-center mb-4">
                                            <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.3 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>

                                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-3">
                                            Cuenta Inactiva
                                        </h2>

                                        <p class="text-gray-600 dark:text-gray-300 mb-6">
                                            Tu cuenta está actualmente <span class="font-bold text-red-500">INACTIVA</span>.<br>
                                            Para recuperar el acceso a tu panel, por favor comunícate con el equipo de administración.
                                        </p>

                                        <button 
                                            @click="showModal = false" 
                                            class="bg-[#7bcb01] hover:bg-[#5aa301] text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                                            Entendido
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                    
                </div>
            </main>

            <section class="hidden md:block">
                <div class="fixed-social fixed bottom-0 left-2 z-40 flex flex-col items-center">
                    <a
                        href="https://www.instagram.com/101fitgym"
                        target="_blank"
                        class="block p-2 transition duration-300 hover:translate-y-[-3px] dark:text-[#7bcb01] text-white"
                        aria-label="instagram"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            fill="currentColor"
                            class="text-subtle-gray "
                            viewBox="0 0 16 16"
                        >
                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                        </svg>
                    </a>
                    <a
                        href="#"
                        target="_blank"
                        class="block p-2 transition duration-300 hover:translate-y-[-3px] dark:text-[#7bcb01] text-white"
                        aria-label="whatsapp"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            fill="currentColor"
                            class="text-subtle-gray "
                            viewBox="0 0 16 16"
                        >
                            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                        </svg>
                    </a>
                    <a
                        href="#"
                        target="_blank"
                        class="block p-2 transition duration-300 hover:translate-y-[-3px] dark:text-[#7bcb01] text-white"
                        aria-label="whatsapp"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            fill="currentColor"
                            class="text-subtle-gray "
                            viewBox="0 0 16 16"
                        >
                            <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                        </svg>
                    </a>
                </div>

                <div class="fixed-email fixed bottom-0 right-2 z-40 flex flex-col items-center ">
                    <a 
                        href="" 
                        class="dark:text-[#7bcb01] text-white transition duration-300"
                    >
                        correo@mail.com
                    </a>
                </div>
            </section>
            
            <footer class="bg-white text-black dark:text-[#7bcb01] text-center py-4 w-full dark:bg-[#1a1a1a]/95">
                
                <div class="md:hidden flex flex-col items-center space-y-4 py-4 border-t border-b border-gray-200 dark:border-gray-700">
                    
                    <div class="flex space-x-8 justify-center">
                        <a
                            href="#"
                            target="_blank"
                            class="block p-2 transition duration-300 hover:translate-y-[-3px] dark:text-[#7bcb01] text-black"
                            aria-label="whatsapp"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="text-subtle-gray "
                                viewBox="0 0 16 16"
                            >
                                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                            </svg>
                        </a>
                        <a
                            href="https://www.instagram.com/101fitgym"
                            target="_blank"
                            class="block p-2 transition duration-300 hover:translate-y-[-3px] dark:text-[#7bcb01] text-black"
                            aria-label="instagram"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                fill="currentColor"
                                class="text-subtle-gray "
                                viewBox="0 0 16 16"
                            >
                                <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                            </svg>
                        </a>
                    </div>
                    
                    <a href="#" class="text-sm text-black dark:text-[#7bcb01] hover:text-[#5aa301] transition duration-300">
                        correo@correo.com
                    </a>

                </div>
                
                <div class="container mx-auto mt-2 md:mt-0" style="width: 85%">
                    <p class="mb-0 text-sm text-black dark:text-[#7bcb01]">
                        &copy; {{ date('Y') }} Copyright: All rights reserved.
                    </p>
                </div>
                
            </footer>
            
        </div>
    </body>
</html>
