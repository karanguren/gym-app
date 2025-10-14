<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trainer extends Model
{
    use HasFactory;

    /**
     * Los campos que se pueden asignar masivamente.
     * Sincronizado con la nueva estructura de la tabla.
     * @var array<int, string>
     */
   protected $fillable = [
        'user_id',
        'id_number', 
        'address', 
        'personal_contact', 
        'emergency_contact', 
        'personal_description',
        'work_experience',
        'certification_paths',
        'certification_id',
        'specialty',
        'hourly_rate',
        'is_active',
    ];

    /**
     * Los campos que deben ser casteados.
     * @var array<string, string>
     */
    protected $casts = [
        'certification_paths' => 'array', // CRUCIAL para guardar y leer JSON
        'is_active' => 'boolean',
    ];

    /**
     * Define la relación inversa con el usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
