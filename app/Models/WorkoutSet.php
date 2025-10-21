<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_log_id',
        'exercise_id',
        'set_number',
        'reps',
        'weight',
    ];

    /**
     * Relación: Una serie pertenece a un registro de entrenamiento.
     */
    public function workoutLog(): BelongsTo
    {
        return $this->belongsTo(WorkoutLog::class);
    }

    /**
     * Relación: Una serie está asociada a un ejercicio.
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}