<div class="max-w-3xl mx-auto p-6">

    @if(!$showForm)
        <div class="h-screen flex items-center justify-center bg-transparent">
            <!-- CARD INICIAL -->
            <div class="bg-white/90 dark:bg-[#1a1a1a]/95 shadow-xl sm:rounded-lg p-8 text-center border-2 !border-[#7bcb01] dark:border-[#7bcb01]">
                <h2 class="text-2xl font-bold text-[#7bcb01] dark:text-[#7bcb01] mb-4">
                    Completa tu perfil
                </h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Debes completar tus datos para verificar tu cuenta y acceder a todos los beneficios.
                </p>
                <button wire:click="$set('showForm', true)"
                    class="px-6 py-3 bg-[#7bcb01] text-white rounded-lg font-semibold hover:bg-[#7bcb01]/80 transition">
                    Comenzar
                </button>
            </div>
        </div>
    @else
        <!-- MODAL / FORMULARIO POR PASOS -->
        <div class="bg-white/80 dark:bg-[#1a1a1a]/95 shadow-xl sm:rounded-lg p-6 space-y-6">
            <h2 class="text-2xl font-bold text-white dark:text-[#7bcb01] mb-4">
                Paso {{ $step }} de 3
            </h2>

            @if (session()->has('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                    <p class="font-bold">¡Guardado!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif  

            <form wire:submit.prevent="submitData" class="space-y-4">
                {{-- PASO 1 --}}
                @if($step === 1)
                    <div class="space-y-4">
                        <!-- Nombre -->
                        <div>
                            <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nombre
                            </flux:label>
                            <flux:input
                                wire:model="name"
                                placeholder="Nombre y Apellido"
                                type="text"
                                required
                                class="mt-2 block w-full border border-[#7bcb01] rounded-lg shadow-sm focus:border-[#7bcb01] focus:ring-[#7bcb01] dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                            />
                            @error('name') 
                                <span class="text-xs text-red-500">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Apellido -->
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

                        <!-- Foto de perfil -->
                        <div>
                            <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Foto de perfil (opcional)
                            </flux:label>
                            <flux:input
                                wire:model="profile_photo"
                                type="file"
                                class="mt-2 block w-full border border-[#7bcb01] rounded-lg shadow-sm focus:border-[#7bcb01] focus:ring-[#7bcb01] dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                            />
                            @error('profile_photo') 
                                <span class="text-xs text-red-500">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- PASO 2 --}}
                @if($step === 2)
                    <div class="space-y-4">
                        <!-- Cédula -->
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

                        <!-- Dirección -->
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

                        <!-- Número personal -->
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

                        <!-- Contacto de emergencia -->
                        <div>
                            <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Contacto de emergencia
                            </flux:label>
                            <flux:input
                                wire:model="emergency_contact"
                                type="text"
                                placeholder="0404-1234567 Pedro Perez"
                                class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm 
                                    focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                    dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                            />
                            @error('emergency_contact')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                {{-- PASO 3 --}}
                @if($step === 3)
                    <div class="space-y-4">
                        <!-- Peso -->
                        <div>
                            <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Peso (kg)
                            </flux:label>
                            <flux:input
                                wire:model="weight"
                                type="number"
                                step="0.1"
                                placeholder="Ej: 70.5"
                                class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm
                                    focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                    dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                            />
                            @error('weight')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Estatura -->
                        <div>
                            <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Estatura (cm)
                            </flux:label>
                            <flux:input
                                wire:model="height"
                                type="number"
                                placeholder="Ej: 170"
                                class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm
                                    focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                    dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                            />
                            @error('height')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Objetivo Fitness -->
                        <div>
                            <flux:label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Objetivo Fitness
                            </flux:label>
                            <flux:select
                                wire:model="goal"
                                class="mt-2 block w-full rounded-lg border border-[#7bcb01] shadow-sm
                                    focus:border-[#7bcb01] focus:ring-[#7bcb01]
                                    dark:bg-[#1a1a1a]/95 dark:border-[#7bcb01] dark:text-white"
                            >
                                <option value="">Selecciona tu objetivo</option>
                                <option value="Perder Peso">Perder Peso</option>
                                <option value="Ganar Músculo">Ganar Músculo</option>
                                <option value="Mejorar Resistencia">Mejorar Resistencia</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                            </flux:select>
                            @error('goal')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                <!-- BOTONES -->
                <div class="flex justify-between mt-6">
                    @if($step > 1)
                        <button type="button" wire:click="prevStep"
                            class="px-4 py-2 border border-[#7bcb01] bg-gray-300 dark:bg-gray-700 rounded-lg hover:bg-gray-400 transition">
                            Atrás
                        </button>
                    @endif

                    @if($step < 3)
                        <button type="button" wire:click="nextStep"
                            class="px-6 py-3 bg-[#7bcb01] text-white rounded-lg font-semibold hover:bg-[#7bcb01]/80 transition">
                            Siguiente
                        </button>
                    @else
                        <button type="submit" 
                            class="px-6 py-3 bg-[#7bcb01] text-white rounded-lg font-semibold hover:bg-[#7bcb01]/80 transition" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-wait" wire:target="submitData">
                            <span wire:loading.remove wire:target="submitData">
                                Guardar
                            </span>

                            <span wire:loading wire:target="submitData">
                                Registrando...
                            </span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    @endif
</div>
