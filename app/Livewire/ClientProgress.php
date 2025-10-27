<?php

namespace App\Livewire;

use App\Models\RoutineExercise;
use App\Models\WorkoutLog;
use App\Models\WorkoutSet;
use App\Models\Exercise; // AÑADIDO: Necesitamos el modelo Exercise para obtener los nombres
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClientProgress extends Component
{
    // Datos de estadísticas
    public int $totalWorkouts = 0;
    public int $maxDuration = 0; // en segundos
    public int $avgDuration = 0; // en segundos

    // Progreso de fuerza por ejercicio (Ej: ['2' => ['name' => 'Bench Press', 'max_weight' => 100.0]])
    public array $strengthProgress = [];

    // Propiedades para la paginación/ordenación (opcional, dejamos solo ordenación simple por ahora)
    public string $sortBy = 'max_weight';
    public string $sortDirection = 'desc';

    public function mount()
    {
        // Aseguramos que solo el cliente vea su progreso
        $this->calculateGeneralStats();
        $this->calculateMaxStrength();
    }

    /**
     * Calcula el total de entrenamientos y las duraciones promedio/máximas.
     */
    protected function calculateGeneralStats()
    {
        $userId = auth()->id();

        // Carga todos los logs del usuario
        $logs = WorkoutLog::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->get();

        $this->totalWorkouts = $logs->count();

        if ($this->totalWorkouts > 0) {
            $totalDuration = $logs->sum('duration_seconds');
            $this->avgDuration = (int) ($totalDuration / $this->totalWorkouts);
            $this->maxDuration = $logs->max('duration_seconds');
        }
    }

    /**
     * Calcula el peso máximo levantado para cada ejercicio por el usuario.
     */
    protected function calculateMaxStrength()
    {
        $userId = auth()->id();
        
        // 1. Obtener los IDs de todos los logs de entrenamiento del usuario actual
        $logIds = WorkoutLog::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->pluck('id');

        if ($logIds->isEmpty()) {
            $this->strengthProgress = [];
            return;
        }

        // 2. Ejecutar una consulta optimizada para encontrar el peso máximo (columna 'weight')
        $maxWeights = WorkoutSet::select('exercise_id', DB::raw('MAX(weight) as max_weight'))
            // Solo sets asociados a los logs del usuario
            ->whereIn('workout_log_id', $logIds) 
            // SOLUCIÓN: Quitamos la condición de 'completed' ya que la columna no existe
            // ->where('completed', true) 
            // Solo registros con peso mayor a cero
            ->where('weight', '>', 0) 
            ->groupBy('exercise_id')
            ->get()
            ->keyBy('exercise_id')
            ->toArray();

        // 3. Obtener los nombres de los ejercicios correspondientes a los IDs encontrados
        $exerciseIds = array_keys($maxWeights);
        
        // Cargar los nombres desde el modelo Exercise
        $exercises = Exercise::whereIn('id', $exerciseIds)
             ->pluck('name', 'id') // Pluck devuelve un array [id => name]
             ->toArray();
        
        // 4. Combinar los resultados
        $progress = [];
        foreach ($maxWeights as $exerciseId => $data) {
            // Usamos el array de nombres de ejercicio para buscar el nombre
            $exerciseName = $exercises[$exerciseId] ?? 'Ejercicio Desconocido';
            
            $progress[] = [
                'id' => $exerciseId,
                'name' => $exerciseName,
                'max_weight' => $data['max_weight'] ?? 0,
            ];
        }

        $this->strengthProgress = $progress;
    }

    /**
     * Retorna el progreso de fuerza ordenado para la vista.
     */
    public function getSortedStrengthProgressProperty()
    {
        // En lugar de ordenar en la base de datos, ordenamos el array en PHP
        return collect($this->strengthProgress)->sortByDesc('max_weight')->values()->all();
    }

    /**
     * Helper para formatear segundos a HH:MM:SS
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

    public function render()
    {
        // CORRECCIÓN CLAVE: Pasar la propiedad computada a la vista con el nombre esperado ($sortedProgress)
        $sortedProgress = $this->sortedStrengthProgress;

        return view('livewire.client-progress', [
            'sortedProgress' => $sortedProgress,
        ]);
    }
}
