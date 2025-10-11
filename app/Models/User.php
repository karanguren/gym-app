<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne; // Importamos HasOne

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'role', // Ya estaba incluido, ¡perfecto!
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
    
    // ------------------------------------------------------------------
    // RELACIONES
    // ------------------------------------------------------------------
    
    /**
     * Define la relación con el perfil del cliente (ClientProfile).
     * Un usuario tiene un perfil de cliente.
     */
    public function profile(): HasOne
    {
        // Asume que tienes un modelo llamado App\Models\ClientProfile
        return $this->hasOne(ClientProfile::class);
    }
    
    /**
     * Define la relación con el perfil del entrenador (Trainer).
     * Un usuario puede tener un perfil de entrenador.
     */
    public function trainer(): HasOne
    {
        return $this->hasOne(Trainer::class);
    }

    // ------------------------------------------------------------------
    // MÉTODOS DE ROL
    // ------------------------------------------------------------------

    public function isClient(): bool
    {
        return $this->role === 'cliente';
    }

    public function isTrainer(): bool
    {
        // Verifica si el rol es 'trainer' (usado en el controlador de registro)
        return $this->role === 'trainer';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'empleado';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'administrador';
    }


    // ------------------------------------------------------------------
    // MÉTODOS EXISTENTES
    // ------------------------------------------------------------------

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
