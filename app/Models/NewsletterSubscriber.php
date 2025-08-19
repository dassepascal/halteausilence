<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
     protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'newsletter_id',
        'user_id',
        'sent_at',
        'opened',
        'opened_at',
        'clicked',
        'clicked_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened' => 'boolean',
        'opened_at' => 'datetime',
        'clicked' => 'boolean',
        'clicked_at' => 'datetime',
    ];

    public function newsletter()
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsOpened(): void
    {
        if (!$this->opened) {
            $this->update([
                'opened' => true,
                'opened_at' => now(),
            ]);
        }
    }

    public function markAsClicked(): void
    {
        if (!$this->clicked) {
            $this->update([
                'clicked' => true,
                'clicked_at' => now(),
            ]);
        }
    }
}
