<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
    @include('layouts.notifications') 
    <livewire:confirm-modal />
</x-layouts.app.sidebar>
