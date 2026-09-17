<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IbuHamil extends Model
{
    use HasFactory;

    protected $table = 'ibu_hamil';

    protected $fillable = [
        'tapos_id',
        'nik',
        'nama',
        'tanggal_lahir',
        'alamat',
        'no_hp',
        'hari_pertama_haid_terakhir',
        'kehamilan_ke',
        'usia_kehamilan_minggu',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'hari_pertama_haid_terakhir' => 'date',
        ];
    }

    public function tapos(): BelongsTo
    {
        return $this->belongsTo(Tapos::class);
    }

    public function pemeriksaan(): HasMany
    {
        return $this->hasMany(PemeriksaanIbuHamil::class);
    }
}