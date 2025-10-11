<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'user_id', 
        'goal',
        'last_name',
        'weight',
        'height',
        'personal_number',
        'birth_date',
        'medical_history',
        'profile_photo_path',
        'emergency_contact',
        'address',
        'id_number',

        // Asegúrate de que todos los campos que guardas en el formulario estén aquí.
    ];

    /**
     * Define la relación inversa con el usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}