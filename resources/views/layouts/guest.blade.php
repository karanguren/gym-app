<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Acceso Admin</title>
        <!-- <script>
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
        </script> -->
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[url('../../../public/img/fondo2.jpg')] bg-center bg-cover">
            {{ $slot }}
        </div>
        @livewireScripts
    </body>
</html>