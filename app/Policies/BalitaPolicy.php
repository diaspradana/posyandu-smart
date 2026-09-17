<?php

namespace App\Policies;

use App\Models\Balita;
use App\Models\User;

class BalitaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isKader();
    }

    public function view(User $user, Balita $balita): bool
    {
        if ($user->isAdmin()) {
            if (!$balita->relationLoaded('tapos') || !$balita->tapos) {
                $balita->load('tapos');
            }
            return $balita->tapos && ((int)$balita->tapos->puskesmas_id === (int)$user->puskesmas_id);
        }

        if ($user->isKader()) {
            return (int)$balita->tapos_id === (int)$user->tapos_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isKader();
    }

    public function update(User $user, Balita $balita): bool
    {
        if ($user->isAdmin()) {
            if (!$balita->relationLoaded('tapos') || !$balita->tapos) {
                $balita->load('tapos');
            }
            return $balita->tapos && ((int)$balita->tapos->puskesmas_id === (int)$user->puskesmas_id);
        }

        if ($user->isKader()) {
            return (int)$balita->tapos_id === (int)$user->tapos_id;
        }

        return false;
    }

    public function delete(User $user, Balita $balita): bool
    {
        if ($user->isAdmin()) {
            if (!$balita->relationLoaded('tapos') || !$balita->tapos) {
                $balita->load('tapos');
            }
            return $balita->tapos && ((int)$balita->tapos->puskesmas_id === (int)$user->puskesmas_id);
        }

        if ($user->isKader()) {
            return (int)$balita->tapos_id === (int)$user->tapos_id;
        }

        return false;
    }
}
