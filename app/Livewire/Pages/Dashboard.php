<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\WorkoutLog;
use App\Models\Routine;
use App\Models\ClientProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class Dashboard extends Component
{
    // Esta línea funcionará una vez Livewire controle la ruta
    public $layout = 'components.layouts.app'; 

   public $user;
    public int $weeklySessionsTarget = 3; // Meta semanal
    public int $weeklySessionsCompleted = 0;
    
    // Todas estas propiedades están type-hinted como Eloquent\Collection
    public Collection $recentWorkouts;
    public Collection $selfMadeRoutines;
    public Collection $assignedRoutines; 
    
    public $latestWeight = null; 

    // ------------------------------------------------------------------
    // CICLO DE VIDA
    // ------------------------------------------------------------------

    public function mount()
    {
        // CORRECCIÓN CLAVE: Inicializar con new Collection() (referenciando Eloquent\Collection) 
        // en lugar de collect() (que devuelve Support\Collection) para evitar el TypeError.
        $this->recentWorkouts = new Collection();
        $this->selfMadeRoutines = new Collection();
        $this->assignedRoutines = new Collection(); 

        $this->user = Auth::user();
        $this->loadDashboardData();
    }

    // ------------------------------------------------------------------
    // LÓGICA DE CARGA DE DATOS
    // ------------------------------------------------------------------

    /**
     * Carga todos los datos necesarios para el dashboard.
     */
    protected function loadDashboardData()
    {
        $userId = $this->user->id;

        // 1. CÁLCULO DE SESIONES SEMANALES
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::MONDAY);

        $this->weeklySessionsCompleted = WorkoutLog::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->count();

        // 2. ÚLTIMOS ENTRENAMIENTOS
        $this->recentWorkouts = WorkoutLog::with('routine')
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();
        
        // 3. LÓGICA DE RUTINAS
        
        // Determinar el límite
        if (method_exists($this->user, 'isFullClient') && $this->user->isFullClient()) {
             $this->routineLimit = 999; 
        } else {
            $this->routineLimit = 5;
        }

        // 3a. Rutinas CREADAS POR EL PROPIO CLIENTE (selfMadeRoutines)
        $this->selfMadeRoutines = Routine::where('user_id', $userId)
                                        ->where('creator_id', $userId)
                                        ->get();

        // 3b. Rutinas ASIGNADAS POR EL ENTRENADOR (assignedRoutines) 
        $this->assignedRoutines = Routine::where('user_id', $userId)
                                        ->where('creator_id', '!=', $userId)
                                        ->get();

        // 4. ÚLTIMO PESO REGISTRADO
        
        $profile = $this->user->profile;

        if ($profile && $profile->weight) {
            $this->latestWeight = (object) [
                'weight' => $profile->weight,
                'created_at' => $profile->updated_at ?? now(), 
            ];
        } else {
            $this->latestWeight = null;
        }
    }

    // ------------------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------------------

    /**
     * Helper para formatear segundos a HH:MM o HH:MM:SS.
     */
    public function formatSeconds(int $seconds): string
    {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        
        if ($h > 0) {
            return sprintf('%02d:%02d:%02d', $h, $m, $s);
        }
        return sprintf('%02d:%02d', $m, $s);
    }

    // ------------------------------------------------------------------
    // RENDER
    // ------------------------------------------------------------------

    public function render()
    {
        // Cálculo de variables de la barra de progreso
        $progressPercent = ($this->weeklySessionsTarget > 0) 
                           ? ($this->weeklySessionsCompleted / $this->weeklySessionsTarget) * 100 
                           : 0;
        $progressPercent = min(100, $progressPercent); 
        $remaining = max(0, $this->weeklySessionsTarget - $this->weeklySessionsCompleted);

        return view('livewire.pages.dashboard', [
            'progressPercent' => $progressPercent,
            'remaining' => $remaining,
        ])->title('Dashboard');
    }
}