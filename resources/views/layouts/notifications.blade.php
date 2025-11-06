{{-- 
    ARCHIVO: notification.blade.php
    RESPONSABILIDAD: Contenedor flotante y lógica JavaScript 
    para escuchar eventos 'notify' de Livewire.

    ESTE ARCHIVO INCLUYE UNA LÍNEA CRÍTICA CON console.log() PARA DEPURAR.
--}}
<div id="toast-container" class="fixed top-4 right-4 z-[50] space-y-2 max-w-sm w-full pointer-events-none p-4 sm:p-0">
    {{-- Los toasts se inyectarán aquí mediante JavaScript --}}
</div>


<script>
    // Aseguramos que la lógica se ejecuta cuando Livewire y el DOM están listos
    document.addEventListener('livewire:initialized', () => {

        const TOAST_CONFIG = {
            success: {
                iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                bg: 'bg-white dark:bg-gray-800 border-l-4 border-emerald-500',
                text: 'text-gray-900 dark:text-white',
                iconClass: 'text-emerald-500',
                autoClose: true,
                duration: 5000 
            },
            error: {
                iconPath: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                bg: 'bg-white dark:bg-gray-800 border-l-4 border-red-500',
                text: 'text-gray-900 dark:text-white',
                iconClass: 'text-red-500',
                autoClose: false,
                duration: 3000 
            },
            warning: {
                iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.3 16.7c-.77 1.333.192 3 1.732 3z',
                bg: 'bg-white dark:bg-gray-800 border-l-4 border-amber-500',
                text: 'text-gray-900 dark:text-white',
                iconClass: 'text-amber-500',
                autoClose: true,
                duration: 8000
            },
            info: {
                iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                bg: 'bg-white dark:bg-gray-800 border-l-4 border-blue-500',
                text: 'text-gray-900 dark:text-white',
                iconClass: 'text-blue-500',
                autoClose: true,
                duration: 4000
            }
        };


        // Listener principal de Livewire 3
        window.Livewire.on('notify', (event) => {
            
            // ====================================================
            // *** PUNTO DE DEPURACIÓN CRÍTICO ***
            // ESTA LÍNEA DEBE APARECER EN LA CONSOLA AL DISPARAR EL EVENTO.
            // ====================================================
            console.log('EVENTO NOTIFY CAPTURADO:', event);
            // ====================================================

            // El evento siempre llega como un array en Livewire 3, incluso si solo hay un payload
            const data = event[0] || event;
            const { message, type = 'info', duration: customDuration } = data;


            const container = document.getElementById('toast-container');
            if (!container) return; 


            const config = TOAST_CONFIG[type] || TOAST_CONFIG.info;
            const finalDuration = customDuration !== undefined ? customDuration : config.duration;
            
            const toast = document.createElement('div');

            // Clases para estilo y animación
            toast.className = `flex items-center p-4 rounded-lg shadow-xl pointer-events-auto transition ease-out duration-300 opacity-0 translate-x-full ${config.bg} ${config.text}`;
            toast.setAttribute('role', 'alert');


            // Contenido HTML del toast
            toast.innerHTML = `
                {{-- Ícono de Alerta --}}
                <div class="w-6 h-6 mr-3 flex-shrink-0">
                    <svg class="w-6 h-6 ${config.iconClass}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="${config.iconPath}"></path>
                    </svg>
                </div>
                
                {{-- Mensaje --}}
                <div class="text-sm font-medium flex-grow">${message}</div>
                
                {{-- Botón de Cierre --}}
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 inline-flex h-8 w-8 hover:bg-gray-200 dark:hover:bg-gray-700 transition" aria-label="Cerrar">
                    <span class="sr-only">Close</span>
                    <svg class="w-5 h-5 ${config.iconClass}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            `;


            // Función de cierre con transición
            const closeToast = () => {
                // Animación de salida: mover a la derecha
                toast.classList.remove('opacity-100', 'translate-x-0');
                toast.classList.add('opacity-0', 'translate-x-full'); 
                
                // Remover después de la transición
                setTimeout(() => {
                    toast.remove();
                }, 300); 
            };
            

            // Inserción en el DOM (LIFO: Last In, First Out)
            if (container.firstChild) {
                container.insertBefore(toast, container.firstChild);
            } else {
                container.appendChild(toast);
            }


            // Forzar animación de entrada
            requestAnimationFrame(() => {
                toast.classList.remove('opacity-0', 'translate-x-full');
                toast.classList.add('opacity-100', 'translate-x-0');
            });


            // Cierre automático
            if (config.autoClose && finalDuration > 0) {
                setTimeout(closeToast, finalDuration);
            }
            
            // Cierre manual
            const closeButton = toast.querySelector('button');
            if (closeButton) {
                closeButton.addEventListener('click', closeToast);
            }
            // Permite cerrar haciendo clic en el toast
            toast.addEventListener('click', closeToast); 

        });

    });

</script>