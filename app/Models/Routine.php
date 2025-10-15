<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection; // Importar Collection para el retorno de tipo

class Routine extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'exercise_ids',
        'notes',
    ];

    /**
     * El casting es esencial para que 'exercise_ids' se maneje como un array
     * automáticamente por Eloquent.
     */
    protected $casts = [
        'exercise_ids' => 'array',
    ];

    /**
     * Relación: Una rutina pertenece a un usuario (cliente).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Obtiene los objetos Exercise asociados a esta rutina.
     * Esto permite cargar los detalles de los ejercicios de la DB.
     * @return Collection
     */
    public function exercises(): Collection
    {
        $exerciseIds = $this->exercise_ids;
        
        // 🎯 CORRECCIÓN CLAVE: Si exercise_ids es NULL o un array vacío,
        // devolvemos una colección vacía. Esto evita el TypeError.
        if (empty($exerciseIds)) {
            return Collection::make();
        }

        // Si tenemos IDs válidos, procedemos con la consulta.
        return Exercise::whereIn('id', $exerciseIds)->get();
    }
}
