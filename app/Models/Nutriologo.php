<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nutriologo extends Model
{
    use HasFactory;
    
    // 🎯 IMPORTANTE: user_id debe estar en fillable para que funcione el create()
    protected $fillable = [
        'user_id',
        'license_number',
        'specialty',
        'consultation_fee',
        'bio',
        'is_active',
    ];

    /**
     * Un perfil de Nutriólogo pertenece a un Usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
