<?php

// app/Models/Newsletter.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subject',
        'content',
        'status',
        'scheduled_at',
        'sent_at',
        'created_by',
        'sent_count',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'newsletter_subscribers')
            ->withPivot(['sent_at', 'opened', 'opened_at', 'clicked', 'clicked_at'])
            ->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(NewsletterCategory::class, 'newsletter_category_pivot')
            ->withTimestamps();
    }

    public function subscriberRecords(): HasMany
    {
        return $this->hasMany(NewsletterSubscriber::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    // Méthodes utilitaires
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function getOpenRate(): float
    {
        $totalSent = $this->subscriberRecords()->count();
        $totalOpened = $this->subscriberRecords()->where('opened', true)->count();

        return $totalSent > 0 ? ($totalOpened / $totalSent) * 100 : 0;
    }

    public function getClickRate(): float
    {
        $totalSent = $this->subscriberRecords()->count();
        $totalClicked = $this->subscriberRecords()->where('clicked', true)->count();

        return $totalSent > 0 ? ($totalClicked / $totalSent) * 100 : 0;
    }
}
