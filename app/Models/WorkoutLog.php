<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class WorkoutLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'routine_id',
        'duration_seconds',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // ------------------------------------------------------------------
    // RELACIONES
    // ------------------------------------------------------------------

    /**
     * Relación: Un registro de entrenamiento pertenece a un usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Un registro de entrenamiento está asociado a una rutina.
     */
    public function routine(): BelongsTo
    {
        return $this->belongsTo(Routine::class);
    }

    /**
     * Relación: Un registro de entrenamiento tiene muchas series realizadas.
     * Asumimos que WorkoutSet tiene campos: 'workout_log_id', 'exercise_id', 'reps', 'weight_kg', 'completed'.
     */
    public function workoutSets(): HasMany
    {
        return $this->hasMany(WorkoutSet::class);
    }
}
