<div class="div-principal">
    <div class="max-w mx-auto sm:px-6 lg:px-8">
        <div class="md:p-8">
            

            {{-- Puedes agregar un botón para crear una nueva plantilla --}}
            

            <div class="p-5 flex flex-wrap items-center justify-between gap-2 ">
                <h1 class="titles">Mis Plantillas de Rutina Creadas</h1>
                <div class="btn-outline-lime">
                    <a href="{{ route('employee.routines.create') }}" class="btn-primary" wire:navigate>
                        Crear Nueva Plantilla
                    </a>
                </div>
            </div>
            
            <hr class="mb-6">

            {{-- Usamos la propiedad computada --}}
            @if ($this->routineTemplates->isEmpty())
                <p class="text-gray-500 dark:text-gray-400">Aún no has creado ninguna plantilla de rutina.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($this->routineTemplates as $template)
                        <div class="card-tb-ve-v2">

                            <p class="text-lg font-semibold text-gray-800 dark:text-white mb-2">{{ $template->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Creada:
                                {{ $template->created_at->diffForHumans() }}</p>

                            <div class="mt-4 flex space-x-3">
                                
                                {{-- Botón para editar/usar como base (vinculado al método Livewire) --}}
                                <button wire:click="editTemplate({{ $template->id }})"
                                    class="btn-outline-lime"
                                    wire:loading.attr="disabled">
                                    Usar como Base / Editar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
