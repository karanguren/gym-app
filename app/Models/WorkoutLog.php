<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // 🎯 Importar HasMany

class WorkoutLog extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'routine_id',
        'duration_seconds',
        'completed_at',
        // 🎯 ELIMINADO: 'details' ya no se usa, ahora se usa la tabla workout_sets.
    ];

    /**
     * Los atributos que deben ser casteados a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed_at' => 'datetime',
        // 🎯 ELIMINADO: 'details' ya no se castea.
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
     * 🎯 Relación: Un registro de entrenamiento tiene muchas series realizadas.
     * Esta es la nueva "fuente de verdad" para el progreso del entrenamiento.
     */
    public function workoutSets(): HasMany
    {
        // Asumimos que el modelo WorkoutSet existe y está correctamente definido.
        return $this->hasMany(WorkoutSet::class);
    }
}
