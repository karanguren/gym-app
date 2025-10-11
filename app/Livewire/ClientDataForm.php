<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; // Para subir foto de perfil
use App\Models\ClientProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // <-- ¡NUEVA IMPORTACIÓN REQUERIDA!

class ClientDataForm extends Component
{
    use WithFileUploads;

    // Propiedad para el layout
    public $layout = 'components.layouts.app'; 

    public $showForm = false;
    public $step = 1;

    // Datos de la tabla 'users'
    public $name;
    public $lastName; 
    public $profile_photo;

    // Datos de la tabla 'client_profiles'
    public $id_number = ''; 
    public $address = '';
    public $personal_number = ''; 
    public $emergency_contact = '';

    public $weight = '';
    public $height = '';
    public $goal = '';

    public function mount()
    {
        $user = Auth::user();
        
        // Redirección si el perfil ya está completo y verificado (Opcional, pero buena práctica)
        if ($user->profile && $user->profile->is_verified) {
             return redirect()->route('dashboard');
        }
        
        $this->name = $user->name ?? '';
        $this->lastName = $user->last_name ?? '';
        
        if ($user->profile) {
            $this->id_number = $user->profile->id_number ?? '';
            $this->address = $user->profile->address ?? '';
            $this->personal_number = $user->profile->personal_number ?? '';
            $this->emergency_contact = $user->profile->emergency_contact ?? '';
            $this->weight = $user->profile->weight ?? '';
            $this->height = $user->profile->height ?? '';
            $this->goal = $user->profile->goal ?? '';
        }
    }

    protected function rules()
    {
        // Obtener el ID del perfil del usuario autenticado para ignorarlo en la comprobación de unicidad
        // Si el perfil no existe, devuelve null y la regla Rule::unique no ignora nada.
        $profileId = Auth::user()->profile->id ?? null;
        
        return [
            1 => [
                'name' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'profile_photo' => 'nullable|image|max:1024',
            ],
            2 => [
                'id_number' => [
                    'required', 
                    'string', 
                    'max:30',
                    // 👇 REGLA DE UNICIDAD MODIFICADA
                    Rule::unique('client_profiles', 'id_number')->ignore($profileId),
                ],
                'address' => 'nullable|string|max:500',
                'personal_number' => 'nullable|string|max:20',
                'emergency_contact' => 'nullable|string|max:50',
            ],
            3 => [
                'weight' => 'nullable|numeric|min:1|max:500', 
                'height' => 'nullable|numeric|min:1|max:300',
                'goal' => 'required|string|in:Ganar Músculo,Pérdida de Peso,Mejorar Resistencia,Mantenimiento',
            ],
        ];
    }

    public function nextStep()
    {
        // Validar solo las reglas del paso actual antes de avanzar
        $this->validate(array_merge(...array_values([$this->rules()[$this->step]])));
        
        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submitData()
    {
        // Validar todas las reglas al final
        $allRules = array_merge(...array_values($this->rules()));
        $this->validate($allRules);

        $user = Auth::user();

        $user->update([
            'name' => $this->name,
            'last_name' => $this->lastName,
        ]);

        $profileData = [
            'last_name' => $this->lastName,
            'id_number' => $this->id_number,
            'address' => $this->address,
            'personal_number' => $this->personal_number,
            'emergency_contact' => $this->emergency_contact,
            'weight' => $this->weight,
            'height' => $this->height,
            'goal' => $this->goal,
        ];

        // Guardar foto de perfil si existe
        if ($this->profile_photo) {
            $profileData['profile_photo_path'] = $this->profile_photo->store('profile_photos', 'public');
        }

        // Esta línea es la que ejecuta el insert/update
        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        session()->flash('success', '¡Datos guardados! Tu perfil ha sido completado y está pendiente de verificación.');
        
        // Redirigir después de guardar (asumiendo que ahora debe ir a la espera de verificación)
        return redirect()->route('verification.pending');
    }

    public function render()
    {
        // El layout con el sidebar se aplicará automáticamente gracias a $this->layout
        return view('livewire.client-data-form');
    }
}