<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoutineExercise extends Pivot 
{
    use HasFactory;

    protected $table = 'routine_exercises'; 

    protected $fillable = [
        'routine_id',
        'exercise_id',
        'order',
        'target_sets',
        'target_reps',
        'target_weight',
        'sets_details',
    ];

    protected $casts = [
        'sets_details' => 'array', 
    ];

    public function routine()
    {
        return $this->belongsTo(Routine::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}