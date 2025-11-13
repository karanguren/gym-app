<div>
    @props(['showModal', 'exercise'])

    @if ($exercise)
        {{-- Overlay del Modal (Controlado por Livewire) --}}
        <div x-data="{ open: @entangle($showModal) }" x-show="open" x-transition.opacity.scale.80 x-cloak class="bg-modal"
            aria-labelledby="modal-title-exercise" role="dialog" aria-modal="true">

            {{-- Contenido del Modal --}}
            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                class="relative z-50 w-full max-w-2xl bg-gray-900 rounded-xl shadow-2xl p-6 md:p-8 transform transition-all modal-instrucciones">

                {{-- Encabezado y Botón de Cierre --}}
                <div class="border-b border-gray-700 pb-4 mb-4 flex items-center justify-between">
                    <h3 class="titles">
                        <span class="text-lime-400">{{ $exercise->name }}</span>
                    </h3>
                    {{-- Usamos wire:click para asegurar que la variable de Livewire se actualice --}}
                    <button wire:click.prevent="{{ $showModal }} = false"
                        class="text-gray-400 hover:text-lime-400 transition duration-200 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- CONTENIDO SCROLLABLE --}}
                <div class="mt-4 space-y-6 max-h-[65vh] overflow-y-auto pr-3 -mr-2 custom-scrollbar">

                    {{-- GIF / Video --}}
                    @if ($exercise->gif_path ?? false)
                        <div class="w-full relative pb-[100%] overflow-hidden rounded-xl bg-white flex items-center justify-center shadow-xl border-2 border-lime-600">
                            <img src="{{ $exercise->gif_path }}" alt="GIF de {{ $exercise->name }}" class="absolute inset-0 w-full h-full object-contain">
                        </div>
                    @endif

                    {{-- Grupo Muscular --}}
                    <p class="text-sm font-semibold text-lime-400 capitalize">
                        Grupo Muscular:
                        <span class="font-normal ">{{ $exercise->muscle_group }}</span>
                    </p>

                    {{-- SECCIÓN PREPARACIÓN (Resumen) --}}
                    <div class="border-t border-gray-700 pt-5">
                        <h4 class="text-xl font-bold mb-2">Preparación (Resumen)</h4>
                        <p class="text-sm leading-relaxed">
                            {{ $exercise->description }}
                        </p>
                    </div>

                    {{-- SECCIÓN EJECUCIÓN (Pasos) --}}
                    <div class="border-t border-gray-700 pt-5">
                        <h4 class="text-xl font-bold mb-2">Ejecución</h4>
                        {{-- Si las instrucciones son HTML/Markdown, usa @if ($exercise->instructions) {!! nl2br(e($exercise->instructions)) !!} @endif --}}
                        <p class="text-sm leading-relaxed">
                            {{ $exercise->instructions }}
                        </p>
                    </div>

                    {{-- SECCIÓN CONSEJOS CLAVES (Tips) --}}
                    <div class="border-t border-gray-700 pt-5">
                        <h4 class="text-xl font-bold mb-2">Consejos Claves</h4>
                        <p class="text-sm leading-relaxed">
                            {{ $exercise->tips }}
                        </p>
                    </div>

                </div>
                {{-- FIN: CONTENIDO SCROLLABLE --}}

                <div class="mt-8 pt-5 border-t border-gray-700 flex justify-end">
                    {{-- Usamos wire:click para cerrar el modal desde Livewire --}}
                    <button wire:click.prevent="{{ $showModal }} = false" type="button" class="btn-outline-lime">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
