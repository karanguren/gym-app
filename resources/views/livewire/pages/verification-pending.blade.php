<div class="div-principal">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="max-w-lg w-full card-tb-ve">

            <svg class="mx-auto h-16 w-16 svg-ve " fill="none" viewBox="0 0 24 24" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>

            <h1 class="titulos-card">
                Solicitud en Revisión
            </h1>

            <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                ¡Gracias por completar su perfil!
            </p>

            <p class="mt-2 text-md text-gray-700 dark:text-gray-200 font-semibold">
                Su acceso está **pendiente de verificación** por parte de la administración del gimnasio.
            </p>

            <div class="mt-6 p-4 bg-orange-300 rounded-lg text-sm text-black border border-orange-400">
                <p>Le notificaremos por correo electrónico una vez que su perfil haya sido aprobado y obtenga acceso
                    exclusivo a todas las herramientas.</p>
            </div>

            <div class="mt-8">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full btn-outline-ve">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div>
