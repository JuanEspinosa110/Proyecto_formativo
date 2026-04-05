<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevoUsuarioCreado extends Mailable
{
    use Queueable, SerializesModels;

    public $nombre;
    public $documento;
    public $password;
    public $nit;

    /**
     * Create a new message instance.
     */
    public function __construct($nombre, $documento, $password, $nit)
    {
        $this->nombre = $nombre;
        $this->documento = $documento;
        $this->password = $password;
        $this->nit = $nit;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido a SIGU! Tus credenciales de acceso'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.usuarios.nuevo_usuario',
            with: [
                'nombre' => $this->nombre,
                'documento' => $this->documento,
                'password' => $this->password,
                'nit' => $this->nit,
                'url_login' => config('app.url') . '/login',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
