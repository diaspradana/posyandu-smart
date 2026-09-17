<?php

namespace App\Services;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use Illuminate\Support\Collection;

class LongitudinalMonitoringService
{
    /**
     * Hitung tren longitudinal riwayat pemeriksaan Balita
     */
    public function getBalitaTrend(Balita $balita, int $limit = 6): array
    {
        $inspections = PemeriksaanBalita::where('balita_id', $balita->id)
            ->where('kehadiran', true)
            ->orderBy('tanggal_pemeriksaan', 'asc')
            ->take($limit)
            ->get();

        if ($inspections->isEmpty()) {
            return [
                'has_data' => false,
                'trend_label' => 'Belum Diperiksa',
                'trend_badge' => '<span class="status-pill secondary">⚪ Belum Diperiksa</span>',
                'sequence_html' => '<span class="text-muted">Tidak ada riwayat</span>',
                'inspections' => collect([]),
                'direction' => 'neutral',
            ];
        }

        $scores = [];
        $pills = [];
        $weights = ['normal' => 1, 'pemantauan' => 2, 'risiko_stunting' => 3, 'stunting' => 3];

        foreach ($inspections as $ins) {
            $st = strtolower($ins->hasil_ai ?? $ins->status_stunting ?? 'normal');
            $scores[] = $weights[$st] ?? 1;

            if ($st === 'risiko_stunting' || $st === 'stunting') {
                $pills[] = '<span class="status-dot danger" title="' . $ins->tanggal_pemeriksaan->format('d/m/Y') . ' - Risiko">🔴</span>';
            } elseif ($st === 'pemantauan') {
                $pills[] = '<span class="status-dot warning" title="' . $ins->tanggal_pemeriksaan->format('d/m/Y') . ' - Pemantauan">🟡</span>';
            } else {
                $pills[] = '<span class="status-dot success" title="' . $ins->tanggal_pemeriksaan->format('d/m/Y') . ' - Normal">🟢</span>';
            }
        }

        $sequenceHtml = implode(' <span class="trend-arrow">→</span> ', $pills);

        // Calculate direction
        $firstScore = reset($scores);
        $lastScore = end($scores);

        if (count($scores) === 1) {
            $direction = 'single';
            $trendLabel = 'Pemeriksaan Perdana';
            $trendBadge = '<span class="status-pill info">ℹ️ Data Pertama</span>';
        } elseif ($firstScore > $lastScore) {
            $direction = 'improving';
            $trendLabel = 'Pertumbuhan Membaik (Risiko Menurun)';
            $trendBadge = '<span class="status-pill success">📈 Membaik</span>';
        } elseif ($firstScore < $lastScore) {
            $direction = 'worsening';
            $trendLabel = 'Perlu Intervensi (Risiko Meningkat)';
            $trendBadge = '<span class="status-pill danger">📉 Perlu Intervensi</span>';
        } else {
            $direction = 'stable';
            $trendLabel = $lastScore >= 2 ? 'Kondisi Perlu Pemantauan Berkelanjutan' : 'Pertumbuhan Stabil Normal';
            $trendBadge = $lastScore >= 2 
                ? '<span class="status-pill warning">⏳ Pemantauan Berlanjut</span>' 
                : '<span class="status-pill success">✓ Stabil Normal</span>';
        }

        return [
            'has_data' => true,
            'direction' => $direction,
            'trend_label' => $trendLabel,
            'trend_badge' => $trendBadge,
            'sequence_html' => $sequenceHtml,
            'latest' => $inspections->last(),
            'inspections' => $inspections,
        ];
    }

    /**
     * Hitung tren longitudinal riwayat pemeriksaan Ibu Hamil
     */
    public function getIbuHamilTrend(IbuHamil $ibuHamil, int $limit = 6): array
    {
        $inspections = PemeriksaanIbuHamil::where('ibu_hamil_id', $ibuHamil->id)
            ->where('kehadiran', true)
            ->orderBy('tanggal_pemeriksaan', 'asc')
            ->take($limit)
            ->get();

        if ($inspections->isEmpty()) {
            return [
                'has_data' => false,
                'trend_label' => 'Belum Diperiksa',
                'trend_badge' => '<span class="status-pill secondary">⚪ Belum Diperiksa</span>',
                'sequence_html' => '<span class="text-muted">Tidak ada riwayat</span>',
                'inspections' => collect([]),
                'direction' => 'neutral',
            ];
        }

        $scores = [];
        $pills = [];
        $weights = ['low' => 1, 'medium' => 2, 'high' => 3];

        foreach ($inspections as $ins) {
            $risk = strtolower($ins->ai_risk_level ?? 'low');
            $scores[] = $weights[$risk] ?? 1;

            if ($risk === 'high') {
                $pills[] = '<span class="status-dot danger" title="' . $ins->tanggal_pemeriksaan->format('d/m/Y') . ' - High Risk">🔴</span>';
            } elseif ($risk === 'medium') {
                $pills[] = '<span class="status-dot warning" title="' . $ins->tanggal_pemeriksaan->format('d/m/Y') . ' - Medium Risk">🟡</span>';
            } else {
                $pills[] = '<span class="status-dot success" title="' . $ins->tanggal_pemeriksaan->format('d/m/Y') . ' - Low Risk">🟢</span>';
            }
        }

        $sequenceHtml = implode(' <span class="trend-arrow">→</span> ', $pills);

        $firstScore = reset($scores);
        $lastScore = end($scores);

        if (count($scores) === 1) {
            $direction = 'single';
            $trendLabel = 'Pemeriksaan Perdana';
            $trendBadge = '<span class="status-pill info">ℹ️ Data Pertama</span>';
        } elseif ($firstScore > $lastScore) {
            $direction = 'improving';
            $trendLabel = 'Kondisi Kesehatan Membaik (Risiko Menurun)';
            $trendBadge = '<span class="status-pill success">📈 Membaik</span>';
        } elseif ($firstScore < $lastScore) {
            $direction = 'worsening';
            $trendLabel = 'Peningkatan Risiko Maternal (Perhatian Khusus)';
            $trendBadge = '<span class="status-pill danger">📉 Risiko Meningkat</span>';
        } else {
            $direction = 'stable';
            $trendLabel = $lastScore >= 2 ? 'Kondisi Risiko Terpantau Stabil' : 'Kondisi Kehamilan Stabil Baik';
            $trendBadge = $lastScore >= 2 
                ? '<span class="status-pill warning">⏳ Terpantau Stabil</span>' 
                : '<span class="status-pill success">✓ Stabil Baik</span>';
        }

        return [
            'has_data' => true,
            'direction' => $direction,
            'trend_label' => $trendLabel,
            'trend_badge' => $trendBadge,
            'sequence_html' => $sequenceHtml,
            'latest' => $inspections->last(),
            'inspections' => $inspections,
        ];
    }
}
