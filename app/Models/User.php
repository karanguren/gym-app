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
        'role',
        'is_active',
        'client_type',
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
    
    public function profile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }
    
    public function trainer(): HasOne
    {
        return $this->hasOne(Trainer::class);
    }

    public function nutriologo(): HasOne 
    {
        return $this->hasOne(Nutriologo::class);
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
        return $this->role === 'trainer';
    }

    public function isNutriologo(): bool 
    {
        return $this->role === 'nutriologo'; 
    }

    public function isAdmin(): bool
    {
        return $this->role === 'administrador';
    }

    public function isEmployee(): bool
    {
        return $this->isTrainer() || $this->isNutriologo();
    }


    public function isStaff(): bool
    {
        return $this->isAdmin() || $this->isTrainer() || $this->isNutriologo();
    }

    public function isRegularClient(): bool
    {
        return $this->isClient() && $this->client_type === 'regular';
    }

    public function isPersonalizedClient(): bool
    {
        return $this->isClient() && $this->client_type === 'personalized';
    }

    
    // ------------------------------------------------------------------
    // MÉTODOS DE PERFIL (CLAVE PARA EL FLUJO)
    // ------------------------------------------------------------------
    
    /**
     * Verifica si el empleado (Trainer o Nutriólogo) tiene un perfil asociado.
     * @return bool
     */
    public function hasEmployeeProfile(): bool
    {
        if ($this->isTrainer()) {
            return $this->trainer()->exists();
        }

        if ($this->isNutriologo()) {
            return $this->nutriologo()->exists();
        }

        // Si no es un rol de empleado, asumimos que no tiene un perfil de empleado
        return false;
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
