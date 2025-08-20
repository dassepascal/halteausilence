<?php

namespace App\Policies;

use App\Models\User;

class ManageNewsletterPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Détermine si l'utilisateur peut gérer les newsletters.
     */
    public function manage(User $user): bool
    {
        // Autorise les admins et les rédacteurs
        return $user->isAdmin() || $user->hasRole('redac') || $user->can('manage-newsletters');
    }
}
