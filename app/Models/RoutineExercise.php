<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

// Usamos 'Pivot' en lugar de 'Model' ya que esta es una tabla intermedia/pivote
class RoutineExercise extends Pivot 
{
    use HasFactory;

    // Aunque extiende Pivot, definir la tabla es buena práctica si el nombre no sigue la convención
    protected $table = 'routine_exercises'; 

    protected $fillable = [
        'routine_id',
        'exercise_id',
        'order',
        'target_sets',
        'target_reps',
        'target_weight',
    ];

    // Opcional: Relaciones de conveniencia
    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}