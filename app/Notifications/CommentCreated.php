<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentCreated extends Notification
{
    use Queueable;

    public Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('A comment has been created on your post'))
            ->line(__('A comment has been created on your post') . ' "' . $this->comment->post->title . '" ' . __('by') . ' ' . $this->comment->user->name . '.')
            ->lineIf(!$this->comment->user->valid, __('This comment is awaiting moderation.'))
            ->action(__('Manage this comment'), "#");
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
