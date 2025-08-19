<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentAnswerCreated extends Notification
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
            ->subject(__('An answer has been created on your comment'))
            ->line(__('An answer has been created on your comment') . ' "' . $this->comment->post->title . '" ' . __('by') . ' ' . $this->comment->user->name . '.')
            ->action(__('Show this comment'), route('posts.show', $this->comment->post->slug));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
