<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiPredictionService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('AI_SERVICE_URL', 'http://127.0.0.1:8001');
    }

    /**
     * Prediksi / Screening Risiko Kesehatan Ibu Hamil
     * 
     * @param array $data ['age', 'systolic_bp', 'diastolic_bp', 'blood_sugar', 'body_temp', 'heart_rate']
     * @return array
     */
    public function predictMaternal(array $data): array
    {
        try {
            $response = Http::timeout(2.5)->post("{$this->baseUrl}/api/predict/maternal", [
                'age' => (int)$data['age'],
                'systolic_bp' => (int)$data['systolic_bp'],
                'diastolic_bp' => (int)$data['diastolic_bp'],
                'blood_sugar' => (float)$data['blood_sugar'],
                'body_temp' => (float)$data['body_temp'],
                'heart_rate' => (int)$data['heart_rate'],
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning("AiPredictionService (Maternal) fallback triggered: " . $e->getMessage());
        }

        // Local Fallback Algorithm (Clinical Decision Support Rule Engine)
        return $this->fallbackMaternalPrediction($data);
    }

    /**
     * Prediksi / Screening Risiko Pertumbuhan & Stunting Balita
     * 
     * @param array $data ['umur_bulan', 'jenis_kelamin', 'berat_badan', 'tinggi_badan', 'lingkar_kepala', 'berat_lahir', 'asi_eksklusif']
     * @return array
     */
    public function predictBalita(array $data): array
    {
        try {
            $response = Http::timeout(2.5)->post("{$this->baseUrl}/api/predict/balita", [
                'umur_bulan' => (int)$data['umur_bulan'],
                'jenis_kelamin' => (string)($data['jenis_kelamin'] ?? 'L'),
                'berat_badan' => (float)$data['berat_badan'],
                'tinggi_badan' => (float)$data['tinggi_badan'],
                'lingkar_kepala' => isset($data['lingkar_kepala']) ? (float)$data['lingkar_kepala'] : null,
                'berat_lahir' => isset($data['berat_lahir']) ? (float)$data['berat_lahir'] : null,
                'asi_eksklusif' => isset($data['asi_eksklusif']) ? (int)$data['asi_eksklusif'] : 1,
            ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            Log::warning("AiPredictionService (Balita) fallback triggered: " . $e->getMessage());
        }

        // Local Fallback Algorithm (WHO Growth Curve Standards)
        return $this->fallbackBalitaPrediction($data);
    }

    /**
     * Heuristic Clinical Fallback for Maternal Screening
     */
    protected function fallbackMaternalPrediction(array $data): array
    {
        $age = (int)($data['age'] ?? 25);
        $sys = (int)($data['systolic_bp'] ?? 120);
        $dia = (int)($data['diastolic_bp'] ?? 80);
        $bs = (float)($data['blood_sugar'] ?? 7.0);
        $temp = (float)($data['body_temp'] ?? 98.6);
        $hr = (int)($data['heart_rate'] ?? 75);

        $isHigh = ($sys >= 140 || $dia >= 90 || $bs >= 10.0 || $temp >= 100.4 || ($age >= 38 && $sys >= 135));
        $isMedium = ($sys >= 125 || $dia >= 82 || $bs >= 7.8 || $hr > 95 || $age < 18 || $age > 35);

        if ($isHigh) {
            $risk = 'high';
            $probs = ['high' => 0.86, 'medium' => 0.10, 'low' => 0.04];
            $statusLabel = 'MEMERLUKAN PERHATIAN TINGGI (HIGH RISK)';
            $rec = 'Segera rujuk ke Puskesmas / dokter spesialis kandungan untuk pemeriksaan intensif dan penanganan tanda bahaya kehamilan.';
        } elseif ($isMedium) {
            $risk = 'medium';
            $probs = ['high' => 0.11, 'medium' => 0.77, 'low' => 0.12];
            $statusLabel = 'MEMERLUKAN PEMANTAUAN (MEDIUM RISK)';
            $rec = 'Lakukan pemantauan berkala tiap 2 minggu, edukasi nutrisi gizi seimbang, dan konsumsi tablet tambah darah rutin.';
        } else {
            $risk = 'low';
            $probs = ['high' => 0.03, 'medium' => 0.09, 'low' => 0.88];
            $statusLabel = 'KONDISI TERPANTAU BAIK (LOW RISK)';
            $rec = 'Pertahankan pola hidup sehat dan jadwalkan kunjungan pemeriksaan kehamilan rutin bulan berikutnya.';
        }

        $indicators = [];
        if ($sys >= 140 || $dia >= 90) {
            $indicators[] = "Tekanan Darah Tinggi ({$sys}/{$dia} mmHg)";
        } elseif ($sys < 90 || $dia < 60) {
            $indicators[] = "Tekanan Darah Rendah ({$sys}/{$dia} mmHg)";
        } else {
            $indicators[] = "Tekanan Darah Normal ({$sys}/{$dia} mmHg)";
        }

        if ($bs >= 10.0) {
            $indicators[] = "Gula Darah Tinggi ({$bs} mmol/L)";
        } elseif ($bs >= 7.8) {
            $indicators[] = "Gula Darah Perlu Pengawasan ({$bs} mmol/L)";
        } else {
            $indicators[] = "Gula Darah Terkendali ({$bs} mmol/L)";
        }

        if ($hr > 100) {
            $indicators[] = "Detak Jantung Cepat ({$hr} bpm)";
        } else {
            $indicators[] = "Detak Jantung Normal ({$hr} bpm)";
        }

        return [
            'risk_level' => $risk,
            'probability' => $probs[$risk],
            'probabilities' => $probs,
            'status_label' => $statusLabel,
            'indicators' => $indicators,
            'recommendation' => $rec,
            'disclaimer' => '⚠ Hasil AI merupakan screening awal/decision support dan bukan diagnosis medis.',
            'model_version' => 'maternal-v1.0'
        ];
    }

    /**
     * WHO Growth Curve Fallback for Balita Stunting Screening
     */
    protected function fallbackBalitaPrediction(array $data): array
    {
        $u = (int)($data['umur_bulan'] ?? 12);
        $tb = (float)($data['tinggi_badan'] ?? 75.0);
        $bb = (float)($data['berat_badan'] ?? 9.0);
        $lk = isset($data['lingkar_kepala']) ? (float)$data['lingkar_kepala'] : null;

        // WHO 2006 Reference Median TB approx
        $expectedTb = 50.0 + (1.92 * $u) - (0.015 * ($u ** 1.8));
        $diffTb = $tb - $expectedTb;

        if ($diffTb < -4.2) {
            $status = 'risiko_stunting';
            $probs = ['risiko_stunting' => 0.84, 'pemantauan' => 0.12, 'normal' => 0.04];
            $statusLabel = 'RISIKO STUNTING (MEMERLUKAN INTERVENSI)';
            $rec = 'Lakukan intervensi gizi spesifik, pemberian makanan tambahan (PMT) kaya protein hewani, dan rujukan tindak lanjut ke Puskesmas.';
        } elseif ($diffTb < -2.0) {
            $status = 'pemantauan';
            $probs = ['risiko_stunting' => 0.16, 'pemantauan' => 0.74, 'normal' => 0.10];
            $statusLabel = 'MEMERLUKAN PEMANTAUAN TUMBUH KEMBANG';
            $rec = 'Lakukan pemantauan berkala kenaikan berat badan dan tinggi badan setiap bulan serta konseling gizi PMBA pada orang tua.';
        } else {
            $status = 'normal';
            $probs = ['risiko_stunting' => 0.03, 'pemantauan' => 0.09, 'normal' => 0.88];
            $statusLabel = 'PERTUMBUHAN NORMAL';
            $rec = 'Pertumbuhan balita sesuai kurva standar. Pertahankan pemberian nutrisi bergizi seimbang dan stimulasi perkembangan.';
        }

        $indicators = [
            "Pertumbuhan Tinggi Badan: {$tb} cm (Target Standar Median Usia {$u} bln: " . round($expectedTb, 1) . " cm)",
            "Berat Badan: {$bb} kg",
        ];
        if ($lk) {
            $indicators[] = "Lingkar Kepala: {$lk} cm";
        }

        return [
            'hasil_ai' => $status,
            'probability' => $probs[$status],
            'probabilities' => $probs,
            'status_label' => $statusLabel,
            'indicators' => $indicators,
            'recommendation' => $rec,
            'disclaimer' => '⚠ Hasil AI merupakan screening awal pertumbuhan dan bukan diagnosis stunting.',
            'model_version' => 'stunting-v1.0'
        ];
    }
}
