<div class="max-w-4xl mx-auto p-6 lg:p-8">
<div class="bg-white dark:bg-gray-800 shadow-2xl sm:rounded-xl p-8 border-t-4 border-indigo-500 dark:border-indigo-700">

    <header class="mb-8 text-center">
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-2">
            Configura tu Perfil Profesional
        </h1>
        <p class="text-indigo-600 dark:text-indigo-400 text-lg font-semibold">
            {{ $employeeRole === 'trainer' ? 'Entrenador Personal' : 'Nutriólogo Profesional' }}
        </p>
        <p class="text-gray-500 dark:text-gray-400 mt-2">
            Paso {{ $step }} de 3: Completa estos datos para activar tu acceso.
        </p>
        
        <!-- Indicador de pasos (Opcional, pero útil) -->
        <div class="mt-4 flex justify-center space-x-2">
            <div class="w-8 h-1 rounded-full {{ $step >= 1 ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
            <div class="w-8 h-1 rounded-full {{ $step >= 2 ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
            <div class="w-8 h-1 rounded-full {{ $step >= 3 ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-600' }}"></div>
        </div>
    </header>

    <!-- Mensajes de Sesión -->
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-900 dark:text-red-400" role="alert">
            {{ session('error') }}
        </div>
    @elseif (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-900 dark:text-green-400" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="saveProfile" class="space-y-8">

        <!-- PASO 1: DATOS PERSONALES Y CONTACTO -->
        @if ($step === 1)
            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 border-indigo-200 dark:border-indigo-600">
                    1. Información Personal de Contacto
                </h2>

                <!-- Nombre y Apellido (Asumo que están en el modelo User) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nombre
                        </flux:label>
                        <flux:input
                            wire:model="name"
                            placeholder="Nombre"
                            type="text"
                            required
                            class="mt-2 block w-full border border-[#7bcb01] rounded-lg shadow-sm focus:border-[#7bcb01] focus:ring-[#7bcb01] dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                        />
                        @error('name') 
                            <span class="text-xs text-red-500">{{ $message }}</span> 
                        @enderror
                    </div>
                    <div>
                        <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Apellido
                        </flux:label>
                        <flux:input
                            wire:model="lastName"
                            placeholder="Apellido"
                            type="text"
                            required
                            class="mt-2 block w-full border border-[#7bcb01] rounded-lg shadow-sm focus:border-[#7bcb01] focus:ring-[#7bcb01] dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                        />
                        @error('lastName') 
                            <span class="text-xs text-red-500">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <!-- Cédula y Dirección -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Cédula
                        </flux:label>
                        <flux:input
                            wire:model="id_number"
                            type="text"
                            placeholder="12345678"
                            class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm 
                                focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                        />
                        @error('id_number')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Dirección
                        </flux:label>
                        <flux:input
                            wire:model="address"
                            type="text"
                            placeholder="Dirección completa"
                            class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm 
                                focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                        />
                        @error('address')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Contacto Personal y de Emergencia -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Número personal
                        </flux:label>
                        <flux:input
                            wire:model="personal_number"
                            type="phone"
                            mask="(9999)999-9999"
                            placeholder="(9999)999-9999"
                            class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm 
                                focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                        />
                        @error('personal_number')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Contacto de Emergencia (Nombre y Teléfono)
                        </flux:label>
                        <flux:input
                            wire:model="emergency_contact"
                            type="text"
                            placeholder="Ej: Juan Pérez - 0404-1234567"
                            class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm 
                                focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                        />
                        @error('emergency_contact')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        @endif

        <!-- PASO 2: PERFIL PÚBLICO -->
        @if ($step === 2)
            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 border-indigo-200 dark:border-indigo-600">
                    2. Perfil Público y Experiencia
                </h2>
                
                <!-- Área de Especialidad -->
                <div class="mb-4">
                    <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Área de Especialidad (Ej: Crossfit, Dieta Keto, Entrenamiento Funcional)
                    </flux:label>
                    <flux:input
                            wire:model="specialty"
                            type="text"
                            placeholder="Describe brevemente tu especialización"
                            required
                            class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm 
                                focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                        />
                    @error('specialty') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Descripción Personal/Biografía -->
                <div class="mb-4">
                    <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Descripción Personal / Filosofía de Trabajo (Máx. 1000 caracteres)
                    </flux:label>
                    <flux:textarea 
                        wire:model="personal_description"
                        rows="auto"
                        required
                        class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm 
                                focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                        placeholder="Cuéntanos sobre tu experiencia, filosofía y logros."
                    />
                    @error('personal_description') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Descripción de Experiencia Laboral -->
                <div>
                    <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Descripción de Experiencia Laboral
                    </flux:label>
                    <flux:textarea 
                        wire:model="work_experience"
                        rows="auto"
                        class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm 
                                focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                        placeholder="Detalla tu experiencia relevante (puedes usar listas o párrafos)."
                    />
                    @error('work_experience') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                
            </div>
        @endif

        <!-- PASO 3: ARCHIVOS Y FINALIZACIÓN -->
        @if ($step === 3)
            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 border-indigo-200 dark:border-indigo-600">
                    3. Archivos Adjuntos (Certificaciones)
                </h2>
                
                <!-- Certificaciones Escaneadas (Múltiples archivos) -->
                <div>
                    <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Certificaciones Escaneadas (Imágenes o PDF, múltiples archivos)
                    </flux:label>
                    <input type="file" id="certifications" wire:model="certifications" multiple
                        class="block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Archivos permitidos: JPG, PNG, PDF. Máx 5MB por archivo.</p>
                    @error('certifications.*') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
                    @error('certifications') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror

                    <!-- Indicador de carga de archivos -->
                    <div wire:loading wire:target="certifications" class="mt-2 text-sm text-indigo-500 dark:text-indigo-400">
                        Cargando archivos...
                    </div>
                </div>

                <div class="mt-6 p-4 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                    <p class="text-sm text-indigo-700 dark:text-indigo-300 font-semibold">
                        Al presionar "Guardar", tu perfil será enviado para revisión. Te notificaremos cuando sea aprobado.
                    </p>
                </div>
            </div>
        @endif

        <!-- CONTROLES DE NAVEGACIÓN -->
        <div class="pt-4 flex justify-between">
            <!-- Botón Atrás -->
            @if ($step > 1)
                <button type="button" wire:click.prevent="prevStep"
                    class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                    &larr; Atrás
                </button>
            @else
                <!-- Placeholder para mantener la alineación -->
                <div></div>
            @endif
            
            <!-- Botón Siguiente / Guardar -->
            @if ($step < 3)
                <button type="button" wire:click.prevent="nextStep"
                    class="px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Siguiente &rarr;
                </button>
            @else
                <button type="submit" 
                    class="px-6 py-3 bg-[#7bcb01] text-white rounded-lg font-semibold hover:bg-[#7bcb01]/80 transition"
                    wire:loading.attr="disabled" wire:target="saveProfile, certifications">
                    <span wire:loading.remove wire:target="saveProfile, certifications">
                        Guardar Perfil y Enviar
                    </span>
                    <span wire:loading wire:target="saveProfile, certifications">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Guardando...
                    </span>
                </button>
            @endif
        </div>
    </form>
</div>

</div>