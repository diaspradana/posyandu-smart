<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Balita extends Model
{
    use HasFactory;

    protected $table = 'balita';

    protected $fillable = [
        'tapos_id',
        'nik',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'nama_ibu',
        'nama_ayah',
        'no_hp_orang_tua',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function tapos(): BelongsTo
    {
        return $this->belongsTo(Tapos::class);
    }

    public function pemeriksaan(): HasMany
    {
        return $this->hasMany(PemeriksaanBalita::class);
    }

    /**
     * Format usia dalam format: X Th Y bln Z hr
     */
    public function getUsiaLengkapAttribute(): string
    {
        if (!$this->tanggal_lahir) {
            return '-';
        }

        $birth = \Carbon\Carbon::parse($this->tanggal_lahir);
        $diff = $birth->diff(now());

        return "{$diff->y} Th {$diff->m} bln {$diff->d} hr";
    }

    /**
     * Format usia dan jenis kelamin: X Th Y bln Z hr (JK)
     * Contoh: 3 Th 11 bln 12 hr (L)
     */
    public function getUmurJkAttribute(): string
    {
        if (!$this->tanggal_lahir) {
            return $this->jenis_kelamin ? "({$this->jenis_kelamin})" : '-';
        }

        $usia = $this->usia_lengkap;
        $jk = $this->jenis_kelamin ? "({$this->jenis_kelamin})" : '';

        return trim("{$usia} {$jk}");
    }

    /**
     * Usia dalam bulan bulat (integer) untuk perhitungan kurva & AI
     */
    public function getUsiaBulanAttribute(): int
    {
        if (!$this->tanggal_lahir) {
            return 0;
        }

        $birth = \Carbon\Carbon::parse($this->tanggal_lahir);
        $diff = $birth->diff(now());

        return ($diff->y * 12) + $diff->m;
    }

    /**
     * Static helper untuk format usia/jk dari tanggal lahir
     */
    public static function formatUmurJk($tanggalLahir, $jenisKelamin = null): string
    {
        if (!$tanggalLahir) {
            return $jenisKelamin ? "({$jenisKelamin})" : '-';
        }

        $birth = is_string($tanggalLahir) ? \Carbon\Carbon::parse($tanggalLahir) : $tanggalLahir;
        $diff = $birth->diff(now());
        $str = "{$diff->y} Th {$diff->m} bln {$diff->d} hr";

        if ($jenisKelamin) {
            $str .= " ({$jenisKelamin})";
        }

        return $str;
    }
}