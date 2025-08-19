<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsletterCategory extends Model
{
     use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function newsletters(): BelongsToMany
    {
        return $this->belongsToMany(Newsletter::class, 'newsletter_category_pivot')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

