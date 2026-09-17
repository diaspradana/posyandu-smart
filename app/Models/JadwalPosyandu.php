<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPosyandu extends Model
{
    use HasFactory;

    protected $table = 'jadwal_posyandu';

    protected $fillable = [
        'tapos_id',
        'nama_kegiatan',
        'jenis_kegiatan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'catatan',
        'status',
        'status_konfirmasi',
        'alasan_perubahan',
        'usulan_tanggal',
        'usulan_waktu_mulai',
        'usulan_waktu_selesai',
        'usulan_lokasi',
        'alasan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'usulan_tanggal' => 'date',
        ];
    }

    public function tapos(): BelongsTo
    {
        return $this->belongsTo(Tapos::class);
    }

    /**
     * Label Status Konfirmasi Human-Readable
     */
    public function getKonfirmasiLabelAttribute(): string
    {
        return match ($this->status_konfirmasi) {
            'siap' => '🟢 Kader siap melaksanakan',
            'usulan_perubahan' => '🟡 Usulan perubahan jadwal',
            'ditolak' => '🔴 Usulan perubahan ditolak',
            default => '⏳ Menunggu konfirmasi kader',
        };
    }

    /**
     * Status Badge CSS Class
     */
    public function getKonfirmasiBadgeClassAttribute(): string
    {
        return match ($this->status_konfirmasi) {
            'siap' => 'badge-success',
            'usulan_perubahan' => 'badge-warning',
            'ditolak' => 'badge-danger',
            default => 'badge-info',
        };
    }
}
