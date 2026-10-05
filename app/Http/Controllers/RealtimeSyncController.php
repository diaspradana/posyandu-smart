<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\JadwalPosyandu;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\Tapos;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RealtimeSyncController extends Controller
{
    /**
     * Endpoint Sinkronisasi Real-time Data Posyandu Smart
     */
    public function sync(Request $request): JsonResponse
    {
        $user = Auth::user();
        $lastSync = (int)$request->get('last_sync', 0);
        $context = $request->get('context', 'general');
        $taposId = $request->get('tapos_id');

        // Resolve active tapos
        if (!$taposId && $user && $user->isKader()) {
            $taposId = $user->tapos_id;
        }

        $now = now();
        $currentTimestamp = $now->timestamp;

        // Calculate latest update timestamp across database models
        $latestBalitaExam = PemeriksaanBalita::max('updated_at');
        $latestBumilExam = PemeriksaanIbuHamil::max('updated_at');
        $latestBalita = Balita::max('updated_at');
        $latestBumil = IbuHamil::max('updated_at');
        $latestJadwal = JadwalPosyandu::max('updated_at');

        $timestamps = array_filter([
            $latestBalitaExam ? Carbon::parse($latestBalitaExam)->timestamp : 0,
            $latestBumilExam ? Carbon::parse($latestBumilExam)->timestamp : 0,
            $latestBalita ? Carbon::parse($latestBalita)->timestamp : 0,
            $latestBumil ? Carbon::parse($latestBumil)->timestamp : 0,
            $latestJadwal ? Carbon::parse($latestJadwal)->timestamp : 0,
        ]);

        $latestDbUpdate = count($timestamps) > 0 ? max($timestamps) : 0;
        $hasUpdates = ($lastSync > 0) ? ($latestDbUpdate > $lastSync) : true;

        // 1. Admin Aggregated Statistics
        $puskesmasId = $user ? $user->puskesmas_id : 1;
        $taposList = Tapos::when($puskesmasId, fn($q) => $q->where('puskesmas_id', $puskesmasId))->get();
        $taposIds = $taposList->pluck('id');

        $totalTapos = $taposList->where('status', 'aktif')->count();
        $totalBalita = Balita::whereIn('tapos_id', $taposIds)->where('status', 'aktif')->count();
        $totalIbuHamil = IbuHamil::whereIn('tapos_id', $taposIds)->where('status', 'aktif')->count();

        // Balita Stats
        $balitas = Balita::whereIn('tapos_id', $taposIds)->where('status', 'aktif')->get();
        $balitaNormalCount = 0;
        $balitaPemantauanCount = 0;
        $balitaStuntingCount = 0;
        $balitaBelumDiperiksaCount = 0;
        $imunisasiTertunda = 0;

        foreach ($balitas as $balita) {
            $latest = PemeriksaanBalita::where('balita_id', $balita->id)
                ->orderBy('tanggal_pemeriksaan', 'desc')
                ->first();

            if ($latest && $latest->kehadiran) {
                $status = $latest->hasil_ai ?? $latest->status_stunting;
                if ($status === 'normal') {
                    $balitaNormalCount++;
                } elseif ($status === 'pemantauan') {
                    $balitaPemantauanCount++;
                } elseif ($status === 'risiko_stunting' || $status === 'stunting') {
                    $balitaStuntingCount++;
                }

                if ($latest->status_imunisasi === 'tertunda' || $latest->status_imunisasi === 'belum_lengkap') {
                    $imunisasiTertunda++;
                }
            } else {
                $balitaBelumDiperiksaCount++;
            }
        }

        // Ibu Hamil Stats
        $ibuHamils = IbuHamil::whereIn('tapos_id', $taposIds)->where('status', 'aktif')->get();
        $ibuHamilLowCount = 0;
        $ibuHamilMediumCount = 0;
        $ibuHamilHighCount = 0;
        $ibuHamilBelumDiperiksaCount = 0;

        foreach ($ibuHamils as $bumil) {
            $latest = PemeriksaanIbuHamil::where('ibu_hamil_id', $bumil->id)
                ->orderBy('tanggal_pemeriksaan', 'desc')
                ->first();

            if ($latest && $latest->kehadiran) {
                $risk = strtolower($latest->ai_risk_level ?? 'low');
                if ($risk === 'high') {
                    $ibuHamilHighCount++;
                } elseif ($risk === 'medium') {
                    $ibuHamilMediumCount++;
                } else {
                    $ibuHamilLowCount++;
                }
            } else {
                $ibuHamilBelumDiperiksaCount++;
            }
        }

        // Pending Validation Count
        $pendingBalitaCount = PemeriksaanBalita::where('status_validasi', 'pending')->count();
        $pendingBumilCount = PemeriksaanIbuHamil::where('status_validasi', 'pending')->count();
        $pendingValidationCount = $pendingBalitaCount + $pendingBumilCount;

        // Monitoring Tapos Array
        $monitoringTapos = [];
        $taposRisikoAlerts = [];

        foreach ($taposList as $t) {
            $tBalitaIds = Balita::where('tapos_id', $t->id)->where('status', 'aktif')->pluck('id');
            $tBumilIds = IbuHamil::where('tapos_id', $t->id)->where('status', 'aktif')->pluck('id');

            $tBalitaCount = $tBalitaIds->count();
            $tBumilCount = $tBumilIds->count();

            $tStuntingRisk = 0;
            $tPemantauan = 0;
            foreach ($tBalitaIds as $bId) {
                $latest = PemeriksaanBalita::where('balita_id', $bId)->orderBy('tanggal_pemeriksaan', 'desc')->first();
                if ($latest && $latest->kehadiran) {
                    $st = $latest->hasil_ai ?? $latest->status_stunting;
                    if ($st === 'risiko_stunting' || $st === 'stunting') {
                        $tStuntingRisk++;
                    } elseif ($st === 'pemantauan') {
                        $tPemantauan++;
                    }
                }
            }

            $tHighRiskBumil = 0;
            foreach ($tBumilIds as $bmId) {
                $latest = PemeriksaanIbuHamil::where('ibu_hamil_id', $bmId)->orderBy('tanggal_pemeriksaan', 'desc')->first();
                if ($latest && $latest->kehadiran && strtolower($latest->ai_risk_level ?? '') === 'high') {
                    $tHighRiskBumil++;
                }
            }

            // Attendance
            $bChecks = PemeriksaanBalita::whereIn('balita_id', $tBalitaIds)->get();
            $iChecks = PemeriksaanIbuHamil::whereIn('ibu_hamil_id', $tBumilIds)->get();
            $totChecks = $bChecks->count() + $iChecks->count();
            $prsChecks = $bChecks->where('kehadiran', true)->count() + $iChecks->where('kehadiran', true)->count();
            $attPct = ($totChecks > 0) ? round(($prsChecks / $totChecks) * 100) : 85;

            if ($tStuntingRisk >= 2 || $tHighRiskBumil >= 2 || $attPct < 70) {
                $badge = 'red';
                $dot = '🔴';
                $label = 'Perhatian Khusus';
            } elseif ($tStuntingRisk >= 1 || $tPemantauan >= 2 || $tHighRiskBumil >= 1 || $attPct < 85) {
                $badge = 'yellow';
                $dot = '🟡';
                $label = 'Pemantauan';
            } else {
                $badge = 'green';
                $dot = '🟢';
                $label = 'Kondisi Baik';
            }

            $monitoringTapos[] = [
                'id' => $t->id,
                'nama' => $t->nama,
                'kode' => $t->kode,
                'balita' => $tBalitaCount,
                'bumil' => $tBumilCount,
                'stunting_risk' => $tStuntingRisk,
                'pemantauan' => $tPemantauan,
                'high_risk_bumil' => $tHighRiskBumil,
                'kehadiran' => $attPct,
                'status_badge' => $badge,
                'status_dot' => $dot,
                'status_label' => $label,
            ];

            if ($tStuntingRisk > 0 || $tHighRiskBumil > 0) {
                $taposRisikoAlerts[] = [
                    'nama' => $t->nama,
                    'stunting_count' => $tStuntingRisk,
                    'high_risk_bumil' => $tHighRiskBumil,
                ];
            }
        }

        // 2. Kader Specific Stats (if tapos_id is present)
        $kaderStats = null;
        if ($taposId) {
            $tBalitaIds = Balita::where('tapos_id', $taposId)->where('status', 'aktif')->pluck('id');
            $tBumilIds = IbuHamil::where('tapos_id', $taposId)->where('status', 'aktif')->pluck('id');

            $kBStunting = 0;
            $kBPemantauan = 0;
            foreach ($tBalitaIds as $bId) {
                $latest = PemeriksaanBalita::where('balita_id', $bId)->orderBy('tanggal_pemeriksaan', 'desc')->first();
                if ($latest && $latest->kehadiran) {
                    $st = $latest->hasil_ai ?? $latest->status_stunting;
                    if ($st === 'risiko_stunting' || $st === 'stunting') $kBStunting++;
                    elseif ($st === 'pemantauan') $kBPemantauan++;
                }
            }

            $kIHigh = 0;
            $kIMedium = 0;
            foreach ($tBumilIds as $bmId) {
                $latest = PemeriksaanIbuHamil::where('ibu_hamil_id', $bmId)->orderBy('tanggal_pemeriksaan', 'desc')->first();
                if ($latest && $latest->kehadiran) {
                    $risk = strtolower($latest->ai_risk_level ?? 'low');
                    if ($risk === 'high') $kIHigh++;
                    elseif ($risk === 'medium') $kIMedium++;
                }
            }

            $kaderStats = [
                'balita_count' => $tBalitaIds->count(),
                'ibu_hamil_count' => $tBumilIds->count(),
                'risiko_stunting_count' => $kBStunting,
                'pemantauan_balita_count' => $kBPemantauan,
                'high_risk_ibu_hamil_count' => $kIHigh,
                'medium_risk_ibu_hamil_count' => $kIMedium,
                'total_follow_up' => $kBStunting + $kBPemantauan + $kIHigh + $kIMedium,
            ];
        }

        // 3. Recent Realtime Activity Feed & Toast Messages
        $recentBalitaExams = PemeriksaanBalita::with('balita.tapos')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'balita',
                    'nama' => $item->balita->nama ?? 'Balita',
                    'tapos' => $item->balita->tapos->nama ?? '-',
                    'tanggal' => $item->tanggal_pemeriksaan ? $item->tanggal_pemeriksaan->format('d/m/Y') : '-',
                    'ai_status' => $item->hasil_ai ?? $item->status_stunting ?? 'normal',
                    'status_validasi' => $item->status_validasi ?? 'pending',
                    'updated_at' => $item->updated_at->toIso8601String(),
                    'updated_at_human' => $item->updated_at->diffForHumans(),
                ];
            });

        $recentBumilExams = PemeriksaanIbuHamil::with('ibuHamil.tapos')
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'ibu_hamil',
                    'nama' => $item->ibuHamil->nama ?? 'Ibu Hamil',
                    'tapos' => $item->ibuHamil->tapos->nama ?? '-',
                    'tanggal' => $item->tanggal_pemeriksaan ? $item->tanggal_pemeriksaan->format('d/m/Y') : '-',
                    'ai_risk' => $item->ai_risk_level ?? 'Low',
                    'status_validasi' => $item->status_validasi ?? 'pending',
                    'updated_at' => $item->updated_at->toIso8601String(),
                    'updated_at_human' => $item->updated_at->diffForHumans(),
                ];
            });

        // Check if there are newly created/updated items in the last 15 seconds
        $newNotifications = [];
        if ($lastSync > 0) {
            $newBalitas = PemeriksaanBalita::with('balita.tapos')
                ->where('updated_at', '>', Carbon::createFromTimestamp($lastSync))
                ->latest('updated_at')
                ->take(3)
                ->get();

            foreach ($newBalitas as $nb) {
                $statusText = strtoupper($nb->hasil_ai ?? $nb->status_stunting ?? 'NORMAL');
                $taposName = $nb->balita->tapos->nama ?? 'Posyandu';
                $valStatus = $nb->status_validasi === 'validated' ? 'Disetujui Nakes' : ($nb->status_validasi === 'rejected' ? 'Ditolak' : 'Menunggu Validasi');
                $newNotifications[] = [
                    'title' => "Pemeriksaan Balita: {$nb->balita->nama}",
                    'message' => "Posyandu {$taposName} | AI: {$statusText} | Status: {$valStatus}",
                    'type' => in_array($statusText, ['RISIKO_STUNTING', 'STUNTING']) ? 'danger' : 'info',
                    'time' => $nb->updated_at->format('H:i:s'),
                ];
            }

            $newBumils = PemeriksaanIbuHamil::with('ibuHamil.tapos')
                ->where('updated_at', '>', Carbon::createFromTimestamp($lastSync))
                ->latest('updated_at')
                ->take(3)
                ->get();

            foreach ($newBumils as $nbm) {
                $riskText = strtoupper($nbm->ai_risk_level ?? 'LOW');
                $taposName = $nbm->ibuHamil->tapos->nama ?? 'Posyandu';
                $valStatus = $nbm->status_validasi === 'validated' ? 'Disetujui Nakes' : ($nbm->status_validasi === 'rejected' ? 'Ditolak' : 'Menunggu Validasi');
                $newNotifications[] = [
                    'title' => "Pemeriksaan Ibu Hamil: {$nbm->ibuHamil->nama}",
                    'message' => "Posyandu {$taposName} | AI Risk: {$riskText} | Status: {$valStatus}",
                    'type' => ($riskText === 'HIGH') ? 'danger' : 'info',
                    'time' => $nbm->updated_at->format('H:i:s'),
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'server_time' => $currentTimestamp,
            'latest_db_update' => $latestDbUpdate,
            'has_updates' => $hasUpdates,
            'admin_stats' => [
                'total_tapos' => $totalTapos,
                'total_balita' => $totalBalita,
                'total_ibu_hamil' => $totalIbuHamil,
                'balita_stunting_count' => $balitaStuntingCount,
                'balita_pemantauan_count' => $balitaPemantauanCount,
                'balita_normal_count' => $balitaNormalCount,
                'balita_belum_diperiksa_count' => $balitaBelumDiperiksaCount,
                'ibu_hamil_high_count' => $ibuHamilHighCount,
                'ibu_hamil_medium_count' => $ibuHamilMediumCount,
                'ibu_hamil_low_count' => $ibuHamilLowCount,
                'ibu_hamil_belum_diperiksa_count' => $ibuHamilBelumDiperiksaCount,
                'imunisasi_tertunda' => $imunisasiTertunda,
                'pending_validation_count' => $pendingValidationCount,
                'pending_balita_count' => $pendingBalitaCount,
                'pending_bumil_count' => $pendingBumilCount,
                'monitoring_tapos' => $monitoringTapos,
                'tapos_risiko_alerts' => $taposRisikoAlerts,
            ],
            'kader_stats' => $kaderStats,
            'recent_balita_exams' => $recentBalitaExams,
            'recent_bumil_exams' => $recentBumilExams,
            'notifications' => $newNotifications,
        ]);
    }
}
