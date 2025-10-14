<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\Trainer;
use App\Models\Nutriologo; // Asegúrate de que este modelo exista
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;

/**
 * Componente Livewire para la configuración del perfil inicial de Empleados (Entrenadores/Nutriólogos).
 * Este formulario es visible solo si el usuario no tiene un perfil profesional asociado.
 */
class EmployeeProfileSetupForm extends Component
{
    use WithFileUploads;

    // Propiedades del empleado
    public $user; // El usuario autenticado
    public string $employeeRole = ''; // Rol del usuario (trainer o nutriologo)

    // Paso actual del formulario (1: Contacto, 2: Perfil Público y Rol, 3: Archivos)
    public int $step = 1;

    // --- PROPIEDADES DEL FORMULARIO ---

    // Paso 1: Datos Personales y Contacto (Campos adicionales para la tabla 'users' o una tabla relacionada)
    public $name = '';
    public $lastName = ''; 
    public $id_number = ''; // Cédula
    public $address = ''; // Dirección
    public $personal_number = ''; // Número personal
    public $emergency_contact = ''; // Contacto de emergencia

    // Paso 2: Perfil Público y Rol
    public $specialty = ''; // Área de especialidad (Ej: Crossfit, Dieta Keto)
    public $personal_description = ''; // Biografía profesional (correspondiente a 'bio' en DB)
    public $work_experience = ''; // Descripción de experiencia laboral
    
    // Paso 2: Campos Específicos del Rol
    public $certification_id = ''; // Trainer: Certificación
    public $hourly_rate = null; // Trainer: Tarifa por hora
    public $license_number = ''; // Nutriólogo: Número de Licencia
    public $consultation_fee = null; // Nutriólogo: Tarifa por consulta

    // Paso 3: Archivos Adjuntos
    public $certifications = []; // Array de archivos subidos (Requiere WithFileUploads)


    /**
     * Define las reglas de validación para el paso actual.
     */
    protected function rules()
    {
        return $this->rulesForStep($this->step);
    }

    /**
     * Define las reglas de validación para un paso específico.
     * Se han ajustado para coincidir con los campos de tu vista HTML original.
     */
    protected function rulesForStep(int $targetStep): array
    {
        // Paso 1: Información Personal de Contacto (coincide con los campos de la vista)
        if ($targetStep === 1) {
            return [
                'name' => ['required', 'string', 'max:255'],
                'lastName' => ['required', 'string', 'max:255'],
                'id_number' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:500'],
                'personal_number' => ['required', 'string', 'max:20'],
                'emergency_contact' => ['nullable', 'string', 'max:255'],
            ];
        }

        // Paso 2: Perfil Público y Campos Específicos del Rol
        if ($targetStep === 2) {
            $rules = [
                // Campos públicos
                'specialty' => ['required', 'string', 'max:255'],
                'personal_description' => ['required', 'string', 'max:1000'], // Corresponde al campo 'bio' en la DB
                'work_experience' => ['nullable', 'string', 'max:2000'],

                // Placeholder para campos rol-específicos (serán validados si el rol aplica)
                // 'certification_id' => ['nullable', 'string', 'max:50'],
                // 'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
                // 'license_number' => ['nullable', 'string', 'max:50'],
                // 'consultation_fee' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            ];

            // Requerir y aplicar reglas únicas según el rol
            // if ($this->employeeRole === 'trainer') {
            //     $rules['certification_id'] = array_merge(['required'], $rules['certification_id'], [Rule::unique('trainers', 'certification_id')]);
            //     $rules['hourly_rate'] = array_merge(['required'], $rules['hourly_rate']);
            // } elseif ($this->employeeRole === 'nutriologo') {
            //     $rules['license_number'] = array_merge(['required'], $rules['license_number'], [Rule::unique('nutriologos', 'license_number')]);
            //     $rules['consultation_fee'] = array_merge(['required'], $rules['consultation_fee']);
            // }
            return $rules;
        }

        // Paso 3 (Archivos)
        if ($targetStep === 3) {
            return [
                'certifications.*' => 'nullable|file|mimes:jpg,png,pdf|max:5120', // Límite de 5MB
            ];
        }
        
        return [];
    }

    /**
     * Se ejecuta al cargar el componente.
     */
    public function mount()
    {
        $this->user = Auth::user();
        
        if (!$this->user) {
            return redirect()->route('login');
        }

        $role = $this->user->role; 
        if ($role === 'trainer' || $role === 'nutriologo') {
            $this->employeeRole = $role;
        } else {
            // Redirigir si no es un empleado o un rol reconocido
            Auth::logout();
            return redirect()->route('login');
        }

        // Si el perfil ya existe, redirigir al dashboard
        if (method_exists($this->user, 'hasEmployeeProfile') && $this->user->hasEmployeeProfile()) {
            return Redirect::route('employee.dashboard');
        }
        
        // Cargar datos existentes del usuario (nombre, apellido, y otros si se guardan en el modelo User)
        $this->name = $this->user->name ?? '';
        $this->lastName = $this->user->last_name ?? ''; 
        // Si tienes campos de contacto en tu tabla 'users' o una relación one-to-one, cárgalos aquí.
        // Ej: $this->personal_number = $this->user->personal_number ?? ''; 
    }

    /**
     * Valida el paso actual y avanza al siguiente.
     */
    public function nextStep()
    {
        // Validar solo los campos del paso actual antes de avanzar
        $this->validate();

        if ($this->step < 3) {
            $this->step++;
        }
    }

    /**
     * Retrocede un paso.
     */
    public function prevStep() // Cambiado a prevStep para coincidir con la vista
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }


    /**
     * Maneja el envío del formulario para crear el perfil del empleado (solo en el paso final).
     */
    public function saveProfile()
    {
        // 1. Validar todos los pasos (1, 2 y 3) antes de guardar.
        $rulesStep1 = $this->rulesForStep(1);
        $rulesStep2 = $this->rulesForStep(2);
        $rulesStep3 = $this->rulesForStep(3); // Validación de archivos

        $allRules = array_merge($rulesStep1, $rulesStep2, $rulesStep3);
        
        // Ejecutar validación final
        $validatedData = $this->validate($allRules);

        try {
            // 2. Actualizar la tabla 'users' con los datos de contacto
            $this->user->update([
                'name' => $this->name,
                'last_name' => $this->lastName,
                // Asumo que estos campos de contacto existen en tu modelo User
                
                // Puedes optar por guardar el emergency_contact en el User o en el perfil específico
            ]);

            // 3. Simulación de subida de archivos (necesitas lógica de almacenamiento real aquí)
            $filePaths = [];
            if (!empty($this->certifications)) {
                // Iterar sobre los archivos y guardarlos en el disco 'public' bajo 'certifications/'
                foreach ($this->certifications as $file) {
                    $path = $file->store('certifications');
                    $filePaths[] = $path;
                }
            }

            // 4. Crear el perfil específico (Trainer o Nutriólogo)
            if ($this->employeeRole === 'trainer') {
                Trainer::create([
                    'user_id' => $this->user->id,
                    'id_number' => $this->id_number, 
                    'address' => $this->address,
                    'personal_contact' => $this->personal_number, 
                    'certification_id' => $this->certification_id,
                    'specialty' => $this->specialty,
                    'hourly_rate' => $this->hourly_rate,
                    'personal_description' => $this->personal_description, // Usamos personal_description para el campo 'bio'
                    'work_experience' => $this->work_experience,
                    'emergency_contact' => $this->emergency_contact,
                    'certifications_path' => json_encode($filePaths), // Guarda las rutas de los archivos
                    'is_approved' => false, // Cambiado a false para que sea enviado a revisión
                ]);
            } elseif ($this->employeeRole === 'nutriologo') {
                Nutriologo::create([
                    'user_id' => $this->user->id,
                    'id_number' => $this->id_number, 
                    'address' => $this->address,
                    'personal_contact' => $this->personal_number, 
                    'license_number' => $this->license_number,
                    'specialty' => $this->specialty,
                    'consultation_fee' => $this->consultation_fee,
                    'personal_description' => $this->personal_description, // Usamos personal_description para el campo 'bio'
                    'work_experience' => $this->work_experience,
                    'emergency_contact' => $this->emergency_contact,
                    'certifications_path' => json_encode($filePaths), // Guarda las rutas de los archivos
                    'is_approved' => false, // Cambiado a false para que sea enviado a revisión
                ]);
            } else {
                session()->flash('error', 'Rol de empleado no reconocido. No se pudo guardar el perfil.');
                return;
            }

            // 5. Éxito: Redirigir al dashboard
            session()->flash('success', '¡Perfil profesional enviado a revisión exitosamente! Te notificaremos cuando sea aprobado.');
            return Redirect::route('employee.dashboard');

        } catch (\Exception $e) {
            // Manejo de errores de base de datos o validación que se escape.
            session()->flash('error', 'Hubo un error al guardar el perfil. Intenta de nuevo. Detalles: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employee.employee-profile-setup-form')
            ->layout('components.layouts.app', ['title' => 'Completar Perfil de Empleado']);
    }
}
