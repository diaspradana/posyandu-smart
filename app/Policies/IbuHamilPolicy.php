<?php

namespace App\Policies;

use App\Models\IbuHamil;
use App\Models\User;

class IbuHamilPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isKader();
    }

    public function view(User $user, IbuHamil $ibuHamil): bool
    {
        if ($user->isAdmin()) {
            if (!$ibuHamil->relationLoaded('tapos') || !$ibuHamil->tapos) {
                $ibuHamil->load('tapos');
            }
            return $ibuHamil->tapos && ((int)$ibuHamil->tapos->puskesmas_id === (int)$user->puskesmas_id);
        }

        if ($user->isKader()) {
            return (int)$ibuHamil->tapos_id === (int)$user->tapos_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isKader();
    }

    public function update(User $user, IbuHamil $ibuHamil): bool
    {
        if ($user->isAdmin()) {
            if (!$ibuHamil->relationLoaded('tapos') || !$ibuHamil->tapos) {
                $ibuHamil->load('tapos');
            }
            return $ibuHamil->tapos && ((int)$ibuHamil->tapos->puskesmas_id === (int)$user->puskesmas_id);
        }

        if ($user->isKader()) {
            return (int)$ibuHamil->tapos_id === (int)$user->tapos_id;
        }

        return false;
    }

    public function delete(User $user, IbuHamil $ibuHamil): bool
    {
        if ($user->isAdmin()) {
            if (!$ibuHamil->relationLoaded('tapos') || !$ibuHamil->tapos) {
                $ibuHamil->load('tapos');
            }
            return $ibuHamil->tapos && ((int)$ibuHamil->tapos->puskesmas_id === (int)$user->puskesmas_id);
        }

        if ($user->isKader()) {
            return (int)$ibuHamil->tapos_id === (int)$user->tapos_id;
        }

        return false;
    }
}
