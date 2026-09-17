<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING POSYANDU SMART AI SERVICES ===" . PHP_EOL;

$aiService = new \App\Services\AiPredictionService();

// 1. Test Balita Prediction
echo "\n--- 1. Testing Balita AI Screening ---" . PHP_EOL;
$balitaRes = $aiService->predictBalita([
    'umur_bulan' => 18,
    'jenis_kelamin' => 'L',
    'berat_badan' => 9.2,
    'tinggi_badan' => 76.5,
    'lingkar_kepala' => 46.5,
    'berat_lahir' => 3.1,
    'asi_eksklusif' => 1,
]);
echo "Status AI   : " . ($balitaRes['hasil_ai'] ?? 'N/A') . PHP_EOL;
echo "Probabilitas: " . round(($balitaRes['probability'] ?? 0) * 100) . "%" . PHP_EOL;
echo "Model Ver   : " . ($balitaRes['model_version'] ?? 'N/A') . PHP_EOL;
echo "Disclaimer  : " . ($balitaRes['disclaimer'] ?? 'N/A') . PHP_EOL;

// 2. Test Maternal Prediction
echo "\n--- 2. Testing Maternal AI Screening ---" . PHP_EOL;
$maternalRes = $aiService->predictMaternal([
    'age' => 28,
    'systolic_bp' => 120,
    'diastolic_bp' => 80,
    'blood_sugar' => 7.1,
    'body_temp' => 98.6,
    'heart_rate' => 78,
]);
echo "Risk Level  : " . ($maternalRes['risk_level'] ?? 'N/A') . PHP_EOL;
echo "Probabilitas: " . round(($maternalRes['probability'] ?? 0) * 100) . "%" . PHP_EOL;
echo "Model Ver   : " . ($maternalRes['model_version'] ?? 'N/A') . PHP_EOL;
echo "Disclaimer  : " . ($maternalRes['disclaimer'] ?? 'N/A') . PHP_EOL;

// 3. Test Longitudinal Monitoring Service
echo "\n--- 3. Testing Longitudinal Trend Calculation ---" . PHP_EOL;
$longService = new \App\Services\LongitudinalMonitoringService();
$balita = \App\Models\Balita::first();
if ($balita) {
    $trend = $longService->getBalitaTrend($balita);
    echo "Balita      : " . $balita->nama . PHP_EOL;
    echo "Tren Label  : " . $trend['trend_label'] . PHP_EOL;
    echo "Inspections : " . count($trend['inspections']) . " riwayat" . PHP_EOL;
}

$bumil = \App\Models\IbuHamil::first();
if ($bumil) {
    $trendM = $longService->getIbuHamilTrend($bumil);
    echo "Ibu Hamil   : " . $bumil->nama . PHP_EOL;
    echo "Tren Label  : " . $trendM['trend_label'] . PHP_EOL;
    echo "Inspections : " . count($trendM['inspections']) . " riwayat" . PHP_EOL;
}

echo "\n=== ALL SERVICES OPERATIONAL ===" . PHP_EOL;
