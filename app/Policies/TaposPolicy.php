<?php

namespace App\Policies;

use App\Models\Tapos;
use App\Models\User;

class TaposPolicy
{
    /**
     * Determine whether the user can view any tapos.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the specific tapos.
     */
    public function view(User $user, Tapos $tapos): bool
    {
        if ($user->isAdmin()) {
            return (int)$user->puskesmas_id === (int)$tapos->puskesmas_id;
        }

        if ($user->isKader()) {
            return (int)$user->tapos_id === (int)$tapos->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create a tapos (Admin Puskesmas only).
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the tapos (Admin Puskesmas only).
     */
    public function update(User $user, Tapos $tapos): bool
    {
        return $user->isAdmin() && (int)$user->puskesmas_id === (int)$tapos->puskesmas_id;
    }

    /**
     * Determine whether the user can delete the tapos (Admin Puskesmas only).
     */
    public function delete(User $user, Tapos $tapos): bool
    {
        return $user->isAdmin() && (int)$user->puskesmas_id === (int)$tapos->puskesmas_id;
    }
}
