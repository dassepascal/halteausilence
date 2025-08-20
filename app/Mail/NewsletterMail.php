<?php

namespace App\Mail;

use App\Models\Newsletter;
use App\Models\User;
use App\Services\NewsletterService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public Newsletter $newsletter;
    public User $user;
    public string $trackingPixelUrl;
    public string $unsubscribeUrl;

    public function __construct(Newsletter $newsletter, User $user)
    {
        $this->newsletter = $newsletter;
        $this->user = $user;

        $service = new NewsletterService();

        // URLs plus simples qui fonctionnent avec votre setup
        $trackingToken = $service->generateTrackingToken($newsletter, $user);
        $this->trackingPixelUrl = url("/newsletter/{$newsletter->id}/track/open/{$user->id}?token={$trackingToken}");

        $unsubscribeToken = $service->generateUnsubscribeToken($user);
        $this->unsubscribeUrl = url("/newsletter/unsubscribe/{$user->id}?token={$unsubscribeToken}");
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->newsletter->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'newsletter' => $this->newsletter,
                'user' => $this->user,
                'trackingPixelUrl' => $this->trackingPixelUrl,
                'unsubscribeUrl' => $this->unsubscribeUrl,
            ],
        );
    }
}
