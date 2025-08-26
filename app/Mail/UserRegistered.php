<?php

namespace App\Mail;

use App\Models\User; // IMPORTANT : Utiliser le modèle User
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Pour de meilleures performances, il est recommandé de mettre les e-mails en file d'attente
// Implémentez ShouldQueue si votre système de file d'attente est configuré
class UserRegistered extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * L'instance de l'utilisateur.
     * Le rendre public permet à Laravel de le rendre automatiquement disponible dans la vue Blade.
     */
    public User $user;

    /**
     * Crée une nouvelle instance du message.
     * On injecte l'utilisateur qui vient de s'inscrire.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Récupère l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // BONNE PRATIQUE : L'expéditeur doit être une adresse fixe de votre application,
            // configurée dans votre .env (MAIL_FROM_ADDRESS)
            from: new Address(config('mail.from.address'), config('mail.from.name')),

            // On ajoute le destinataire, qui était manquant dans votre code original
            to: [
                new Address($this->user->email, $this->user->name),
            ],

            subject: 'Bienvenue sur notre site !', // Utilisez une chaîne de caractères claire ou une clé de traduction
        );
    }

    /**
     * Récupère la définition du contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.registered', // Assurez-vous que cette vue existe
        );
        
    }

    /**
     * Récupère les pièces jointes du message.
     */
    public function attachments(): array
    {
        return [];
    }
}
