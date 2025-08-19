<?php

namespace App\Models;

use App\Models\Submenu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'label',
        'link',
        'order',
    ];

    public function submenus(): HasMany
    {
        return $this->hasMany(Submenu::class);
    }
}
