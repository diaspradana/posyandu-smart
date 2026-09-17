<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Puskesmas extends Model
{
    use HasFactory;

    protected $table = 'puskesmas';

    protected $fillable = [
        'nama',
        'alamat',
        'kecamatan',
        'kabupaten',
        'latitude',
        'longitude',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tapos(): HasMany
    {
        return $this->hasMany(Tapos::class);
    }
}