<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanBalita extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_balita';

    protected $fillable = [
        'balita_id',
        'tanggal_pemeriksaan',
        'umur_bulan',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'status_stunting',
        'hasil_ai',
        'ai_probability',
        'ai_probabilities',
        'ai_model_version',
        'status_imunisasi',
        'status_pemeriksaan',
        'status_validasi',
        'catatan_validasi',
        'catatan',
        'kehadiran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pemeriksaan' => 'date',
            'umur_bulan' => 'integer',
            'berat_badan' => 'decimal:2',
            'tinggi_badan' => 'decimal:2',
            'lingkar_kepala' => 'decimal:2',
            'ai_probability' => 'decimal:4',
            'ai_probabilities' => 'array',
            'kehadiran' => 'boolean',
        ];
    }

    public function balita(): BelongsTo
    {
        return $this->belongsTo(Balita::class);
    }

    /**
     * Badge visual status AI Stunting Screening
     */
    public function getAiBadgeAttribute(): string
    {
        $status = strtolower($this->hasil_ai ?? $this->status_stunting ?? 'normal');
        return match ($status) {
            'risiko_stunting', 'stunting' => '<span class="status-pill danger">🔴 Risiko Stunting</span>',
            'pemantauan' => '<span class="status-pill warning">🟡 Pemantauan</span>',
            'belum_diperiksa' => '<span class="status-pill secondary">⚪ Belum Diperiksa</span>',
            default => '<span class="status-pill success">🟢 Normal</span>',
        };
    }

    /**
     * Badge validasi Admin Puskesmas
     */
    public function getStatusValidasiBadgeAttribute(): string
    {
        return match ($this->status_validasi) {
            'validated' => '<span class="status-pill success">✓ Tervalidasi</span>',
            'rejected' => '<span class="status-pill danger">✗ Ditolak</span>',
            default => '<span class="status-pill warning">⏳ Menunggu Validasi</span>',
        };
    }
}
