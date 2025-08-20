<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Post;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'firstname',
        'email',
        'password',
        'newsletter',
        'role',
        'valid'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'newsletter' => 'boolean',
            'valid' => 'boolean',
        ];
    }
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    public function favoritePosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'favorites');
    }
  // Dans votre fichier app/Models/User.php

public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function isRedac(): bool
{
    return $this->role === 'redac';
}

public function isAdminOrRedac(): bool
{
    return in_array($this->role, ['admin', 'redac']);
}
       public function newsletters(): BelongsToMany
      {
          return $this->belongsToMany(Newsletter::class, 'newsletter_subscribers')
              ->withPivot(['sent_at', 'opened', 'opened_at', 'clicked', 'clicked_at'])
              ->withTimestamps();
      }
    public function createdNewsletters(): HasMany
    {
        return $this->hasMany(Newsletter::class, 'created_by');
    }
    public function newsletterSubscriptions(): HasMany
    {
        return $this->hasMany(NewsletterSubscriber::class);
    }
    // Méthodes utilitaires pour les newsletters
    public function subscribeToNewsletter(): void
    {
        $this->update(['newsletter' => true]);
    }

    public function unsubscribeFromNewsletter(): void
    {
        $this->update(['newsletter' => false]);
    }

    public function isSubscribedToNewsletter(): bool
    {
        return $this->newsletter;
    }

    public function canManageNewsletters(): bool
    {
        return $this->isAdmin() || $this->hasRole('redac') || $this->can('manage-newsletters');
    }

    public function getNewsletterStats(): array
    {
        $subscriptions = $this->newsletterSubscriptions();

        return [
            'total_received' => $subscriptions->count(),
            'total_opened' => $subscriptions->where('opened', true)->count(),
            'total_clicked' => $subscriptions->where('clicked', true)->count(),
            'open_rate' => $subscriptions->count() > 0
                ? ($subscriptions->where('opened', true)->count() / $subscriptions->count()) * 100
                : 0,
            'click_rate' => $subscriptions->count() > 0
                ? ($subscriptions->where('clicked', true)->count() / $subscriptions->count()) * 100
                : 0,
        ];
    }
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // Scope pour les abonnés à la newsletter
    public function scopeNewsletterSubscribers($query)
    {
        return $query->where('newsletter', true);
    }

    // Scope pour les utilisateurs valides
    public function scopeValidUsers($query)
    {
        return $query->where('valid', true);
    }
}
