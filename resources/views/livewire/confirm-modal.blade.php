<div>
    @if ($show)
        <div class="bg-modal" wire:click.self="executeCancelAction" x-init="$el.style.opacity = '1'" x-cloak>

            <div class="max-w-sm w-full z-10 modal-card">

                <div class="flex justify-center mb-4">
                    @if ($confirmButtonClass == 'btn-outline-red')
                        <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.332 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    @else
                        <svg class="w-10 h-10 text-[#7bcb01]" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </div>

                <h3
                    class="text-xl font-bold text-center {{ $confirmButtonClass == 'btn-outline-red' ? 'text-red-600 dark:text-red-400' : 'text-[#7bcb01]' }} mb-2">
                    {{ $title }}
                </h3>

                <p class="modal-message">
                    {{ $message }}
                </p>

                <div class="flex justify-center space-x-3">
                    <button wire:click.prevent="executeCancelAction" type="button" class="{{ $buttonClass }}">
                        Cancelar
                    </button>

                    <button wire:click.prevent="executeConfirmAction" wire:loading.attr="disabled" type="button"
                        class="{{ $confirmButtonClass }}">
                        {{ $confirmButtonText }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
