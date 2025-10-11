<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trainer extends Model
{
    use HasFactory;

    /**
     * Las columnas que se pueden asignar masivamente.
     */
    protected $fillable = [
        'user_id',
        'certification_id',
        'specialty',
        'hourly_rate',
        'bio',
        'is_active',
    ];

    /**
     * El perfil de entrenador pertenece a un usuario (Relación 1:1 inversa).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
