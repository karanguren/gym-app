<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8 ">
        <div class="md:p-8">

            <h1 class="titles-border">
                Bienvenido, <span class="text-primary">{{ Auth::user()->name }} {{ Auth::user()->last_name }}</span>!
            </h1>

            @php
                $progressPercent =
                    $weeklySessionsTarget > 0 ? ($weeklySessionsCompleted / $weeklySessionsTarget) * 100 : 0;
                $progressPercent = min(100, $progressPercent);
                $remaining = $weeklySessionsTarget - $weeklySessionsCompleted;

                $assignedTrainer = Auth::user()->profile->assignedTrainer ?? null;
                $statusAssignedTrainer = Auth::user()->profile->assignment_status ?? null; // ('unassigned','pending','accepted','rejected')

            @endphp

            <!-- 2. GRID DE MÉTRICAS CLAVE -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

                <!-- TARJETA 1: Entrenador Personal (REEMPLAZO de Meta Semanal) -->
                @if (Auth::user()->client_type == 'personalized')
                    <div class="card-tb-ve-v2 !p-8">
                        <div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                Tu Entrenador Asignado
                            </div>

                            @if ($assignedTrainer)
                                <!-- CON ENTRENADOR -->
                                <div class="flex items-center space-x-4 mb-4">
                                    <img src="{{ $assignedTrainer->profile_photo_path }}" alt="{{ $assignedTrainer->name }}"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-primary"
                                        onerror="this.onerror=null; this.src='https://placehold.co/48x48/7bcb01/ffffff?text=E'">
                                    <div>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white">
                                            {{ $assignedTrainer->name }} {{ $assignedTrainer->last_name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Entrenador Personal
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('client.trainer-selection') }}" class="w-full btn-outline-lime">
                                    Ver Perfil / Cambiar
                                </a>
                            @elseif ($statusAssignedTrainer == 'pending')
                                <div class="flex items-center space-x-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Entrenador Personal (Pendiente de verificación)
                                        </p>
                                    </div>
                                </div>
                            @else
                                <!-- SIN ENTRENADOR -->
                                <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-lg font-semibold text-gray-600 dark:text-gray-300 mb-4">
                                        ¡Sin entrenador aún!
                                    </p>
                                    <a href="{{ route('client.trainer-selection') }}"
                                        class="w-full inline-block bg-primary hover:bg-[#5aa301] text-white font-bold py-2 px-4 rounded-lg transition duration-300 shadow-md">
                                        QUIERO UN ENTRENADOR
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- TARJETA 2: Último Peso (Mantiene el contenido original) -->
                {{-- <div
                    class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Último Peso Registrado
                    </div>
                    @if ($latestWeight)
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-white">
                            {{ number_format($latestWeight->weight, 1) }} kg
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Actualizado: {{ \Carbon\Carbon::parse($latestWeight->created_at)->diffForHumans() }}
                        </p>
                    @else
                        <p class="text-xl font-bold text-gray-500 dark:text-gray-400 py-3">
                            Sin datos
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Completa tu perfil para registrar tu peso.
                        </p>
                    @endif
                </div> --}}

                <!-- TARJETA 3: Rutinas Activas (Mantiene el contenido original) -->
                <div
                    class="card-tb-ve-v2 !p-8">
                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Rutinas Guardadas
                    </div>
                    @if (Auth::user()->client_type == 'personalized')
                        <p class="titles">
                            {{ $selfMadeRoutines->count() + $assignedRoutines->count() }}
                        </p>
                    @else
                        <p class="titles">
                            {{ $selfMadeRoutines->count() }}
                        </p>
                    
                    @endif
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <p>- {{ $selfMadeRoutines->count() }} Creadas por ti</p>

                        @if (Auth::user()->client_type == 'personalized')
                            <p>- {{ $assignedRoutines->count() }} Asignadas por tu Entrenador</p>
                        @endif

                        <a href="{{ route('client.routines') }}" class="w-full inline-block btn-outline-lime mt-4">
                            MIS RUTINAS
                        </a>
                    </div>
                </div>

                <!-- TARJETA 4: Último Entrenamiento (Mantiene el contenido original) -->
                {{-- <div
                    class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Último Entrenamiento
                    </div>
                    @if ($recentWorkouts->count() > 0)
                        @php $latest = $recentWorkouts->first(); @endphp
                        <p class="text-xl font-bold text-gray-900 dark:text-white truncate"
                            title="{{ $latest->routine->name }}">
                            {{ $latest->routine->name }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Hace {{ \Carbon\Carbon::parse($latest->completed_at)->diffForHumans(null, true) }}
                        </p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mt-2">
                            Duración: {{ $this->formatSeconds($latest->duration_seconds) }}
                        </p>
                    @else
                        <p class="text-xl font-bold text-gray-500 dark:text-gray-400 py-3">
                            Sin actividad
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            ¡Es hora de empezar tu primera rutina!
                        </p>
                    @endif
                </div> --}}

            </div>

            <!-- 3. DETALLE DE RUTINAS Y LOGS (TABS) -->
            <div x-data="{ currentTab: 'assigned' }" class="mt-10">
                <!-- PESTAÑAS (TABS) -->
                {{-- <div class="flex border-b border-gray-200 dark:border-gray-700">
                    <button @click="currentTab = 'assigned'"
                        :class="{ 'border-primary text-primary dark:text-primary': currentTab === 'assigned', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': currentTab !== 'assigned' }"
                        class="py-2 px-4 border-b-2 font-semibold transition duration-150">
                        Rutinas Asignadas ({{ $assignedRoutines->count() }})
                    </button>
                    <button @click="currentTab = 'selfMade'"
                        :class="{ 'border-primary text-primary dark:text-primary': currentTab === 'selfMade', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': currentTab !== 'selfMade' }"
                        class="py-2 px-4 border-b-2 font-semibold transition duration-150">
                        Mis Rutinas ({{ $selfMadeRoutines->count() }})
                    </button>
                    <button @click="currentTab = 'recentLogs'"
                        :class="{ 'border-primary text-primary dark:text-primary': currentTab === 'recentLogs', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200': currentTab !== 'recentLogs' }"
                        class="py-2 px-4 border-b-2 font-semibold transition duration-150">
                        Logs Recientes ({{ $recentWorkouts->count() }})
                    </button>
                </div> --}}

                <!-- CONTENIDO DE LAS PESTAÑAS -->
                <div class="py-4">

                    <!-- CONTENIDO: PLANES ASIGNADOS -->
                    {{-- <div x-show="currentTab === 'assigned'" x-cloak>
                        @if ($assignedRoutines->isEmpty())
                            <div
                                class="text-center p-8 bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                                <p class="text-xl font-semibold text-gray-600 dark:text-gray-400">
                                    Aún no tienes **Planes Asignados**
                                </p>
                                <p class="text-gray-500 dark:text-gray-400 mt-2">
                                    Contacta a tu entrenador para que te asigne una rutina de ejercicios.
                                </p>
                            </div>
                        @else
                            <!-- LISTA DE RUTINAS ASIGNADAS (RENDERIZADO) -->
                            <div class="space-y-4">
                                @foreach ($assignedRoutines as $routine)
                                    <!-- Las rutinas asignadas solo tienen la opción de iniciar el entrenamiento -->
                                    <a href="{{ route('routine.workout', ['routine' => $routine->id]) }}"
                                        class="block p-4 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition duration-200 border border-l-4 border-primary">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $routine->name }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Asignada por: {{ $routine->creator->name ?? 'Entrenador Desconocido' }}
                                        </p>
                                        <span
                                            class="inline-flex items-center mt-2 px-3 py-1 text-xs font-medium bg-primary/20 text-primary-dark rounded-full">
                                            {{ $routine->routineExercises->count() }} ejercicios
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div> --}}

                    <!-- CONTENIDO: MIS RUTINAS CREADAS (AHORA PERMITE EDITAR Y EJECUTAR) -->
                    {{-- <div x-show="currentTab === 'selfMade'" x-cloak>
                        @if ($selfMadeRoutines->isEmpty())
                            <div
                                class="text-center p-8 bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                                <p class="text-xl font-semibold text-gray-600 dark:text-gray-400">
                                    No has creado ninguna rutina aún.
                                </p>
                                <a href="{{ route('routine.builder') }}"
                                    class="mt-4 inline-block bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                                    ¡Crear mi Primera Rutina!
                                </a>
                            </div>
                        @else
                            <!-- Alerta de Límite (Solo si es Cliente Regular) -->
                            @if ($user->isRegularClient())
                                <div
                                    class="p-4 mb-6 text-sm bg-red-50 dark:bg-red-950 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                                    <p>
                                        <strong>Límite de Rutinas:</strong> Eres Cliente Regular. Has usado
                                        **{{ $selfMadeRoutines->count() }} de {{ $routineLimit }}** rutinas
                                        personalizadas disponibles.
                                        <a href="{{ route('subscription.upgrade') }}"
                                            class="font-bold underline hover:text-red-800 dark:hover:text-red-200">
                                            ¡Actualiza para tener espacio ilimitado!
                                        </a>
                                    </p>
                                </div>
                            @endif

                            <!-- LISTA DE MIS RUTINAS -->
                            <div class="space-y-4">
                                @foreach ($selfMadeRoutines as $routine)
                                    <div
                                        class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition duration-200 border border-l-4 border-primary flex items-center justify-between">

                                        <!-- Contenido -->
                                        <a href="{{ route('routine.workout', ['routine' => $routine->id]) }}"
                                            class="flex-grow min-w-0 pr-4 group">
                                            <h3
                                                class="text-lg font-bold text-gray-900 dark:text-white truncate group-hover:text-primary transition duration-200">
                                                {{ $routine->name }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                Creada por ti
                                            </p>
                                            <span
                                                class="inline-flex items-center mt-2 px-3 py-1 text-xs font-medium bg-primary/20 text-primary-dark rounded-full">
                                                {{ $routine->routineExercises->count() }} ejercicios
                                            </span>
                                        </a>

                                        <!-- Botón de Acción (Iniciar Entrenamiento) -->
                                        <a href="{{ route('routine.workout', ['routine' => $routine->id]) }}"
                                            class="flex-shrink-0 bg-primary hover:bg-[#5aa301] text-white p-2 rounded-full transition duration-300 shadow-lg"
                                            title="Iniciar entrenamiento">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div> --}}

                    <!-- CONTENIDO: LOGS RECIENTES (Mantiene el contenido original) -->
                    {{-- <div x-show="currentTab === 'recentLogs'" x-cloak>
                        @if ($recentWorkouts->isEmpty())
                            <div
                                class="text-center p-8 bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                                <p class="text-xl font-semibold text-gray-600 dark:text-gray-400">
                                    Aún no has completado ningún entrenamiento.
                                </p>
                                <p class="text-gray-500 dark:text-gray-400 mt-2">
                                    ¡Completa una rutina para ver tu progreso aquí!
                                </p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($recentWorkouts as $log)
                                    <div
                                        class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow border border-l-4 border-gray-300 dark:border-gray-600">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $log->routine->name }}</h3>
                                        <div
                                            class="mt-2 text-sm text-gray-600 dark:text-gray-400 flex justify-between">
                                            <div>
                                                <span class="font-medium">Completado:</span>
                                                {{ \Carbon\Carbon::parse($log->completed_at)->format('d/m/Y H:i') }}
                                            </div>
                                            <div>
                                                <span class="font-medium">Duración:</span>
                                                <span
                                                    class="font-bold text-primary">{{ $this->formatSeconds($log->duration_seconds) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div> --}}
                </div>
            </div>
        </div>

        <!-- MANTENEMOS EL ESTILO DENTRO DEL ÚNICO DIV RAÍZ -->
        <style>
            :root {
                --primary-color: #7bcb01;
                /* Verde/Lima principal */
            }

            .text-primary {
                color: var(--primary-color);
            }

            .border-primary {
                border-color: var(--primary-color);
            }

            .hover\:bg-primary-dark:hover {
                background-color: #5aa301;
            }

            .bg-primary {
                background-color: var(--primary-color);
            }

            .bg-primary\/10 {
                background-color: rgba(123, 203, 1, 0.1);
            }

            .bg-primary\/20 {
                background-color: rgba(123, 203, 1, 0.2);
            }
        </style>
    </div>
</div>
