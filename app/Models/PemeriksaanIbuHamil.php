<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanIbuHamil extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_ibu_hamil';

    protected $fillable = [
        'ibu_hamil_id',
        'tanggal_pemeriksaan',
        'berat_badan',
        'tekanan_darah',
        'systolic_bp',
        'diastolic_bp',
        'blood_sugar',
        'body_temp',
        'heart_rate',
        'usia_kehamilan_minggu',
        'ai_risk_level',
        'ai_probability',
        'ai_probabilities',
        'ai_model_version',
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
            'berat_badan' => 'decimal:2',
            'systolic_bp' => 'integer',
            'diastolic_bp' => 'integer',
            'blood_sugar' => 'decimal:2',
            'body_temp' => 'decimal:2',
            'heart_rate' => 'integer',
            'usia_kehamilan_minggu' => 'integer',
            'ai_probability' => 'decimal:4',
            'ai_probabilities' => 'array',
            'kehadiran' => 'boolean',
        ];
    }

    public function ibuHamil(): BelongsTo
    {
        return $this->belongsTo(IbuHamil::class);
    }

    /**
     * Badge visual status AI Risk
     */
    public function getRiskBadgeAttribute(): string
    {
        $risk = strtolower($this->ai_risk_level ?? 'low');
        return match ($risk) {
            'high' => '<span class="status-pill danger">🔴 High Risk</span>',
            'medium' => '<span class="status-pill warning">🟡 Medium Risk</span>',
            default => '<span class="status-pill success">🟢 Low Risk</span>',
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
