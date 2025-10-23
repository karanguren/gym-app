<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Routine extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'notes',
    ];


    protected $appends = ['exercises_count'];

    // ------------------------------------------------------------------
    // ACCESORES
    // ------------------------------------------------------------------

    /**
     * Accesor para obtener el número de ejercicios en la rutina.
     * @return int
     */
    public function getExercisesCountAttribute(): int
    {
        // Usa la relación para contar las filas en routine_exercises
        return $this->routineExercises()->count();
    }

    // ------------------------------------------------------------------
    // RELACIONES
    // ------------------------------------------------------------------

    /**
     * Relación: Una rutina pertenece a un usuario (cliente).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Una rutina tiene muchos RoutineExercise (el pivote que guarda sets/reps).
     */
    public function routineExercises(): HasMany
    {
        // Ordena por la columna 'order' de la tabla intermedia
        return $this->hasMany(RoutineExercise::class)->orderBy('order');
    }

    /**
     * CRÍTICO: Relación BelongsToMany para acceder a la tabla pivote y sus datos.
     * Esto asegura que los modelos Exercise tengan la propiedad ->pivot.
     */
    public function exercises(): BelongsToMany
    {
       return $this->belongsToMany(Exercise::class, 'routine_exercises')
                    ->withPivot('target_sets', 'target_reps', 'target_weight', 'order', 'sets_details')
                    ->withTimestamps()
                    ->orderBy('routine_exercises.order');
    }
}
