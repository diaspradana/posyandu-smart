<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tapos extends Model
{
    use HasFactory;

    protected $table = 'tapos';

    protected $fillable = [
        'puskesmas_id',
        'nama',
        'kode',
        'alamat',
        'kelurahan',
        'kecamatan',
        'nama_ketua',
        'no_hp',
        'latitude',
        'longitude',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function puskesmas(): BelongsTo
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function balita(): HasMany
    {
        return $this->hasMany(Balita::class);
    }

    public function ibuHamil(): HasMany
    {
        return $this->hasMany(IbuHamil::class);
    }
}