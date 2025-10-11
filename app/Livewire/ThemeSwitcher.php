<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeSwitcher extends Component
{
    /**
     * Este componente no necesita lógica de backend.
     * Toda la manipulación del tema (clase 'dark' en el <html>)
     * se gestionará en el frontend con JavaScript para persistencia.
     */
    public function render()
    {
        return view('livewire.theme-switcher');
    }
}