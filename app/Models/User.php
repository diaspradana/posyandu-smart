<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'puskesmas_id',
        'tapos_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function puskesmas(): BelongsTo
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function tapos(): BelongsTo
    {
        return $this->belongsTo(Tapos::class);
    }

    public function getActiveTapos(): ?Tapos
    {
        if ($this->tapos_id && $this->tapos) {
            return $this->tapos;
        }

        if ($this->puskesmas_id) {
            return Tapos::where('puskesmas_id', $this->puskesmas_id)->first();
        }

        return Tapos::first();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKader(): bool
    {
        return $this->role === 'kader';
    }
}