<?php

namespace App\Mail;

use App\Models\Post;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;


    Public Post $post;

    public function __construct()
    {
        $this->post = Post::firstOrFail();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->post->email, $this->post->name),
            subject: trans('You have been registered'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.registered',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

