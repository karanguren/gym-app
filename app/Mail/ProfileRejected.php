<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProfileRejected extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * La instancia del usuario.
     */
    public $user;

    /**
     * Crea una nueva instancia del mensaje.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Obtiene el sobre (subject, from) del mensaje.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Actualización Importante sobre la Verificación de tu Perfil',
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.profile-rejected', // Apunta al archivo de vista Blade
        );
    }

    /**
     * Obtiene los archivos adjuntos.
     */
    public function attachments(): array
    {
        return [];
    }
}