<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
<div class="bg-white dark:bg-[#1a1a1a]/85 overflow-hidden shadow-xl sm:rounded-lg p-6 md:p-10">

    <!-- 1. SALUDO INICIAL -->
    <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-6 border-b pb-4 dark:border-gray-700">
        Bienvenido de Vuelta, <span class="text-primary">{{ Auth::user()->name }} {{Auth::user()->last_name}}</span>!
    </h1>

    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
        ¡Tu plan de fitness está activo! Aquí tienes un resumen rápido de tu progreso.
    </p>

    <!-- Cálculo de progreso semanal -->
    @php
        $progressPercent = ($weeklySessionsTarget > 0) ? ($weeklySessionsCompleted / $weeklySessionsTarget) * 100 : 0;
        $progressPercent = min(100, $progressPercent); // Limitar a 100%
        $remaining = $weeklySessionsTarget - $weeklySessionsCompleted;
    @endphp

    <!-- 2. GRID DE MÉTRICAS CLAVE -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-10">
        
        <!-- TARJETA 1: Meta Semanal -->
        <div class="p-6 bg-primary/10 dark:bg-primary/20 rounded-xl shadow-lg flex flex-col justify-between border border-primary">
            <div>
                <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Esta Semana
                </div>
                <p class="text-4xl font-extrabold text-primary">
                    {{ $weeklySessionsCompleted }}<span class="text-xl font-medium text-gray-600 dark:text-gray-400">/{{ $weeklySessionsTarget }}</span>
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Entrenamientos Completados
                </p>
            </div>
            <!-- Barra de Progreso -->
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="h-2.5 rounded-full bg-primary transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Te faltan {{ $remaining }} para alcanzar tu meta.
                </p>
            </div>
        </div>

        <!-- TARJETA 2: Último Peso -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
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
        </div>

        <!-- TARJETA 3: Rutinas Activas (Self Made + Assigned) -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                Rutinas Guardadas
            </div>
            <p class="text-4xl font-extrabold text-gray-900 dark:text-white">
                {{ $selfMadeRoutines->count() + $assignedRoutines->count() }}
            </p>
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                <p>- {{ $selfMadeRoutines->count() }} Creadas por ti</p>
                <p>- {{ $assignedRoutines->count() }} Asignadas por tu Entrenador</p>
            </div>
        </div>

        <!-- TARJETA 4: Último Entrenamiento -->
        <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                Último Entrenamiento
            </div>
            @if ($recentWorkouts->count() > 0)
                @php $latest = $recentWorkouts->first(); @endphp
                <p class="text-xl font-bold text-gray-900 dark:text-white truncate" title="{{ $latest->routine->name }}">
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
        </div>

    </div>

    <!-- 3. DETALLE DE RUTINAS Y LOGS (TABS) -->
    <div x-data="{ currentTab: 'assigned' }" class="mt-10">
        <!-- PESTAÑAS (TABS) -->
        <div class="flex border-b border-gray-200 dark:border-gray-700">
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
        </div>

        <!-- CONTENIDO DE LAS PESTAÑAS -->
        <div class="py-4">

            <!-- CONTENIDO: PLANES ASIGNADOS -->
            <div x-show="currentTab === 'assigned'" x-cloak>
                @if ($assignedRoutines->isEmpty())
                    <div class="text-center p-8 bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
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
                            <a href="{{ route('routine.workout', ['routine' => $routine->id]) }}" class="block p-4 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition duration-200 border border-l-4 border-primary">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $routine->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Asignada por: {{ $routine->creator->name ?? 'Entrenador Desconocido' }}
                                </p>
                                <span class="inline-flex items-center mt-2 px-3 py-1 text-xs font-medium bg-primary/20 text-primary-dark rounded-full">
                                    {{ $routine->routineExercises->count() }} ejercicios
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- CONTENIDO: MIS RUTINAS CREADAS -->
            <div x-show="currentTab === 'selfMade'" x-cloak>
                @if ($selfMadeRoutines->isEmpty())
                    <div class="text-center p-8 bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                        <p class="text-xl font-semibold text-gray-600 dark:text-gray-400">
                            No has creado ninguna rutina aún.
                        </p>
                        <a href="{{ route('routine.builder') }}" class="mt-4 inline-block bg-primary hover:bg-primary-dark text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                            ¡Crear mi Primera Rutina!
                        </a>
                    </div>
                @else
                    <!-- Alerta de Límite (Solo si es Cliente Regular) -->
                    @if ($user->isRegularClient())
                        <div class="p-4 mb-6 text-sm bg-red-50 dark:bg-red-950 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                            <p>
                                <strong>Límite de Rutinas:</strong> Eres Cliente Regular. Has usado
                                **{{ $selfMadeRoutines->count() }} de {{ $routineLimit }}** rutinas personalizadas disponibles.
                                <a href="{{ route('subscription.upgrade') }}" class="font-bold underline hover:text-red-800 dark:hover:text-red-200">
                                    ¡Actualiza para tener espacio ilimitado!
                                </a>
                            </p>
                        </div>
                    @endif

                    <!-- LISTA DE MIS RUTINAS -->
                    <div class="space-y-4">
                        @foreach ($selfMadeRoutines as $routine)
                            <a href="{{ route('routine.workout', ['routine' => $routine->id]) }}" class="block p-4 bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition duration-200 border border-l-4 border-primary">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $routine->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Creada por ti
                                </p>
                                <span class="inline-flex items-center mt-2 px-3 py-1 text-xs font-medium bg-primary/20 text-primary-dark rounded-full">
                                    {{ $routine->routineExercises->count() }} ejercicios
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- CONTENIDO: LOGS RECIENTES -->
            <div x-show="currentTab === 'recentLogs'" x-cloak>
                @if ($recentWorkouts->isEmpty())
                    <div class="text-center p-8 bg-gray-50 dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
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
                            <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow border border-l-4 border-gray-300 dark:border-gray-600">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $log->routine->name }}</h3>
                                <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 flex justify-between">
                                    <div>
                                        <span class="font-medium">Completado:</span> 
                                        {{ \Carbon\Carbon::parse($log->completed_at)->format('d/m/Y H:i') }}
                                    </div>
                                    <div>
                                        <span class="font-medium">Duración:</span> 
                                        <span class="font-bold text-primary">{{ $this->formatSeconds($log->duration_seconds) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- MANTENEMOS EL ESTILO DENTRO DEL ÚNICO DIV RAÍZ -->
<style>
    :root {
        --primary-color: #7bcb01; /* Verde/Lima principal */
    }
    .text-primary { color: var(--primary-color); }
    .border-primary { border-color: var(--primary-color); }
    .hover\:bg-primary-dark:hover { background-color: #5aa301; }
    .bg-primary { background-color: var(--primary-color); }
    .bg-primary\/10 { background-color: rgba(123, 203, 1, 0.1); }
    .bg-primary\/20 { background-color: rgba(123, 203, 1, 0.2); }
</style>


</div>